# Algorithms

Repeatable ways to search, walk, narrow, and reuse work. Take the data-structures track through graphs first. Each lesson should state time and space.

## Order

1. `complexity`
2. `recursion`
3. `binary-search`
4. `two-pointers`
5. `sliding-window`
6. `sorting`
7. `bfs-dfs`
8. `backtracking`
9. `greedy`
10. `dynamic-programming`
11. `graph-algorithms`
12. `intervals`

## Lessons

### 01. Complexity

**Slug:** `complexity`  
**After:** `arrays`  
**Outcome:** Bound a piece of code in big-O for time and space, including amortized cost.

Count dominant operations as input size n grows. Drop constants and lower terms. Nested loops over n are O(n²) unless the inner bound shrinks. A loop that doubles an index is O(log n). Recursion that branches twice with no overlap is O(2^n). Space includes the output and the call stack, not only the heap structures the code allocates.

Amortized cost averages the expensive step across the cheap ones. Dynamic-array append is amortized O(1) because resizes are rare. Worst-case of that single append is O(n). Say which one the interview asked for.

**Practice:** label three short snippets; compare O(n log n) and O(n²) at n = 10⁵.  
**Artifacts:** guide; a game that grows n and shows which curve blows up; a step-by-step that picks the tightest bound.

### 02. Recursion

**Slug:** `recursion`  
**After:** `complexity`, `stacks`  
**Outcome:** Write a recursive function with a base case, a smaller subproblem, and a correct stack story.

A call pushes a frame. The base case returns without another call. Each recursive call must move toward that base, usually by shrinking n or advancing an index. Trust the return value of the smaller call, then combine. If you cannot state the base and the shrink, the function is not ready to write.

Time follows the recurrence. A single call that does O(1) and recurses on n - 1 is O(n). Two calls on n - 1 are O(2^n). A call on n / 2 plus O(1) is O(log n). Depth of the call stack is the space of the recursion. Tail recursion is still a stack of frames unless the language reuses the frame; interviews should count the frames.

**Practice:** factorial, then sum of a list, then a recursive binary-tree max depth.  
**Artifacts:** guide; a game that pushes frames and pops them on return.

### 03. Binary search

**Slug:** `binary-search`  
**After:** `complexity`, `arrays`  
**Outcome:** Search a sorted range in O(log n) and keep the loop invariant intact.

The array, or the answer space, must be sorted in the sense that one side of mid is entirely wrong. Maintain a half-open or closed range and write down which side still holds the answer. Mid is `lo + (hi - lo) / 2`, not `(lo + hi) / 2`, so the sum cannot overflow. Decide whether mid is a possible answer before discarding it. Off-by-one bugs come from discarding mid and from an empty range that the loop still enters.

The same loop finds a boundary: first true in a false-then-true predicate ("minimum speed that works", "first bad version"). That is binary search on the answer, and it is still O(log R) probes where R is the range of answers.

**Practice:** classic search in a sorted array; search a rotated sorted array by deciding which half is sorted; koko eating bananas as search on the answer.  
**Artifacts:** guide; a game that shades the discarded half; a step-by-step that sets lo and hi after each probe.

### 04. Two pointers

**Slug:** `two-pointers`  
**After:** `arrays`  
**Outcome:** Solve a pair or partition problem in one pass by moving two indices toward a goal.

Opposite ends: on a sorted array, left starts at 0 and right at n - 1. If the sum is too small, advance left. If too large, retreat right. Each step throws away a row or a column of the pair table, so the scan is O(n) after the sort. Same direction: a slow index writes the kept items, a fast index reads. That partitions in place.

The array usually needs an order, or the problem must let you build that order. Without a monotone move ("this side only gets better"), two pointers is a guess.

**Practice:** pair sum in a sorted array; container with most water; remove duplicates in place.  
**Artifacts:** guide; a game that moves the two indices and shows the discarded region.

### 05. Sliding window

**Slug:** `sliding-window`  
**After:** `two-pointers`, `hash-maps`  
**Outcome:** Keep a contiguous subarray summary up to date in O(1) or O(k) per move, for O(n) total.

A window is a subarray from left to right. Fixed length k: add the new right element, remove the element that fell off the left, and update the answer. Variable length: grow right until the window is invalid, then advance left until it is valid again. Each index enters once and leaves once, so the scan is O(n) times the cost of the update.

The summary has to be editable from both ends. A count map, a sum, or a deque of candidates works. A structure you must rescan does not.

**Practice:** longest substring without repeating characters; minimum window substring; maximum in a window of size k (deque).  
**Artifacts:** guide; a game that grows and shrinks a highlighted slice; a step-by-step that decides fixed vs variable.

### 06. Sorting

**Slug:** `sorting`  
**After:** `complexity`, `heaps`  
**Outcome:** Choose a sort from the comparison lower bound, stability, and the key range.

Comparison sorts are Ω(n log n). Mergesort hits that bound, uses O(n) extra space, and is stable. Quicksort is O(n log n) average and O(n²) worst; introsort and library sorts guard the worst case. Heapsort is O(n log n) worst and not stable, with O(1) extra space. Timsort (and similar library sorts) is stable and fast on partly ordered data.

If keys are small integers, counting sort is O(n + k) and skips comparisons. Radix sort stacks that by digits. Use them when k is not huge. Do not comparison-sort when the real task is top-k (heap) or a single order statistic that a partition can find.

**Practice:** sort then two-sum; merge two sorted lists; top k frequent (heap or bucket, not a full sort of unique counts unless n is small).  
**Artifacts:** guide; a game that partitions around a pivot; a step-by-step that picks the sort.

### 07. BFS and DFS

