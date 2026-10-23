# Data structures

The shapes data lives in, what each operation costs, and which shape fits the access pattern. Algorithms come after this track.

## Order

1. `arrays`
2. `strings`
3. `hash-maps`
4. `linked-lists`
5. `stacks`
6. `queues`
7. `trees`
8. `binary-search-trees`
9. `heaps`
10. `graphs`
11. `tries`
12. `union-find`

## Lessons

### 01. Arrays

**Slug:** `arrays`  
**After:** none  
**Outcome:** Use an array when the job is index access, and say what insert and delete in the middle cost.

An array is a contiguous block. The address of index `i` is base plus `i` times the element width, so read and write by index are O(1). Insert or delete in the middle shifts the tail, so that is O(n). Append is amortized O(1) when the buffer grows by doubling. A fixed array does not grow. A dynamic array copies into a larger buffer when it fills.

Scan is O(n). Search in an unsorted array is O(n). Search in a sorted array can be binary search, which the algorithms track teaches.

**Practice:** reverse in place; remove duplicates from a sorted array by overwriting.  
**Artifacts:** guide; a game that shifts cells on a middle insert; a step-by-step that chooses array vs list.

### 02. Strings

**Slug:** `strings`  
**After:** `arrays`  
**Outcome:** Treat a string as an array of characters, and know when a language copies on edit.

Indexing a character is O(1). A slice often copies. Building a string by repeated append can be O(n²) if each append copies, so accumulate in a buffer and join once. Comparisons are O(k) in the common prefix length. Immutability means "change" allocates a new string.

**Practice:** palindrome check with two indices; anagram check via a count array of 26 or a map.  
**Artifacts:** guide; a game that joins a buffer vs copies on each append.

### 03. Hash maps

**Slug:** `hash-maps`  
**After:** `arrays`  
**Outcome:** Pick a hash map for lookup by key, and separate average cost from worst-case cost.

A hash function sends a key to a bucket. Two keys can share a bucket; the bucket holds a short chain, or the table probes another slot. When the load factor is high the table resizes. One put then costs more than the others, and the average across many puts stays O(1). Worst-case get, put, and delete are O(n) if every key collides.

Use a map for counts, indexes, and "have I seen this key". Use a set when the value is only presence. If the next step needs sorted keys, a hash map is the wrong shape.

**Practice:** frequency count; Two Sum by storing value to index.  
**Artifacts:** guide; a game that drops keys into buckets and shows a collision; a step-by-step that chooses map, sort, or scan.

### 04. Linked lists

**Slug:** `linked-lists`  
**After:** `arrays`  
**Outcome:** Insert or delete at a known node in O(1), and refuse a linked list when the job is "jump to index i".

Each node holds a value and a pointer to the next node. A doubly linked node also points back. There is no index math, so reaching the i-th node is O(n). Once the node is in hand, splicing it in or out is O(1). A dummy head removes the empty-list special case.

The classic pairing is a hash map plus a doubly linked list: the map finds the node, the list reorders it. That is the LRU cache shape. Teach the shape here; the design-systems cache lesson can point back.

**Practice:** reverse a singly linked list; detect a cycle with slow and fast pointers.  
**Artifacts:** guide; a game that splices a node; a step-by-step for LRU move-to-front.

### 05. Stacks

**Slug:** `stacks`  
**After:** `arrays`  
**Outcome:** Use a stack when the last item in must be the first item out.

Push and pop are O(1) on an array end or a list head. Peek is O(1). The call stack is this structure: a function call pushes a frame, a return pops it.

Use a stack for nested pairs (parentheses), for undo, and for depth-first walk. A monotonic stack keeps indices in sorted order of their values and answers "nearest greater element" in one pass. The algorithms track uses that pattern; the structure lesson only needs to make the stack itself boring and reliable.

**Practice:** valid parentheses; daily temperatures via a monotonic stack of indices.  
**Artifacts:** guide; a game that pushes and pops brackets; a step-by-step that picks stack vs queue.

### 06. Queues

**Slug:** `queues`  
**After:** `stacks`  
**Outcome:** Use a queue when the first item in must be the first item out, and a deque when both ends move.

Enqueue and dequeue are O(1). Breadth-first search is a queue: neighbors enter at the back and leave from the front, so nearer nodes run first. A deque (double-ended queue) supports O(1) add and remove at both ends. Sliding-window maximum keeps candidates in a deque, dropping indexes that can no longer win.

Two stacks can implement a queue. Say which stack is the inbox and which is the outbox, and when you pour one into the other.

**Practice:** implement a queue with two stacks; binary tree level order.  
**Artifacts:** guide; a game that shows BFS layers leaving a queue; a step-by-step for deque vs queue.

### 07. Trees

**Slug:** `trees`  
**After:** `queues`  
**Outcome:** Walk a binary tree in preorder, inorder, postorder, and level order, and say what each order is for.

A binary tree node has a value, a left child, and a right child. There is no balance promise yet. Depth is the edge count from the root. Height is the edge count on the longest downward path. A skewed tree is a list and costs O(n) to walk down. A complete tree of n nodes has height floor(log n).

