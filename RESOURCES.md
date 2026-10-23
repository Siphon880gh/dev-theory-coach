# Resource types

A **resource** is a teaching unit the learner can open. Guides, step-by-step coach sessions, quizzes, drills, cases, and cheatsheets are resources.

**Not resources:** learning paths, the dashboard, and nav. Those find or sequence resources.

All data is **educational practice content** (not live systems). This app is not a coding-problem bank, not a code runner, and not a substitute for a live system-design interview. `../leetcode-coach` owns problem practice. Never stamp SAMPLE on UI chrome, and never put SAMPLE / "SAMPLE example" / "SAMPLE lesson" / "Recall checkpoint" in learner-facing prose.

Teachable unit is `slug` (what you are learning). **Track** is which learner conversation the unit belongs to. Cognitive ability is the **resource type** (how you practice). The same `slug` should echo across types — see [`LOOPS/LOOP-Graph.md`](./LOOPS/LOOP-Graph.md).

---

## Tracks

One **Guides** hub. Four tracks. Every guide has a step-by-step coach on the **same slug**.

| Track | Label | What it trains |
|-------|-------|----------------|
| **A** `structures` | Data structures | Which shape holds the data, what each operation costs, and when to refuse the shape. |
| **B** `algorithms` | Algorithms | Which procedure searches, walks, narrows, or reuses work, and what it costs. |
| **C** `systems` | System design | How to spend a design interview: requirements, data, traffic, and the first thing that breaks. |
| **D** `patterns` | Design patterns | Which named object structure the force earns, and when a function is enough. |

The **Beginner path** is 12 units in prerequisite order (phone-screen set): `arrays`, `strings`, `hash-maps`, `linked-lists`, `stacks`, `queues`, `complexity`, `recursion`, `two-pointers`, `binary-search`, `trees`, `sliding-window`. It is a recommended sequence, not a lock on the first lesson of tracks C and D.

**Case gate:** graded cases (tracks C and D) unlock when quiz accuracy on `arrays`, `hash-maps`, and `queues` is each ≥ 0.8. Guides on those tracks can be read before the gate. Drills stay inside their own family and do not gate other tracks.

