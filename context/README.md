# Context — reference material for Dev Theory Coach

Local reference material for the agent when authoring guides, coaching trees, quizzes, drills, cases, and cheatsheets for **developer interview concepts: data structures, algorithms, system design, and design patterns**. It is not served by the app.

Clones stay on disk only (`repos/`, gitignored) and are not redistributed by this repo. Licenses and copyright remain with each upstream project. `toc/` and `notes/` are original outlines and notes written for this app and are committed. `curriculums/` is the syllabus of record, already written for this app.

## Clone

From the app repo root:

```bash
bash context/clone.sh
```

The script shallow-clones (`--depth 1`) into `context/repos/`. If a folder is already a git clone, it runs `git pull --ff-only`.

## Catalog

| Folder / file | Upstream | License | Use for |
|---------------|----------|---------|---------|
| `curriculums/` | written for this app | original | Syllabus of record: slugs, order, outcomes, practice seeds. Wins over every clone. |
| `notes/syllabus-wins.md` | written for this app | original | How to treat clones vs curricula; boundary with leetcode-coach. |
| `notes/work-queues-slug.md` | written for this app | original | Why the design lesson is `work-queues` and the structure lesson is `queues`. |
| `repos/javascript-algorithms` | [trekhleb/javascript-algorithms](https://github.com/trekhleb/javascript-algorithms) | MIT | Track A and B worked-example seeds. Rewrite. |
| `repos/tech-interview-handbook` | [yangshun/tech-interview-handbook](https://github.com/yangshun/tech-interview-handbook) | MIT | Quiz-distractor seeds and "which approach" seeds for tracks A–C. Rewrite. |
| `repos/coding-interview-university` | [jwasham/coding-interview-university](https://github.com/jwasham/coding-interview-university) | CC-BY-SA-4.0 | Coverage check that a beginner unit is not missing a standard name. Do not copy study-plan prose. |
| `repos/system-design-primer` | [donnemartin/system-design-primer](https://github.com/donnemartin/system-design-primer) | CC-BY-4.0 (LICENSE.txt; GitHub API reports NOASSERTION) | Track C case seeds: shortener, feed, cache, sharding. Rewrite. |
| `repos/java-design-patterns` | [iluwatar/java-design-patterns](https://github.com/iluwatar/java-design-patterns) | MIT (LICENSE.md; GitHub API reports NOASSERTION) | Track D structure sketches. Rewrite in original scenarios. Do not port Java listings into guides. |
| `toc/mit-6-006-spring-2020.md` | [MIT OCW 6.006 Spring 2020](https://ocw.mit.edu/courses/6-006-introduction-to-algorithms-spring-2020/pages/resource-index/) | n/a (outline only) | Track A/B ordering check. |
| `toc/ddia-1st-ed.md` | [O'Reilly DDIA contents](https://www.oreilly.com/library/view/designing-data-intensive-applications/9781491903063/) | n/a (outline only) | Track C vocabulary check. |
| `toc/gof-pattern-names.md` | GoF catalog names | n/a (names only) | Track D coverage check: which names we teach and which we refuse as families. |

Local siblings, not cloned:

| Path | Use for |
|------|---------|
| `../leetcode-coach` | PHP guide and coach-tree runtime patterns. That app owns problem practice. Do not import its content. |
| `../mental-math` | PHP guides hub, drill shell, coach rewind, progress key. Gauntlet bar for those surfaces. |
| `../stocks-trainer` | Case player and path UI patterns if a PHP case page needs a structural comparison. |

## Could not fetch

None. Book prose was not requested; only public contents lists were outlined.

## Suggested additions for the human

- `patmorin/ods` (Open Data Structures) is CC BY 2.5 CA. The GitHub repo is about 260MB, so it is not in `clone.sh`. A human who wants the textbook source can clone and keep `latex/` only.
- Alex Xu, *System Design Interview* volumes: copyrighted books. If you own them, a private contents list can sit in `toc/` as titles only. Do not paste chapters into `context/repos/`.

## Authoring

After cloning, open matching material under `context/` when creating guides, coaching trees, or practice items. Rewrite in original words; do not paste. `curriculums/` wins on order and on the outcome sentence.