Preorder visits the node before its children (copy a tree, serialize). Inorder on a binary search tree yields sorted keys. Postorder visits children first (delete a tree, evaluate an expression). Level order is BFS.

Recursion uses the call stack. The same three orders can be written with an explicit stack. Level order needs the queue from the previous lesson.

**Practice:** max depth; invert a binary tree; check same tree.  
**Artifacts:** guide; a game that lights nodes in the chosen order; a step-by-step that picks the order for a stated job.

### 08. Binary search trees

**Slug:** `binary-search-trees`  
**After:** `trees`  
**Outcome:** Search, insert, and delete in a BST in O(h), and say what h becomes without balancing.

Every key in the left subtree is less than the node, and every key in the right subtree is greater. Search follows one child per step, so it is O(h). Insert walks the same path and attaches a leaf. Delete has three cases: no child, one child, or two children. The two-child case copies the inorder successor (or predecessor) into the node and deletes that successor.

Sorted inserts build a stick, h = n. A balanced tree (AVL or red-black) keeps h = O(log n). Name that guarantee. Do not derive rotations until the learner can already search an unbalanced tree.

**Practice:** validate a BST against a lo/hi range, not against the parent only; lowest common ancestor in a BST.  
**Artifacts:** guide; a game that inserts sorted keys and watches the stick form; a step-by-step for the three delete cases.

### 09. Heaps

**Slug:** `heaps`  
**After:** `trees`  
**Outcome:** Take the min or max in O(1) and fix the heap in O(log n), using an array as the tree.

A binary heap is a complete binary tree stored in an array. Parent of `i` is `(i - 1) / 2`. Children are `2i + 1` and `2i + 2`. The heap property: a parent is ≤ its children (min-heap) or ≥ them (max-heap). There is no order among siblings, so a heap is not a BST and an inorder walk is not sorted.

Peek is O(1). Push sifts up. Pop removes the root, moves the last leaf to the root, and sifts down. Both are O(log n). Heapify of n items is O(n), not O(n log n), because most nodes sit near the leaves. A priority queue is this interface.

Use a heap for top-k, merging k sorted lists, and scheduling by priority. Use a sort if you need every item in order once.

**Practice:** kth largest via a min-heap of size k; merge k sorted lists.  
**Artifacts:** guide; a game that sifts a value up an array drawing; a step-by-step that chooses heap vs sort vs BST.

### 10. Graphs

**Slug:** `graphs`  
**After:** `queues`  
**Outcome:** Represent a graph as an adjacency list, and state the cost of listing neighbors.

A graph is vertices plus edges. An edge may be directed. A weight is a number on an edge. An adjacency list maps each vertex to its neighbors and is the default: space O(V + E), listing neighbors of u is proportional to deg(u). An adjacency matrix is V by V, O(1) edge tests, O(V²) space. Use the matrix only when the graph is tiny and dense or you need constant-time "is there an edge".

A path does not repeat vertices. A cycle returns to a vertex. Connected (undirected) means a path exists between every pair. Reachable (directed) is the same idea from a start vertex.

Store the list. Traversals and shortest paths live in the algorithms track (`bfs-dfs`, `graph-algorithms`).

**Practice:** build an adjacency list from an edge array; count connected components with BFS or DFS.  
**Artifacts:** guide; a game that toggles list vs matrix and shows the space; a step-by-step that picks the representation.

### 11. Tries

**Slug:** `tries`  
**After:** `trees`  
**Outcome:** Store strings so prefix queries cost O(L) in the string length, not O(n) in the dictionary size.

Each edge is a character. A node marks whether a word ends there. Insert, search, and starts-with walk one edge per character, O(L). Space is proportional to the number of stored characters, with sharing on shared prefixes. A hash map of whole strings answers exact lookup and does not answer "words starting with pre" without a scan.

Children can be a fixed array of 26 or a map. The array is faster and hungrier for sparse alphabets.

**Practice:** implement insert, search, and starts-with; word search on a board can wait for backtracking.  
**Artifacts:** guide; a game that inserts words and highlights a shared prefix.

### 12. Union-find

**Slug:** `union-find`  
**After:** `graphs`  
**Outcome:** Merge two sets and test whether two items share a set, in nearly constant time.

Each item starts as its own parent. Find follows parents to the root. Union links one root under the other. Path compression points nodes at the root during find. Union by rank (or size) keeps the tree short. Together those make each operation effectively O(1) for every input an interview will use. The true bound is inverse Ackermann; say "almost O(1)" and name the two tricks.

This answers connectivity as edges arrive, and cycle detection in an undirected graph: if find(u) == find(v) before union, the edge closes a cycle. It does not store the path, only the component.

**Practice:** number of provinces; redundant connection (the edge that closes a cycle).  
**Artifacts:** guide; a game that unions two nodes and compresses a path; a step-by-step that chooses union-find vs BFS for an online connectivity stream.
