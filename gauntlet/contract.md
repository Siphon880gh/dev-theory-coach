# Gauntlet contract — Dev Theory Coach

The builder does not decide that a milestone is finished. A fresh critic does. Scores out of 10 are not a verdict.

## Goal

A working engineer can spend 15–25 minutes on a sample interview concept, name it, say the cost or the force, and reject the common wrong pick. The Beginner path is the phone-screen set in `RESOURCES.md`.

## Bar

Compare the running app with two local siblings. Do not import their code or their copy.

| Surface | Bar |
|---------|-----|
| Guides hub, track labels, coach rewind, progress reset | `../mental-math` |
| Guide opens a coach on the same slug; a missing tree is a slot, not a 404 | `../leetcode-coach` |
| Which units exist, and in what order | `RESOURCES.md` and `context/curriculums/` |

A self-written rubric supports these bars. It does not replace them.

## Gates

Every gate is pass or fail. Do not average them.

| Gate | Evidence |
|------|----------|
| Tests | `./vendor/bin/phpunit` exits 0 |
| Join key | HTML and JSON use `slug` values from `RESOURCES.md`. `queues` and `work-queues` are both present and are not the same page |
| Educational data | Practice content is educational / non-live; no live APIs or credentials |
| No paste | The milestone's paste test, when the story has one, is green |
| Slot, not 404 | A slug with no coach file returns 200 and the text "not authored yet" |
| Progress | `assets/js/progress.js` writes only `theory_core_progress_v1` |
| Milestone acceptance | Every Acceptance cell in the current milestone in `IMPLEMENTATION_STORIES.md` is true, checked by running the app or the tests, not by reading the builder's summary |

## Hard boundaries

- Local only. No deploy, no paid API, no accounts
- No runtime LLM inside coaching
- Do not copy sibling source in to "match the bar"
- Do not weaken a test to get a WIN

## Critic

Start a **new** critic with a clean context. Give it this file, the milestone id, the Acceptance cells, and the repo path. Do not give it the builder's reasoning, the chat summary, or a claim that the work is done.

The critic runs the tests and requests the routes (PHP built-in server, or the view functions the tests already render). It looks at the sibling apps only as the bar. It returns one of:

- `WIN` — our milestone is at the bar and every gate passes, with the command each gate used
- `LOSE` — the single biggest gap, plus the one proof that would show it is closed
- `UNJUDGEABLE` — the critic could not run the app or the tests. Fix the inspection path before changing the product

## Loop

- On `LOSE`, the next tick fixes only that gap. Then a new critic judges again
- Budget: **3** critic rounds per milestone. Record rounds in `gauntlet/state.json`
- On the third `LOSE`, or on `UNJUDGEABLE` that a tick cannot fix, stop. Write the open gap. Do not mark the milestone done
- Freeze a milestone only on `WIN`

## Integration critics

After **E7.M1**, one fresh critic walks the beginner path end to end: home → `arrays` guide → `arrays` coach success → `arrays` quiz → next slug → path-complete state with a fixture that has finished all 12 guides. Verdict covers the whole P0 app, not only the last story.

After **E11.M1**, one fresh critic checks the four track labels, a locked row that names the missing slug, a gated case, a drill slot on a non-drill family, and a cheatsheet slot. That verdict is the runtime-complete check before the content graph.

## Stop

The run stops on WIN of the milestone, on the user stopping it, on the 3-round budget, or when the remaining gap is a copy preference the Acceptance cell already marked as human taste. No Acceptance cell in the P0 stories is human taste. They are tests.
