# Design systems

How to design a running software system in an interview: pin the requirements, name the boundaries, choose storage, and say what breaks first. This track is services, data, and traffic.

Take data structures through hash maps, lists, and queues before the cache and queue lessons. Algorithms through sliding window helps the rate-limit lesson. The capstones reuse earlier lessons. Do not introduce a new mechanism in a capstone.

## Order

1. `interview-frame`
2. `estimates`
3. `requirements`
4. `api-and-boundaries`
5. `data-model`
6. `read-and-write-paths`
7. `caching`
8. `storage-and-indexes`
9. `replication-and-consistency`
10. `sharding`
11. `work-queues`
12. `rate-limiting`
13. `failure`
14. `capstone-url-shortener`
15. `capstone-feed`

## Lessons

### 01. Interview frame

**Slug:** `interview-frame`  
**After:** none  
**Outcome:** Spend the hour in a fixed order and leave time to name the bottleneck.

A workable order: clarify what the system must do, agree on scale, sketch the API, sketch the data, draw the read path and the write path, then pick one bottleneck and go deep. The interviewer is grading the order and the tradeoffs, not a drawing of every box you have ever seen. State an assumption out loud and move. Ask before inventing a feature the prompt did not require.

Stop when a component has no number and no failure story. That is the next place to go deep.

**Practice:** given "design a URL shortener", list the seven beats above in two minutes, with no boxes yet.  
**Artifacts:** guide; a step-by-step that chooses the next beat and rejects a dive into sharding before any requirement exists.

### 02. Estimates

**Slug:** `estimates`  
**After:** `interview-frame`  
**Outcome:** Turn users and actions into queries per second and bytes, within an order of magnitude.

There are about 10⁵ seconds in a day (86,400). Daily active users times actions per user, divided by 10⁵, is a rough average QPS. Peak is often a small multiple of average, say 2× to 10×. State the multiple. Storage is objects times bytes times retention. Bandwidth is QPS times response size. Memory for a cache is the working set, not the whole database.

Round to powers of ten. 100 million DAU with 10 reads a day is about 10⁴ average read QPS, not 100 million QPS. Write the formula before the number.

**Practice:** estimate read QPS, write QPS, and five-year storage for a photo service and for a URL shortener. The two answers should differ by more than a constant.  
**Artifacts:** guide; a game that slides DAU and actions and shows QPS jump by orders of ten.

### 03. Requirements

**Slug:** `requirements`  
**After:** `estimates`  
**Outcome:** Split what the system does from how well it must do it, and drop features that do not change the design.

Functional requirements are behaviors: create a short link, redirect, see your own posts. Non-functional requirements are bounds: availability, latency, durability, consistency, and the QPS from the previous lesson. Pick a latency target that a human would notice (redirects feel slow past a few hundred milliseconds; a feed can be slower). Pick a durability target (losing a short link is worse than losing a view count).

If a feature does not change storage, traffic, or failure, leave it out. Search, analytics, and accounts are different systems. Say you are leaving them out.

**Practice:** write five functional and three non-functional requirements for a chat service, then cross out two features that would need their own design.  
**Artifacts:** guide; a step-by-step that accepts or rejects a proposed requirement.

### 04. API and boundaries

**Slug:** `api-and-boundaries`  
**After:** `requirements`  
**Outcome:** Name the client-facing operations and the service boundary that owns each one.

Write a short list of operations with inputs and outputs. For a shortener: create(url) → code, redirect(code) → url. Say who calls them. A boundary is the line where you can scale, deploy, or fail independently. One service is enough until a requirement forces a split (a heavy feed fanout, a separate image pipeline). Premature services add network hops and partial failure for no gain.

Mention auth as a boundary only if the prompt needs it. Idempotency belongs on any create that a client might retry. That theme returns in `failure`.

**Practice:** write the create and read operations for a pastebin, including what the client sends back on a retry.  
**Artifacts:** guide; a step-by-step that splits or refuses to split a service.

### 05. Data model

**Slug:** `data-model`  
**After:** `api-and-boundaries`  
**Outcome:** Choose entities, keys, and access paths before choosing a database product.

List entities and the queries the API actually runs. A short link is (code → long url, owner, created at). The redirect query is get-by-code, so code is the primary key. The "my links" query is by owner, so that needs its own index or table. A feed item is (user, time, post). The query is recent posts for a user, so the key is user plus time, not post id alone.

Pick a key from the query. A product name (Postgres, DynamoDB, Cassandra) comes after the access path is clear. Wide open-ended text search is a different store. Say so and leave it unless the prompt is search.

**Practice:** entities and primary access path for URL shortener, then for chat messages in a room.  
**Artifacts:** guide; a step-by-step that rejects a key that does not match the hottest query.