Within a path, a unit unlocks when every slug in its **After** column is complete (guide read and that family's quiz submitted). The Beginner path is linear on top of that: its milestone N also waits for milestone N−1.

---

## Families (`slug` catalog)

Prerequisite order within each track. The Beginner path is the 12 slugs named above, not "the first 12 rows."

| # | `slug` | Track | Unit (one line) | After | Safety-critical | Rungs required |
|---|--------|-------|-----------------|-------|-----------------|----------------|
| 1 | `arrays` | A | Index access is O(1); a middle insert shifts the tail. | — | no | recognize, explain, apply |
| 2 | `strings` | A | A string is a character array, and an edit often copies. | `arrays` | no | recognize, explain, apply |
| 3 | `hash-maps` | A | Key lookup is O(1) on average and O(n) when every key collides. | `arrays` | no | recognize, explain, apply |
| 4 | `linked-lists` | A | Splice a known node in O(1); reaching index i is O(n). | `arrays` | no | recognize, explain, apply |
| 5 | `stacks` | A | Last in, first out: nested pairs, undo, and a depth-first walk. | `arrays` | no | recognize, explain, apply |
| 6 | `queues` | A | First in, first out, and a deque when both ends move. | `stacks` | no | recognize, explain, apply |
| 7 | `trees` | A | Preorder, inorder, postorder, and level order, and what each order is for. | `queues` | no | recognize, explain, apply |
| 8 | `binary-search-trees` | A | Search, insert, and delete in O(h); without balance, h can be n. | `trees` | no | recognize, explain, apply |
| 9 | `heaps` | A | Peek the extreme in O(1) and fix the heap in O(log n), stored as an array. | `trees` | no | recognize, explain, apply |
| 10 | `graphs` | A | An adjacency list is the default; a matrix only when the graph is tiny and dense. | `queues` | no | recognize, explain, apply |
| 11 | `tries` | A | Prefix queries cost O(length), not O(dictionary size). | `trees` | no | recognize, explain, apply |
| 12 | `union-find` | A | Merge two sets and test membership in nearly constant time. | `graphs` | no | recognize, explain, apply |
| 13 | `complexity` | B | Bound time and space, and say when the question wants amortized cost. | `arrays` | no | recognize, explain, apply |
| 14 | `recursion` | B | A base case, a smaller subproblem, and an honest stack story. | `complexity`, `stacks` | no | recognize, explain, apply |
| 15 | `binary-search` | B | Halve a sorted range in O(log n) without breaking the loop invariant. | `complexity`, `arrays` | no | recognize, explain, apply |
| 16 | `two-pointers` | B | Move two indices in one pass when each move throws away a region. | `arrays` | no | recognize, explain, apply |
| 17 | `sliding-window` | B | Keep a contiguous summary current so the whole scan is O(n). | `two-pointers`, `hash-maps` | no | recognize, explain, apply |
| 18 | `sorting` | B | Pick a sort from the comparison bound, stability, and the key range. | `complexity`, `heaps` | no | recognize, explain, apply |
| 19 | `bfs-dfs` | B | Walk each reachable node once; BFS when hop count is the distance. | `queues`, `stacks`, `graphs`, `trees` | no | recognize, explain, apply |
| 20 | `backtracking` | B | Choose, explore, and undo, and prune a partial choice that already fails. | `recursion`, `bfs-dfs` | no | recognize, explain, apply |
| 21 | `greedy` | B | Commit to the local choice only when an exchange argument says you never revise it. | `sorting` | no | recognize, explain, apply |
| 22 | `dynamic-programming` | B | Write the state sentence and the recurrence before any loop. | `recursion`, `greedy` | no | recognize, explain, apply |
| 23 | `graph-algorithms` | B | Pick BFS, Dijkstra, or Bellman-Ford from the weight constraint. | `bfs-dfs`, `heaps`, `union-find` | no | recognize, explain, apply |
| 24 | `intervals` | B | Sort and sweep so merge, overlap, and scheduling are one pass. | `sorting`, `greedy` | no | recognize, explain, apply |
| 25 | `interview-frame` | C | Spend the hour in a fixed order and leave time to name the bottleneck. | — | no | recognize, explain, apply, diagnose |
| 26 | `estimates` | C | Turn users and actions into QPS and bytes, within an order of magnitude. | `interview-frame` | no | recognize, explain, apply, diagnose |
| 27 | `requirements` | C | Split behavior from bounds, and drop a feature that does not change the design. | `estimates` | no | recognize, explain, apply, diagnose |
| 28 | `api-and-boundaries` | C | Name the operations and the boundary that owns each one. | `requirements` | no | recognize, explain, apply, diagnose |
| 29 | `data-model` | C | Choose entities and keys from the query before naming a database product. | `api-and-boundaries` | no | recognize, explain, apply, diagnose |
| 30 | `read-and-write-paths` | C | Narrate one successful read and one successful write, including the row they touch. | `data-model` | no | recognize, explain, apply, diagnose |
| 31 | `caching` | C | Cache a hot read, and choose the invalidation story before the hit rate. | `read-and-write-paths`, `hash-maps`, `linked-lists` | no | recognize, explain, apply, diagnose |
| 32 | `storage-and-indexes` | C | Match the store to the access path, and say what an index costs on the write. | `data-model` | no | recognize, explain, apply, diagnose |
| 33 | `replication-and-consistency` | C | Say what a replica is for, and what a client observes after a write. | `storage-and-indexes` | no | recognize, explain, apply, diagnose |
| 34 | `sharding` | C | Split data off one machine, and name the hot spot the split just created. | `replication-and-consistency`, `estimates` | no | recognize, explain, apply, diagnose |
| 35 | `work-queues` | C | Move work off the request when the user does not need the result immediately. | `read-and-write-paths`, `queues` | no | recognize, explain, apply, diagnose |
| 36 | `rate-limiting` | C | Protect a boundary, and pick the limiter from the burst you allow. | `api-and-boundaries`, `sliding-window` | no | recognize, explain, apply, diagnose |
| 37 | `failure` | C | Name the timeout, the retry, and the idempotency key for one hop that will fail. | `work-queues`, `replication-and-consistency` | no | recognize, explain, apply, diagnose |
| 38 | `capstone-url-shortener` | C | Run the whole frame on one small read-heavy system without a new mechanism. | `failure` | no | recognize, explain, apply, diagnose, design |
| 39 | `capstone-feed` | C | Pick fanout-on-write or fanout-on-read from the follower count. | `capstone-url-shortener` | no | recognize, explain, apply, diagnose, design |
| 40 | `when-to-name-a-pattern` | D | Say the force first, then the name, and decline the structure when a branch is enough. | — | no | recognize, explain, apply, diagnose |
| 41 | `strategy` | D | Swap a whole algorithm behind one interface without editing the caller. | `when-to-name-a-pattern` | no | recognize, explain, apply, diagnose |
| 42 | `state` | D | Let behavior follow the mode, with each transition written in one place. | `strategy` | no | recognize, explain, apply, diagnose |
| 43 | `observer` | D | Notify dependents on a change, and name the leak and the order problems. | `when-to-name-a-pattern` | no | recognize, explain, apply, diagnose |
| 44 | `factory` | D | Hide which concrete type gets constructed, in one place the caller can find. | `strategy` | no | recognize, explain, apply, diagnose |
| 45 | `builder` | D | Assemble one object with many optional parts, and refuse a half-built object. | `factory` | no | recognize, explain, apply, diagnose |
| 46 | `adapter` | D | Wrap a type you do not own so the caller keeps the interface it already has. | `when-to-name-a-pattern` | no | recognize, explain, apply, diagnose |
| 47 | `facade` | D | Offer one operation in front of a sequence the caller should not orchestrate. | `adapter` | no | recognize, explain, apply, diagnose |
| 48 | `decorator` | D | Add behavior by wrapping, and say how wrap order changes the result. | `strategy` | no | recognize, explain, apply, diagnose |
| 49 | `singleton` | D | One shared instance, passed in from the edge, rather than looked up globally. | `factory` | no | recognize, explain, apply, diagnose |
| 50 | `command` | D | Represent a request as an object so you can queue it, log it, or undo it. | `when-to-name-a-pattern`, `stacks` | no | recognize, explain, apply, diagnose |
| 51 | `composite` | D | Treat one item and a group through one interface. | `decorator`, `trees` | no | recognize, explain, apply, diagnose |
| 52 | `proxy` | D | A stand-in controls whether the real object is reached at all. | `decorator` | no | recognize, explain, apply, diagnose |
| 53 | `composition` | D | Assemble behavior by holding objects; inherit only for a true subtype. | `strategy`, `decorator`, `singleton` | no | recognize, explain, apply, diagnose, design |

`After` is the prerequisite set. A dash means the unit can start a track. Safety-critical is **no** for every family: nothing here is mains wiring, medicine, or a live trade. Capstone cases still wait for the case gate so the learner does not invent a mechanism the track never taught.

Drill families (closed-form answer under a timer): `complexity`, `binary-search`, `two-pointers`, `sliding-window`, `sorting`, `estimates`, `rate-limiting`.

Decision families (one decide-and-reveal case): every track C and track D slug. Design cases, which must name at least two earlier slugs in the consequence: `capstone-url-shortener`, `capstone-feed`, `composition`.

---

## Mastery ladder and floors

| Rung | Learner can… | Proven by |
|------|--------------|-----------|
| recognize | name the unit and when it applies | Guide read |
| explain | reconstruct the reasoning unaided | Coach success, with a clean second pass (no rewind) |
| apply | produce the answer on a sample prompt | Quiz score ≥ 0.8. Drill families also require timed drill accuracy ≥ 0.8 |
| diagnose | find what is wrong in a scenario, or reject a bad proposal | Case grade on tracks C and D |
| design | combine earlier units to meet a goal | Design case on `capstone-url-shortener`, `capstone-feed`, and `composition` |

Tracks A and B declare recognize, explain, and apply. Tracks C and D add diagnose. The three design slugs add design. A rung that a row does not list is not required for Done.

**Coverage floors** (mirror in `.agents/state.json → decisions.content_floors`):

| Floor | Value |
|-------|-------|
| `guides_per_family` | 1 |
| `coach_per_guide` | 1 |
| `quiz_items_per_family` | 6 |
| `drills_per_drill_family` | 8 |
| `cases_per_decision_family` | 1 |
| `cheatsheets_per_family` | 1 |

**Done(graph):** every family meets every floor on every hop in its echo chain. Drill and case floors apply only to families whose row includes that hop. Cheatsheets apply to every family. Home copy is one extra pass, not a per-family floor.

---

## Catalog

| Type | What the learner does | Route | Registry |
|------|------------------------|-------|----------|
| **Guide** | Read the unit: rule, why, worked example, when to use; open the coach | `/guides`, `/guides/{slug}` | `content/guides/<slug>.json` |
| **Step-by-step** | Walk a method tree: wrong → explain → rewind → success | `/coach/{slug}` (same slug as the guide) | `content/coaching/<slug>.json` |
| **Quiz** | Answer an untimed MCQ set; each option explains itself | `/quiz/{slug}` | `content/quizzes/<slug>.json` |
| **Drill** | Answer a timed closed-form item; then read the thought chain | `/drill/{slug}` | `content/drills/<slug>.json` |
| **Case** | Read a practice scenario, commit a decision, see the consequence | `/case/{slug}` | `content/cases/<slug>.json` |
| **Cheatsheet** | Scan one screen: when to pick, when to skip | `/cheatsheet/{slug}` | `content/cheatsheets/<slug>.json` |

Path milestones point at resources with `contentRefs` such as `guide:<slug>`, `coach:<slug>`, `quiz:<slug>`, `drill:<slug>`, `case:<slug>`, `cheatsheet:<slug>`.

---

## Per-type contracts

### Guide (hub)

`slug`, `track`, `title`, `summary`, `body` (300–800 words, original), `relatedCoachSlug` (= slug), optional `parts` with `rule` / `why` / `example` / `use` / `refuse`. Sections inside the body, in this order: the rule, why it holds, one **concrete** worked example (numbers, small structures, or named steps a human can rehearse — not an abstract restatement of the rule), when to use it, when to refuse it. Prefer ~400–600 teaching words. If under 300, expand the worked example — never pad with SAMPLE labels, "Recall checkpoint" lines, or identical repeated paragraphs. Never put SAMPLE / "SAMPLE example" / "SAMPLE lesson" in prose. No `sample: true` stamp. If the coach tree is not authored yet, the CTA still opens `/coach/<slug>` with a "not authored yet" state. Never 404.

### Step-by-step (coach)

Deterministic graph: `start` + ≥2 continue layers, ≥2 `wrong` nodes with `rewind_to`, ≥1 `success` with empty choices. `meta.slug` = slug. First message is an educational setup. Teach **which method to pick**, not a quiz restated as buttons. No runtime LLM.

### Quiz

`slug`, `items` length ≥ 6. Each item: `prompt`, exactly 4 `choices`, `answerIndex`, `whyRight`, and `whyWrong` for every other choice. Untimed. No code is executed. A pass for the mastery flag is score ≥ 0.8.

### Drill

Only the seven drill families. `items` length ≥ 8. Each item: `prompt`, `accept` (normalized strings or a single choice), `thoughtChain` of 3 steps shown after submit, `seconds` cap. Timed. A pass is accuracy ≥ 0.8 inside the caps.

### Case

Tracks C and D only. `scenario` (educational), `prompt`, 3 or 4 `choices`, `best`, and a `consequence` for every choice. One case per family meets the floor. Design families (`capstone-url-shortener`, `capstone-feed`, `composition`) name at least two earlier slugs in the best consequence. Grade is revealed after commit, not before.

### Cheatsheet

`slug`, `track`, `title`, at most 12 recall lines, one "pick when" sentence, one "skip when" sentence. One screen. No second essay.
