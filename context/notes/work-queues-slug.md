# Slug collision: queues

`context/curriculums/data-structures.md` and `design-systems.md` both originally used the slug `queues`.

The join key is one id per teachable unit. The structure lesson keeps `queues` (FIFO, deque, BFS). The system-design lesson is `work-queues`: accept a request, enqueue a job, return before the slow work finishes, and make the job safe to run twice.

Guides may say "queue" in prose. The id on the guide, the coach, the quiz, and the case is `work-queues` for the design lesson.
