# AGENTS_LOOP — Continue Milestone

Reusable loop prompt for advancing Dev Theory Coach (THEORY_CORE) through **all** milestone queues: P0 → post-P0 → later → advanced, until every ordered milestone is `done`.

Companions: [`.agents/state.json`](./.agents/state.json) · [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`EPIC_MAP.md`](./EPIC_MAP.md) · [`RESOURCES.md`](./RESOURCES.md)

---

## How to run

**Preferred (dynamic loop — agent self-paces after each tick):**

```text
/loop continue milestones using AGENTS_LOOP-Continue-Milestone.md
```

**Fixed interval (only if you want heartbeat ticks while idle):**

```text
/loop 5m continue milestones using AGENTS_LOOP-Continue-Milestone.md
```

Prefer **no interval** so each wake continues the next story.

**Before starting**
1. Confirm `.agents/state.json` points at the story you want (`current_milestone_id`, `next_action`).
2. Leave any running dev server alone; the loop verifies with `./vendor/bin/phpunit`.
3. Stop the loop yourself when you want. The agent hard-stops on the error budget or human-verification cases below.

**On each tick the agent should**
- Read this file + `.agents/state.json` + the current milestone in `IMPLEMENTATION_STORIES.md`
- Implement one story, auto-verify, update state, continue
- Create/adapt skills under `.agents/skills/*` when work repeats

---

## Milestone queues (in order)

Read from `.agents/state.json` → `milestones`:

| Queue key | When to enter | Contents |
|-----------|---------------|----------|
| `implementation_order` | First | P0 through MVP freeze |
| `post_mvp_order` | After MVP freeze checklist is green (or human GO) | Volume + remaining P0 types depth |
| `later_order` | After `post_mvp_order` is fully done | <!-- agent: later epics --> |
| `advanced_order` | After `later_order` is fully done | <!-- agent: advanced epics --> |

**Never skip a queue.** Finish every id in the current queue before starting the next.

---

## Loop prompt

```markdown
# OBJECTIVE
Advance Dev Theory Coach (THEORY_CORE) through **every** milestone queue until global Done.

Work queues (strict order), from `.agents/state.json` → `milestones`:
1. `implementation_order` (P0 → MVP freeze)
2. `post_mvp_order` (only after MVP freeze checklist green, or human GO recorded)
3. `later_order`
4. `advanced_order`

**Done (global):** Every milestone id in all queues is complete, with stories meeting acceptance in `IMPLEMENTATION_STORIES.md`.

**Done (per tick):** Exactly one story advanced to acceptance-pass, or one error-recovery round completed with a clear next action.

# CONTEXT
- State of record: `.agents/state.json` (`current_epic_id`, `current_milestone_id`, `next_action`, `status`)
- Story + acceptance source: `IMPLEMENTATION_STORIES.md`
- Product map / constraints: `EPIC_MAP.md`, `RESOURCES.md`, `.impeccable.md`
- Ideas only: `context/` (rewrite; never paste)
- Execution rules: one milestone `in_progress` at a time; follow the active queue in order; do not invent milestones outside the queues
- Auto-verify: `./vendor/bin/phpunit`
- Local skills: `.agents/skills/*` (create/adapt when a task repeats)

# STEP-BY-STEP CADENCE
1. **Orient**
   - Read `.agents/state.json` and the current milestone section in `IMPLEMENTATION_STORIES.md`.
   - Pick the active queue: first queue with any unfinished id (respect the MVP freeze gate). None → Done(global) → STOP.
   - Identify the single next unfinished story. Do not skip ahead inside a queue.

2. **Implement the current story only**
   - Minimal changes that satisfy that story's Acceptance cell.
   - Prefer extending registries / existing patterns.
   - Deterministic coaching graphs only; no runtime LLM; SAMPLE data; `localStorage` key `theory_core_progress_v1`.
   - <!-- agent: per-epic implementation notes, one line each, e.g. "For E6: drill engine reads data/drills.json; timed; thought-chain reveal; no calculator chrome." -->
   - STOP (human verification) only if a story truly requires a credential, hardware, or paid API the agent cannot mock.

3. **Automatic verification (required before marking progress)**
   - Run `./vendor/bin/phpunit`.
   - Check the story Acceptance + global conventions (progress key stable; SAMPLE labels; no 404 on missing coach; Beginner unlock order unchanged).

4. **On PASS**
   - Update `.agents/state.json`: story/milestone progress, `next_action`, `last_updated_iso`, status fields.
   - Update Status columns in `IMPLEMENTATION_STORIES.md` when a milestone flips.
   - Continue to the next story in the same milestone; when the milestone is complete, GO to the next id in the **active queue** (GO is automatic if auto-verify passed).
   - When a queue finishes, enter the next queue automatically.
   - Do **not** pause for human review unless a stop condition applies.

5. **On FAIL (verify/acceptance)**
   - Enter **error-fix mode**. Read stderr / type errors; patch minimally; re-run verify.
   - Count every failed verify → fix → re-verify cycle as one round, including new errors introduced by a fix.

6. **Skills (efficiency / self-heal)**
   - If the same workflow repeats (add coaching session, add pack, wire writeback, GO milestone + state update), create or update `.agents/skills/<skill-name>/SKILL.md`.
   - On failure, try a plausible fix, then rewrite the skill once the approach works.
   - Reused for a different purpose → new skill, do not overload.

# VERIFICATION RUBRIC
Return **PASS** for the current story only if ALL are true:
- [ ] Story Acceptance in `IMPLEMENTATION_STORIES.md` is met
- [ ] `./vendor/bin/phpunit` exits 0
- [ ] No scope creep beyond the current story (unless required to unblock acceptance)
- [ ] `.agents/state.json` reflects the new truth (`next_action` points at the real next story)

Return **CONTINUE** after PASS (start next story same tick if time remains; otherwise end turn ready for next wake).

Return **STOP** only under Stop Conditions (including Done(global)).

# STOP CONDITIONS / ESCAPE HATCHES
- **Maximum outer iterations:** Infinite. Human stops the process when desired.
- **Done(global):** All queues complete → STOP with status reflecting full completion.
- **Error budget (hard stop):** Same blocking failure (or its chain) not resolved within **10** fix rounds → STOP with an **Error Handoff Summary**:
  - Current `epic` / `milestone` / `story` ids
  - Original error + subsequent errors (chronological)
  - Files touched and approaches tried
  - Last command outputs (excerpts)
  - Suspected root cause and what a human should try next
  - Whether a skill was involved and what it claimed vs what failed
- **Human verification required (hard stop):** Only when automatic verification cannot decide:
  - Ambiguous product/UX choice not locked in `.agents/state.json` / `EPIC_MAP.md`
  - Acceptance explicitly requires human taste
  - Credential / secret / hardware / paid API decisions (prefer mocks first)
  - Conflicting instructions between docs
- **Abort immediately (hard stop):** About to change third-party dependency surface in a risky way (unrelated upgrades, removing deps "for cleanup") unless the current story requires it.
- **MVP freeze gate:** Do not enter `post_mvp_order` until the MVP freeze checklist in `IMPLEMENTATION_STORIES.md` is green; if it cannot be auto-confirmed, STOP for human GO. After that, later queues need no further GO.

# TICK OUTPUT (keep short)
1. `milestone/story` worked (+ queue)
2. PASS | FIXING (round n/10) | STOP
3. Commands run + exit status
4. `next_action` (copied from updated state)
5. Skills created/updated (paths only), if any
```
