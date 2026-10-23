# AGENTS_LOOP — Continue Milestone

Reusable loop prompt for advancing Dev Theory Coach (THEORY_CORE) through **all** milestone queues: P0 → post-P0 → later → advanced, until every ordered milestone is `done`.

Companions: [`.agents/state.json`](./.agents/state.json) · [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`EPIC_MAP.md`](./EPIC_MAP.md) · [`RESOURCES.md`](./RESOURCES.md) · [`gauntlet/contract.md`](./gauntlet/contract.md)

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
3. Stop the loop yourself when you want. The agent hard-stops on the error budget, the gauntlet budget, or human-verification cases below.
4. Do not start [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md) in the same session.

**On each tick the agent should**

- Read this file + `.agents/state.json` + the current milestone in `IMPLEMENTATION_STORIES.md` + the gauntlet contract
- Implement one story, auto-verify, and at a milestone boundary run a fresh critic
- Create or adapt skills under `.agents/skills/*` when work repeats

---

## Milestone queues (in order)

Read from `.agents/state.json` → `milestones`:

| Queue key | When to enter | Contents |
|-----------|---------------|----------|
| `implementation_order` | First | P0 through MVP freeze (E7.M1) |
| `post_mvp_order` | After MVP freeze checklist is green (or human GO) | Drill engine, case engine, track paths, prerequisite unlocks |
| `later_order` | After `post_mvp_order` is fully done | Cheatsheet runtime |
| `advanced_order` | After `later_order` is fully done | Locked-reason copy |

**Never skip a queue.** Finish every id in the current queue before starting the next. The content graph starts only after `advanced_order` is done.

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

**Done (global):** Every milestone id in all queues is complete, each with a gauntlet WIN, and stories meeting acceptance in `IMPLEMENTATION_STORIES.md`.

**Done (per tick):** Exactly one story advanced to acceptance-pass, or one gauntlet gap fixed, or one error-recovery round completed with a clear next action.

# CONTEXT
- State of record: `.agents/state.json` (`current_epic_id`, `current_milestone_id`, `next_action`, `status`)
- Story + acceptance source: `IMPLEMENTATION_STORIES.md`
- Product map / constraints: `EPIC_MAP.md`, `RESOURCES.md`, `.impeccable.md`
- Critic contract: `gauntlet/contract.md`, work order `gauntlet/workstreams.md`, log `gauntlet/state.json`
- Ideas only: `context/` (rewrite; never paste). Curricula win over clones. See `context/notes/syllabus-wins.md`
- Execution rules: one milestone `in_progress` at a time; follow the active queue in order; do not invent milestones outside the queues
- Auto-verify: `./vendor/bin/phpunit`
- Local skills: `.agents/skills/*` (create or adapt when a task repeats)
- Layout: `index.php` (front controller), `router.php` (`php -S` only), `src/`, `assets/js/progress.js`, `content/`, `tests/` as named in `IMPLEMENTATION_STORIES.md` global conventions. Mirror patterns from `../mental-math` and `../leetcode-coach`. Do not import their source

# STEP-BY-STEP CADENCE
1. **Orient**
   - Read `.agents/state.json` and the current milestone section in `IMPLEMENTATION_STORIES.md`.
   - If `gauntlet/state.json` has an open LOSE on this milestone, this tick fixes that gap only. Do not start a new story.
   - Pick the active queue: first queue with any unfinished id (respect the MVP freeze gate). None → Done(global) → STOP.
   - Identify the single next unfinished story. Do not skip ahead inside a queue.

2. **Implement the current story only**
   - Minimal changes that satisfy that story's Acceptance cell.
   - Prefer extending the catalog and the registries already named in the stories.
   - Deterministic coaching graphs only; no runtime LLM; SAMPLE data; `localStorage` key `theory_core_progress_v1`.
   - E1: `content/families.json` is the full 53-row catalog. Tests parse `RESOURCES.md` rather than a hand-copied second list.
   - E3: progress logic that tests can round-trip lives in `src/ProgressSchema.php`. The browser writer is `assets/js/progress.js` and must use the same key and fields.
   - E4: beginner guide bodies are original, 300–800 words, five parts (rule, why, SAMPLE example, use, refuse).
   - E5: coach JSON validates before the page renders it. Missing file → slot. The `arrays` tree chooses array vs list, with rewinds.
   - E6: quizzes are untimed MCQ. Four choices, a reason on every wrong choice. No code execution.
   - E2: path files only reference slugs that exist. P0 path does not require unauthored quizzes.
   - E7: the next CTA names a real slug. Path complete uses the credential rule in the story, not a percentage.
   - E8: drills exist only for `decisions.drill_families`. Other slugs get an explicit not-a-drill slot.
   - E9: consequences render after commit. The case gate can hide them.
   - E10: one screen, ≤12 lines.
   - E11: locked copy names the missing slug from `after`.
   - STOP (human verification) only if a story truly requires a credential, hardware, or paid API the agent cannot mock.

