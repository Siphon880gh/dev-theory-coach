# Dev Theory Coach (THEORY_CORE)

Learn **developer interview concepts: data structures, algorithms, system design, and design patterns** through <!-- agent: list the chosen resource types in learner language --> — with SAMPLE data only.

**Author:** Weng Fei Fung (Weng)

---

## Start here

1. **Which track?** — <!-- agent: Track A vs B in one line each -->
2. **Beginner path** — <!-- agent: n --> units in prerequisite order, with a gate before <!-- agent: graded content -->
3. **Then deepen** — every guide has a step-by-step coach; <!-- agent: practice types --> echo the same unit

## Run

```bash
php -S localhost:8080 router.php   # or composer serve once it exists
```

## How it is built

Planning: [`EPIC_MAP.md`](./EPIC_MAP.md) · [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`RESOURCES.md`](./RESOURCES.md) · [`.agents/state.json`](./.agents/state.json)

Runtime loop: [`AGENTS_LOOP-Continue-Milestone.md`](./AGENTS_LOOP-Continue-Milestone.md)

Content loops: [`LOOPS/`](./LOOPS/README.md)

Reference material: [`context/`](./context/README.md) (`bash context/clone.sh`)

Scaffolded with the `new-trainer-app` skill in the parent `trainer/` workspace.