### 06. Read and write paths

**Slug:** `read-and-write-paths`  
**After:** `data-model`  
**Outcome:** Draw one successful read and one successful write, including the row they touch.

Follow a single request from client to store and back. Name the synchronous steps. A write that must be visible to the next read from the same user is a different path from a write that can land later. If the read QPS is far above the write QPS, say that the read path is the one to optimize. If writes fan out to many readers, say that the write path is the expensive one.

Keep this drawing small. Caching, replicas, and queues are later lessons that you will hang on this path, not replacements for it.

**Practice:** narrate redirect for a shortener and "post a message" for chat, five steps each.  
**Artifacts:** guide; a game that highlights the next hop on a fixed diagram; a step-by-step that finds which hop exceeds the latency budget.

### 07. Caching

**Slug:** `caching`  
**After:** `read-and-write-paths`, data-structures `hash-maps`, `linked-lists`  
**Outcome:** Put a cache on a hot read, and choose an invalidation story before talking about hit rate.

Cache-aside: the service reads the cache, and on a miss reads the store and fills the cache. Write-through: writes update the cache and the store together. Write-back: writes hit the cache and flush later, which is faster and loses data on a crash. Most interview designs want cache-aside plus a short TTL, or an explicit delete on write.

The hard part is staleness. A TTL bounds how wrong a value can be. A delete-on-write is fresher and races with a stale fill. Say which inconsistency you accept. Eviction when memory is full is LRU: a hash map for lookup and a doubly linked list for recency, from the data-structures track. Name the hot key problem: one key that every request wants will overload one cache node.

**Practice:** where to cache a shortener redirect, what the TTL is, and what happens on the update of a long URL.  
**Artifacts:** guide; a game that runs LRU evictions on a tiny cache; a step-by-step that picks cache-aside vs write-through.

### 08. Storage and indexes

**Slug:** `storage-and-indexes`  
**After:** `data-model`  
**Outcome:** Match the store to the access path, and say what an index costs on the write.

A relational store fits rows, joins, and transactions. A document store fits self-contained objects you always fetch whole. A wide-column or key-value store fits a known primary key at high write volume. An index makes one read fast and makes every write maintain that index. Add indexes for the queries from the data-model lesson, and refuse indexes nobody queries.

A transaction belongs around invariants that must hold together (debit and credit, reserve the last seat). A single-row update does not need a distributed transaction. Full-text search and analytics belong in stores built for them, fed by the queue lesson, not in the request path.

**Practice:** choose a store and one extra index for "my short links" and for "messages in this room, newest first".  
**Artifacts:** guide; a step-by-step that adds or refuses an index.

### 09. Replication and consistency

**Slug:** `replication-and-consistency`  
**After:** `storage-and-indexes`  
**Outcome:** Say what a replica is for, and what a client can observe after a write.

A replica is another copy. It adds read capacity and a failover target. It also adds lag. Read-after-write from the primary gives the user their own update. Reading from a replica can hide that update for the lag window. Quorum writes and reads (overlap of write quorum and read quorum) let you tune that window. Leader-follower is easier to explain than multi-leader, and multi-leader means conflict resolution.

Availability versus consistency shows up when the network splits. Name which side you keep for this product. A shortener can serve a slightly stale redirect. A seat map for a concert should not sell the same seat twice. CAP is a constraint on a partition, not a slogan to open the interview with.

**Practice:** for the shortener and for concert tickets, say where a read goes right after a write, and what the user sees during a partition.  
**Artifacts:** guide; a step-by-step that routes a read to the primary or a replica.

### 10. Sharding

**Slug:** `sharding`  
**After:** `replication-and-consistency`, `estimates`  
**Outcome:** Split data so one machine does not hold the hot query, and name the new hot spot you just created.

Shard when one node's disk, write rate, or working set no longer fits. The shard key is the field in the hottest query. A shortener shards by code. A chat shard by room can put one celebrity room on one shard. Hashing the key spreads writes. Range-sharding keeps scans of a key prefix on one shard and creates hot ranges.

Resharding moves rows. Consistent hashing reduces how many rows move when a node arrives. Cross-shard transactions are the cost of a key that does not match the query. If the estimate still fits on one primary plus replicas, say you are not sharding yet.

**Practice:** pick a shard key for short links and for a social feed, and name one query that becomes a scatter-gather.  
**Artifacts:** guide; a game that hashes keys onto nodes and then sends a celebrity key at one node; a step-by-step that shards or waits.

### 11. Work queues

**Slug:** `work-queues`  
**After:** `read-and-write-paths`, data-structures `queues`  
**Outcome:** Move work off the request when the user does not need the result immediately.

