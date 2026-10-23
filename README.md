# Dev Theory Coach (THEORY_CORE)

By Weng (Weng Fei Fung)

![Last Commit](https://img.shields.io/github/last-commit/Siphon880gh/dev-theory-coach/main)
<a target="_blank" href="https://github.com/Siphon880gh" rel="nofollow"><img src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" alt="Github" data-canonical-src="https://img.shields.io/badge/GitHub--blue?style=social&logo=GitHub" style="max-width:8.5ch;"></a>
<a target="_blank" href="https://www.linkedin.com/in/weng-fung/" rel="nofollow"><img src="https://img.shields.io/badge/LinkedIn-blue?style=flat&logo=linkedin&labelColor=blue" alt="Linked-In" data-canonical-src="https://img.shields.io/badge/LinkedIn-blue?style=flat&amp;logo=linkedin&amp;labelColor=blue" style="max-width:10ch;"></a>
<a target="_blank" href="https://www.youtube.com/@WengTeachesCode/" rel="nofollow"><img src="https://img.shields.io/badge/Youtube-red?style=flat&logo=youtube&labelColor=red" alt="Youtube" data-canonical-src="https://img.shields.io/badge/Youtube-red?style=flat&amp;logo=youtube&amp;labelColor=red" style="max-width:10ch;"></a>

Learn **developer interview concepts** — data structures, algorithms, system design, and design patterns — through guides, step-by-step choices, quizzes, timed drills, decision cases, and one-screen cheatsheets. SAMPLE data only.

This is not a problem bank and it does not run code. Problem practice lives in the sibling app leetcode-coach.

---

## Start here

1. **Which track?** — Data structures (which shape), Algorithms (which procedure), System design (how to spend the hour), Design patterns (which name the force earns).
2. **Beginner path** — 12 units in prerequisite order, from `arrays` through `sliding-window`. Graded system-design and pattern cases stay locked until the quizzes on `arrays`, `hash-maps`, and `queues` are each at least 80%.
3. **Then deepen** — every guide has a step-by-step coach on the same slug. Quizzes check the idea. Drills time the closed-form ones. Cases ask you to commit, then show the consequence.

## Run

Requires **PHP 7.4+** (MAMP 7.4.33 works). PHPUnit 11 needs PHP 8.2+ for tests only.

`index.php` is the only entry point: Apache/MAMP via `DirectoryIndex index.php` + `.htaccess`, and the built-in server via `php -S localhost:8080 index.php` (it passes `assets/` files through).

```bash
composer serve
# same as: php -S localhost:8080 index.php
```

Open http://localhost:8080/ — SAMPLE stub home. Later routes (`/guides`, `/coach/{slug}`, …) land in the same `index.php` front controller.

## How it is built

Planning: [`EPIC_MAP.md`](./EPIC_MAP.md) · [`IMPLEMENTATION_STORIES.md`](./IMPLEMENTATION_STORIES.md) · [`RESOURCES.md`](./RESOURCES.md) · [`.agents/state.json`](./.agents/state.json)

Runtime loop: [`AGENTS_LOOP-Continue-Milestone.md`](./AGENTS_LOOP-Continue-Milestone.md)

Quality bar for each milestone: [`gauntlet/contract.md`](./gauntlet/contract.md)

Content loops: [`LOOPS/`](./LOOPS/README.md)

Reference material: [`context/`](./context/README.md) (`bash context/clone.sh`)

Scaffolded with the `new-trainer-app` skill in the parent `trainer/` workspace.
