# Context — reference material for Dev Theory Coach

Local reference material for the agent when authoring guides, coaching trees, and practice content for **developer interview concepts: data structures, algorithms, system design, and design patterns**. It is not served by the app.

Clones stay on disk only (`repos/`, gitignored) and are not redistributed by this repo. Licenses and copyright remain with each upstream project. `toc/` and `notes/` are original outlines and notes written for this app and are committed.

## Clone

From the app repo root:

```bash
bash context/clone.sh
```

The script shallow-clones (`--depth 1`) into `context/repos/`. If a folder is already a git clone, it runs `git pull --ff-only`.

## Catalog

| Folder / file | Upstream | License | Use for |
|---------------|----------|---------|---------|
| `repos/<!-- agent -->` | [owner/repo](https://github.com/owner/repo) | <!-- agent --> | <!-- agent: which tracks / families; guides vs drill seeds vs cases --> |
| `toc/<!-- agent -->.md` | <!-- agent: url --> | n/a (outline only) | syllabus ordering for track <!-- agent --> |

## Could not fetch

<!-- agent: list sources that were paywalled / blocked / empty, with the URL tried. Delete the section if empty. -->

## Suggested additions for the human

<!-- agent: repos or books you would like added but could not verify the license for, or that need an account. Delete if empty. -->

## Authoring

After cloning, open matching material under `context/` when creating guides, coaching trees, or practice items. Rewrite in original words; do not paste.