3. **Automatic verification (required before marking progress)**
   - Run `./vendor/bin/phpunit`.
   - Check the story Acceptance + global conventions (progress key stable; SAMPLE labels; no 404 on missing coach; Beginner unlock order unchanged; `queues` ≠ `work-queues`).

4. **On PASS of a story that is not the last in its milestone**
   - Update `.agents/state.json`: story progress, `next_action`, `last_updated_iso`, status fields.
   - Continue to the next story in the same milestone.
   - Do not run the critic mid-milestone.
   - Do not pause for human review unless a stop condition applies.

5. **On PASS of the last story in the milestone — gauntlet**
   - Do not mark the milestone `done` yet.
   - Start a fresh critic (new subagent, empty context). Give it only `gauntlet/contract.md`, the milestone id, that milestone's Acceptance cells, and the instruction to run the tests and read the routes. Do not pass the builder's notes.
   - `WIN`: mark the milestone done, append the verdict to `gauntlet/state.json`, set `next_action` to the next queue id. If this was E7.M1, the same critic pass must include the integration walk in the contract, or a second fresh critic does that walk before the freeze checklist is checked. If this was E11.M1, run the final integration critic before Done(global).
   - `LOSE`: record the round. Next tick fixes only the named gap. A new critic judges after the fix. This is not a new story.
   - `UNJUDGEABLE`: fix the inspection path (server, test command, route). Do not change product behavior to chase a guess.
   - Third `LOSE`, or an unfixable `UNJUDGEABLE`: write the gap under `open_gaps` and STOP. Leave the milestone `in_progress`.

6. **On FAIL (verify/acceptance)**
   - Enter **error-fix mode**. Read stderr / type errors; patch minimally; re-run verify.
   - Count every failed verify → fix → re-verify cycle as one round, including new errors introduced by a fix.
   - Gauntlet rounds and phpunit rounds are separate budgets.

7. **Skills (efficiency / self-heal)**
   - If the same workflow repeats (add coaching session, add quiz, add drill, add case, add cheatsheet, GO milestone + state update), create or update `.agents/skills/<skill-name>/SKILL.md`.
   - On failure, try a plausible fix, then rewrite the skill once the approach works.
   - Reused for a different purpose → new skill, do not overload.
   - Stories E5.M2.S2, E8.M1.S4, E9.M1.S4, and E10.M1.S3 require the matching skill's `TODO` paths to be replaced with real ones.

# VERIFICATION RUBRIC
Return **PASS** for the current story only if ALL are true:
- [ ] Story Acceptance in `IMPLEMENTATION_STORIES.md` is met
- [ ] `./vendor/bin/phpunit` exits 0
- [ ] No scope creep beyond the current story (unless required to unblock acceptance)
- [ ] `.agents/state.json` reflects the new truth (`next_action` points at the real next story)
- [ ] If this tick closes a milestone, `gauntlet/state.json` contains a WIN for that id from a fresh critic

Return **CONTINUE** after PASS (start next story same tick if time remains; otherwise end turn ready for next wake).

Return **STOP** only under Stop Conditions (including Done(global)).

# STOP CONDITIONS / ESCAPE HATCHES
- **Maximum outer iterations:** Infinite. Human stops the process when desired.
- **Done(global):** All queues complete, including the E11 integration critic WIN → STOP with status reflecting full completion. Then tell the human the next session is the content graph, and do not start it here.
- **Error budget (hard stop):** Same blocking phpunit failure (or its chain) not resolved within **10** fix rounds → STOP with an **Error Handoff Summary**:
  - Current `epic` / `milestone` / `story` ids
  - Original error + subsequent errors (chronological)
  - Files touched and approaches tried
  - Last command outputs (excerpts)
  - Suspected root cause and what a human should try next
  - Whether a skill was involved and what it claimed vs what failed
- **Gauntlet budget (hard stop):** 3 LOSE rounds on one milestone, or UNJUDGEABLE that cannot be fixed by repairing the run command → STOP. Do not mark the milestone done. Include the critic's gap and the proof it asked for.
- **Human verification required (hard stop):** Only when automatic verification cannot decide:
  - Ambiguous product/UX choice not locked in `.agents/state.json` / `EPIC_MAP.md`
  - Acceptance explicitly requires human taste
  - Credential / secret / hardware / paid API decisions (prefer mocks first)
  - Conflicting instructions between docs
- **Abort immediately (hard stop):** About to change third-party dependency surface in a risky way (unrelated upgrades, removing deps "for cleanup") unless the current story requires it. About to paste `context/` into a guide. About to add a game or a simulation epic.
- **MVP freeze gate:** Do not enter `post_mvp_order` until the MVP freeze checklist in `IMPLEMENTATION_STORIES.md` is green, including the E7 integration critic. If it cannot be auto-confirmed, STOP for human GO. After that, later queues need no further GO beyond their own critic WINs.

# TICK OUTPUT (keep short)
1. `milestone/story` worked (+ queue) or `gauntlet gap` fixed
2. PASS | FIXING (round n/10) | GAUNTLET WIN | GAUNTLET LOSE (round n/3) | STOP
3. Commands run + exit status
4. `next_action` (copied from updated state)
5. Skills created/updated (paths only), if any
```