A queue decouples a fast accept from a slow job: image resize, feed fanout, email, search indexing. The request writes the source of truth, enqueues a job, and returns. Workers pull jobs. The user-visible promise has to match that. "Your post is live" cannot return before the write commits. "Followers will see it shortly" can.

Jobs must be safe to run twice, because workers crash after the side effect and before the ack. That is the bridge to failure and idempotency. Ordering is per partition key if you need it (all events for one room). A queue is not a database. Do not treat it as the only copy of the data.

**Practice:** which steps of "upload a photo" and "post to followers" are on the request, and which are jobs.  
**Artifacts:** guide; a game that acks or retries a job; a step-by-step that leaves work on the request or moves it.

### 12. Rate limiting

**Slug:** `rate-limiting`  
**After:** `api-and-boundaries`, algorithms `sliding-window`  
**Outcome:** Protect a boundary with a limiter, and pick the algorithm from the burst you allow.

A limiter counts actions per key (user, IP, API token) in a time window. Fixed window is simple and allows twice the rate at the boundary between windows. Sliding window fixes that boundary. Token bucket allows a burst up to the bucket size, then a steady refill. Leaky bucket smooths the output rate.

The counter has to live where every app node agrees, or each node enforces a fraction of the budget. A centralized store (Redis or similar) is the usual interview answer, with the cache lesson's hot-key warning if one key is huge. Return a clear rejection. Do not silently drop writes the client thinks succeeded.

**Practice:** limit create-link to 10 per minute per user, and say what happens on the 11th call in the same second.  
**Artifacts:** guide; a game that compares a fixed window and a token bucket on the same burst; a step-by-step that places the limiter at the edge or in the service.

### 13. Failure

**Slug:** `failure`  
**After:** `work-queues`, `replication-and-consistency`  
**Outcome:** Name timeouts, retries, and idempotency for one hop that will fail.

Every network call can hang or return an error. Set a timeout shorter than the user's budget. Retry only when the call might have failed before the side effect, or when the side effect is idempotent. A create without an idempotency key, retried, writes two rows. The client sends a key, the server stores the key with the result, and a repeat returns the first result.

Backoff with jitter stops a thundering herd after an outage. A circuit breaker stops calling a dependency that is already down. Partial failure is normal once you have more than one hop: say which hop is allowed to be down (a cache) and which is not (the primary write).

**Practice:** the client retries create(url). Walk the idempotency key through cache, queue, and primary.  
**Artifacts:** guide; a step-by-step that retries or refuses a retry on a non-idempotent write.

### 14. Capstone: URL shortener

**Slug:** `capstone-url-shortener`  
**After:** `failure`  
**Outcome:** Run the whole frame on one small system without adding a mechanism this track did not teach.

Use `interview-frame` through `failure`. Functional scope: create a short code for a long URL, redirect, optionally list a user's links. Estimate a read-heavy ratio and size the code space (a random code long enough that collisions are rare, or a counter range per shard). API: create and redirect. Data: code → url. Read path: cache-aside then primary or replica. Write path: allocate code, insert, return. Shard by code only if the estimate says one primary is not enough. Rate-limit create. Idempotency key on create. The deep dive is the code allocation and the cache invalidation when a URL is updated.

Leave out analytics and custom aliases unless the interviewer asks. If they ask, say which lesson the new feature stresses.

**Practice:** a 35-minute outline with one deep dive, then a second pass that changes the ratio to write-heavy and says what moves.  
**Artifacts:** guide; a step-by-step session whose choices are the beats of this track, with wrong paths that shard too early or skip the idempotency key.

### 15. Capstone: feed

**Slug:** `capstone-feed`  
**After:** `capstone-url-shortener`  
**Outcome:** Choose fanout-on-write or fanout-on-read from follower counts, and name the queue that does the work.

Scope: post, and read a home feed of people you follow. Estimate posts and follows. Data: posts by author and time, and a follow graph. Fanout-on-write pushes a post id into each follower's feed at write time, so reads are a single lookup and celebrity accounts explode the write. Fanout-on-read gathers recent posts from followees at read time, so writes stay cheap and reads get heavy. A hybrid pushes for normal users and pulls for accounts above a follower threshold.

The push work is a queue from the `work-queues` lesson. The read of your own new post must hit the primary or a feed you just updated (read-your-writes from the consistency lesson). Cache the rendered page of a hot feed. Rank only if asked, and then it is a strategy over the candidate set, which the design-patterns track names. Do not invent ranking math here.

**Practice:** two users, one with 200 followers and one with 20 million. Say which fanout each uses.  
**Artifacts:** guide; a step-by-step that picks fanout from a follower count and points at the lesson that pays for it.