**Slug:** `bfs-dfs`  
**After:** `queues`, `stacks`, `graphs`, `trees`  
**Outcome:** Walk every reachable node once, and pick BFS when the edge count is the distance.

Both are O(V + E) with an adjacency list. Mark a node when you first discover it, or you will loop on cycles. DFS goes deep: recursion or an explicit stack. Use it for paths that must be fully explored, for topo sort (finishing times), and for connected components. BFS goes layer by layer with a queue. In an unweighted graph the first time you reach a node is a shortest path in hops.

Store the parent if you must reconstruct the path. Level order on a tree is BFS with the queue size frozen at the start of each layer.

**Practice:** number of islands; shortest path in a binary maze; clone a graph.  
**Artifacts:** guide; a game that shows the frontier of BFS vs the dive of DFS; a step-by-step that picks the walk.

### 08. Backtracking

**Slug:** `backtracking`  
**After:** `recursion`, `bfs-dfs`  
**Outcome:** Search a space of partial decisions by choosing, exploring, and undoing.

State is the choices so far. From a state, try each legal next choice, recurse, then undo so the next sibling starts clean. That undo is the whole technique. Prune when the partial choice already violates a constraint, so you do not explore a dead branch. Time is proportional to the nodes of the decision tree you actually visit, which is often exponential. Say so, and say what you pruned.

Permutations, combinations, subsets, and constraint puzzles (n-queens, sudoku) share this skeleton. The difference is the choice list and the prune.

**Practice:** subsets; permutations; combination sum; n-queens on a small board.  
**Artifacts:** guide; a game that places and removes a queen; a step-by-step that names choose, explore, and undo on one path.

### 09. Greedy

**Slug:** `greedy`  
**After:** `sorting`  
**Outcome:** Take the local choice only when you can say why no earlier choice needs to be revised.

Sort or scan, commit to the best-looking option, and never take it back. That is correct only when a proof exists: an exchange argument (any optimal solution can be rewritten to include your choice without getting worse) or a stay-ahead argument (your partial solution is at least as good as any other at every step). If you cannot sketch that, try dynamic programming or produce a counterexample before coding.

Interval scheduling (earliest finish time), jump game, and Huffman coding are greedy. Coin change with arbitrary denominations is not, unless the denominations are canonical.

**Practice:** earliest-finish interval scheduling; jump game; gas station circuit.  
**Artifacts:** guide; a step-by-step that accepts or rejects a greedy claim with a counterexample.

### 10. Dynamic programming

**Slug:** `dynamic-programming`  
**After:** `recursion`, `greedy`  
**Outcome:** Turn an exponential recursion with overlapping subproblems into a table, and state the recurrence before writing loops.

You need optimal substructure (the best answer uses best answers of smaller states) and overlapping subproblems (the same state is needed many times). Define the state in a sentence: "dp[i] is the best cost using the first i items" or "dp[i][j] is the length of the longest common subsequence of the prefixes". Write the transition and the base cases. Then pick the loop order so each dependency is already filled. Top-down memoization and bottom-up tables compute the same states. Memoization is easier to write from the recursion. The table makes the space optimization obvious.

Start with one dimension: climb stairs, house robber. Then a decision per item: 0/1 knapsack. Then two strings: longest common subsequence, edit distance. Space can drop from two dimensions to two rows, or one row plus a backward scan, when a cell depends only on the previous row.

If a greedy exchange already works, the table is wasted work. If subproblems do not overlap, the recursion was already fine.

**Practice:** climb stairs; house robber; coin change (min coins); longest increasing subsequence; 0/1 knapsack.  
**Artifacts:** guide; a game that fills a grid cell from its dependencies; a step-by-step that writes the state sentence before any code.

### 11. Graph algorithms

**Slug:** `graph-algorithms`  
**After:** `bfs-dfs`, `heaps`, `union-find`  
**Outcome:** Pick the shortest-path or ordering algorithm from the weight and direction constraints.

Topological order exists only in a directed acyclic graph. Kahn's algorithm repeatedly removes a node of indegree 0. A DFS that appends a node after its descendants also produces a reverse topo order. A cycle means some node never reaches indegree 0, or DFS sees a back edge.

Unweighted shortest path is BFS. Non-negative weights: Dijkstra, a BFS where the frontier is a min-heap of distance. It is wrong with negative edges. Negative edges and no negative cycle: Bellman-Ford, O(VE), and the V-th pass detects a negative cycle. All-pairs on a dense graph can be Floyd-Warshall, O(V³). A minimum spanning tree (Kruskal via union-find, or Prim via a heap) connects every vertex at minimum total weight and is not a shortest-path tree.

**Practice:** course schedule (topo); network delay time (Dijkstra); cheapest flights within k stops (Bellman-Ford bounded to k rounds).  
**Artifacts:** guide; a game that relaxes edges on a small graph; a step-by-step that picks BFS, Dijkstra, or Bellman-Ford from the weight sign.

### 12. Intervals

**Slug:** `intervals`  
**After:** `sorting`, `greedy`  
**Outcome:** Sort intervals and sweep a line so merge, overlap, and scheduling become one pass.

Represent an interval as a start and an end. Sort by start. A merge walks left to right and extends the current end when the next start falls inside it, otherwise it emits the current interval and starts a new one. That is O(n log n) for the sort and O(n) for the sweep. Scheduling by earliest finish is the greedy lesson applied to the same objects. A sweep line can also count how many intervals cover a point, by sorting start and end events.

Watch half-open ranges. [1, 3) and [3, 5) do not overlap. [1, 3] and [3, 5] do, if the problem treats endpoints as inclusive.

**Practice:** merge intervals; insert interval; meeting rooms (can attend all; how many rooms).  
**Artifacts:** guide; a game that merges overlapping bars on a line; a step-by-step that sorts by start vs by end for the stated question.
