# Dev Theory Coach (THEORY_CORE)

Learn **developer interview concepts** — data structures, algorithms, system design, and design patterns — through guides, step-by-step choices, quizzes, timed drills, decision cases, and one-screen cheatsheets. SAMPLE data only.

This is not a problem bank and it does not run code. Problem practice lives in the sibling app leetcode-coach.

**Author:** Weng Fei Fung (Weng)

---

## Start here

1. **Which track?** — Data structures (which shape), Algorithms (which procedure), System design (how to spend the hour), Design patterns (which name the force earns).
2. **Beginner path** — 12 units in prerequisite order, from `arrays` through `sliding-window`. Graded system-design and pattern cases stay locked until the quizzes on `arrays`, `hash-maps`, and `queues` are each at least 80%.
3. **Then deepen** — every guide has a step-by-step coach on the same slug. Quizzes check the idea. Drills time the closed-form ones. Cases ask you to commit, then show the consequence.

## Run

```bash
php -S localhost:8080 router.php   # or composer serve once it exists
```

The runtime is not in this repo yet. The next session builds it from the milestone loop below.

## How it is built

Planning: [`EPIC_MAP.md`](./EPIC_MAP.md) · [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`RESOURCES.md`](./RESOURCES.md) · [`.agents/state.json`](./.agents/state.json)

Runtime loop: [`AGENTS_LOOP-Continue-Milestone.md`](./AGENTS_LOOP-Continue-Milestone.md)

Quality bar for each milestone: [`gauntlet/contract.md`](./gauntlet/contract.md)

Content loops: [`LOOPS/`](./LOOPS/README.md)

Reference material: [`context/`](./context/README.md) (`bash context/clone.sh`)

Scaffolded with the `new-trainer-app` skill in the parent `trainer/` workspace.
