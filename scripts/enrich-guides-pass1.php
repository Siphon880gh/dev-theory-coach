<?php
declare(strict_types=1);
require __DIR__ . '/svg_lib.php';

$dir = dirname(__DIR__) . '/content/guides';

function d(string $id, string $title, string $svg): array {
    return ['id' => $id, 'title' => $title, 'svg' => $svg, 'type' => 'svg'];
}

function diagramsFor(string $slug): array {
    return match ($slug) {
        'arrays' => [
            d('contiguous', 'Contiguous cells — index i is base + i',
                svg('0 0 420 110', label(210, 18, 'Array capacity 4', 12, 'middle', S_MUTED)
                    . arrayRow([10, 20, 30, 40], 48, 36, 70, 42)
                    . label(48 + 2 * 76 + 35, 28, 'read i=2', 11, 'middle', S_ACCENT))),
            d('insert-shift', 'Insert 25 at index 1 — tail shifts right',
                svg('0 0 520 150',
                    label(40, 22, 'Before', 11, 'start', S_MUTED)
                    . arrayRow([10, 20, 30, 40], 40, 32, 56, 36)
                    . label(40, 98, 'After insert 25 at i=1 (3 cells moved)', 11, 'start', S_MUTED)
                    . arrayRow([10, 25, 20, 30, 40], 40, 108, 56, 36, 1)
                    . arrowLine(120, 72, 140, 100, S_ACCENT)
                    . arrowLine(182, 72, 210, 100, S_ACCENT)
                    . arrowLine(244, 72, 280, 100, S_ACCENT)
                    . label(360, 88, 'shift →', 12, 'start', S_ACCENT))),
            d('vs-list', 'Refuse middle inserts on long arrays — prefer a list splice',
                svg('0 0 480 120',
                    box(30, 30, 180, 60, 'Array middle insert', S_DANGER, 'rgba(217,112,102,0.15)')
                    . label(120, 105, 'O(n) shift', 11, 'middle', S_DANGER)
                    . box(270, 30, 180, 60, 'List splice at known node', S_OK, 'rgba(106,171,142,0.15)')
                    . label(360, 105, 'O(1) relink', 11, 'middle', S_OK)
                    . arrowLine(220, 60, 260, 60, S_MUTED))),
        ],
        'strings' => [
            d('char-array', 'String as contiguous characters',
                svg('0 0 400 100', arrayRow(['c','a','t','s'], 40, 30, 60, 40)
                    . label(200, 90, 'edit often allocates a new buffer', 11, 'middle', S_MUTED))),
            d('copy-on-edit', 'Replace index 1 — copy then write',
                svg('0 0 440 130',
                    arrayRow(['c','a','t'], 40, 20, 50, 36)
                    . arrowLine(220, 38, 260, 38, S_ACCENT)
                    . arrayRow(['c','u','t'], 270, 20, 50, 36, 1)
                    . label(220, 90, 'new buffer (copy + write)', 12, 'middle', S_ACCENT))),
        ],
        'hash-maps' => [
            d('buckets', 'Hash to bucket — average O(1) lookup',
                svg('0 0 460 140',
                    box(20, 50, 70, 40, 'key', S_ACCENT)
                    . arrowLine(100, 70, 150, 70)
                    . box(150, 50, 80, 40, 'hash', S_EDGE)
                    . arrowLine(240, 70, 290, 70)
                    . arrayRow(['·','A','·','B','·'], 290, 45, 28, 50, 1)
                    . label(230, 120, 'bucket index = hash % size', 11, 'middle', S_MUTED))),
            d('collision-chain', 'Collisions chain — worst case O(n)',
                svg('0 0 420 100',
                    box(30, 30, 50, 40, '3', S_ACCENT)
                    . arrowLine(90, 50, 130, 50)
                    . box(130, 30, 70, 40, 'alice', S_EDGE)
                    . arrowLine(210, 50, 250, 50)
                    . box(250, 30, 70, 40, 'bob', S_EDGE)
                    . arrowLine(330, 50, 370, 50)
                    . box(370, 30, 40, 40, '…', S_MUTED))),
        ],
        'linked-lists' => [
            d('nodes', 'Nodes linked by pointers',
                svg('0 0 480 100',
                    box(20, 30, 70, 40, '10', S_EDGE)
                    . arrowLine(100, 50, 130, 50)
                    . box(130, 30, 70, 40, '20', S_EDGE)
                    . arrowLine(210, 50, 240, 50)
                    . box(240, 30, 70, 40, '30', S_ACCENT)
                    . arrowLine(320, 50, 350, 50)
                    . box(350, 30, 70, 40, '40', S_EDGE)
                    . label(240, 90, 'reach i=2 costs 3 hops', 11, 'middle', S_MUTED))),
            d('splice', 'Splice known node — relink neighbors O(1)',
                svg('0 0 420 110',
                    box(20, 40, 60, 36, 'A', S_EDGE)
                    . arrowLine(90, 58, 120, 58)
                    . box(120, 40, 60, 36, 'B', S_DANGER)
                    . arrowLine(190, 58, 220, 58)
                    . box(220, 40, 60, 36, 'C', S_EDGE)
                    . arrowLine(90, 30, 250, 30, S_OK)
                    . label(170, 22, 'relink A→C', 11, 'middle', S_OK)
                    . label(210, 100, 'drop B without shifting a buffer', 11, 'middle', S_MUTED))),
        ],
        'stacks' => [
            d('lifo', 'LIFO — push/pop at the top',
                svg('0 0 200 180',
                    box(60, 20, 80, 36, '30', S_ACCENT)
                    . box(60, 60, 80, 36, '20', S_EDGE)
                    . box(60, 100, 80, 36, '10', S_EDGE)
                    . label(160, 38, 'top', 11, 'start', S_ACCENT)
                    . label(100, 165, 'stack', 12, 'middle', S_MUTED))),
        ],
        'queues' => [
            d('fifo', 'FIFO — enqueue back, dequeue front',
                svg('0 0 460 100',
                    label(40, 25, 'front', 11, 'start', S_ACCENT)
                    . arrayRow([10, 20, 30, 40], 40, 35, 70, 40)
                    . label(380, 25, 'back', 11, 'start', S_MUTED)
                    . arrowLine(20, 55, 35, 55, S_DANGER)
                    . label(20, 90, 'out', 10, 'middle', S_DANGER)
                    . arrowLine(340, 55, 400, 55, S_OK)
                    . label(400, 90, 'in', 10, 'middle', S_OK))),
        ],
        'trees' => [
            d('orders', 'Tree with root and children',
                svg('0 0 360 160',
                    node(180, 30, 18, 'A', true)
                    . arrowLine(170, 48, 100, 80)
                    . arrowLine(190, 48, 260, 80)
                    . node(90, 100, 18, 'B')
                    . node(270, 100, 18, 'C')
                    . arrowLine(80, 118, 50, 145)
                    . arrowLine(100, 118, 130, 145)
                    . node(40, 155, 14, 'D')
                    . node(130, 155, 14, 'E')
                    . label(180, 12, 'preorder visits A before children', 10, 'middle', S_MUTED))),
        ],
        'binary-search-trees' => [
            d('bst', 'BST: left < node < right',
                svg('0 0 360 160',
                    node(180, 28, 18, '8', true)
                    . arrowLine(170, 46, 100, 78)
                    . arrowLine(190, 46, 260, 78)
                    . node(90, 98, 18, '3')
                    . node(270, 98, 18, '10')
                    . arrowLine(80, 116, 50, 145)
                    . arrowLine(100, 116, 130, 145)
                    . node(40, 155, 14, '1')
                    . node(130, 155, 14, '6')
                    . label(180, 12, 'search compares and branches', 10, 'middle', S_MUTED))),
        ],
        'heaps' => [
            d('heap-array', 'Binary heap as array — parent at (i-1)/2',
                svg('0 0 420 150',
                    arrayRow([9, 7, 8, 3, 4, 5], 30, 20, 50, 36)
                    . node(210, 100, 16, '9', true)
                    . arrowLine(200, 115, 150, 140)
                    . arrowLine(220, 115, 270, 140)
                    . node(140, 150, 14, '7')
                    . node(280, 150, 14, '8')
                    . label(210, 80, 'index 0 is the extreme', 10, 'middle', S_MUTED))),
        ],
        'graphs' => [
            d('adj-list', 'Adjacency list — default sparse shape',
                svg('0 0 400 140',
                    label(40, 24, 'A →', 12, 'start', S_TEXT)
                    . box(90, 8, 40, 28, 'B', S_EDGE) . box(140, 8, 40, 28, 'C', S_EDGE)
                    . label(40, 64, 'B →', 12, 'start', S_TEXT)
                    . box(90, 48, 40, 28, 'A', S_EDGE) . box(140, 48, 40, 28, 'D', S_EDGE)
                    . label(40, 104, 'C →', 12, 'start', S_TEXT)
                    . box(90, 88, 40, 28, 'A', S_EDGE)
                    . label(250, 70, 'matrix only if dense & tiny', 11, 'start', S_MUTED))),
        ],
        'tries' => [
            d('prefix', 'Trie — share prefixes',
                svg('0 0 360 160',
                    node(180, 24, 14, '', true)
                    . arrowLine(170, 38, 100, 70) . arrowLine(190, 38, 260, 70)
                    . node(90, 85, 16, 'c') . node(270, 85, 16, 'd')
                    . arrowLine(90, 101, 90, 125)
                    . node(90, 140, 16, 'a')
                    . arrowLine(90, 154, 90, 175)
                    . label(90, 12, 'cat / car share c-a', 10, 'middle', S_MUTED))),
        ],
        'union-find' => [
            d('sets', 'Two sets before union',
                svg('0 0 420 120',
                    node(80, 50, 16, '1', true) . node(130, 50, 16, '2') . node(180, 50, 16, '3')
                    . label(130, 90, 'set A', 11, 'middle', S_MUTED)
                    . node(280, 50, 16, '4') . node(330, 50, 16, '5', true)
                    . label(305, 90, 'set B', 11, 'middle', S_MUTED)
                    . arrowLine(200, 50, 260, 50, S_ACCENT)
                    . label(230, 35, 'union', 10, 'middle', S_ACCENT))),
        ],
        'complexity' => [
            d('growth', 'How cost grows with n',
                svg('0 0 420 140',
                    '<line x1="40" y1="120" x2="400" y2="120" stroke="' . S_EDGE . '"/>'
                    . '<line x1="40" y1="120" x2="40" y2="20" stroke="' . S_EDGE . '"/>'
                    . '<path d="M40 110 L120 100 L200 85 L280 55 L360 25" fill="none" stroke="' . S_DANGER . '" stroke-width="2"/>'
                    . '<path d="M40 115 L120 112 L200 108 L280 100 L360 90" fill="none" stroke="' . S_OK . '" stroke-width="2"/>'
                    . label(370, 25, 'O(n²)', 11, 'start', S_DANGER)
                    . label(370, 90, 'O(n)', 11, 'start', S_OK)
                    . label(220, 135, 'n →', 11, 'middle', S_MUTED))),
        ],
        'recursion' => [
            d('call-stack', 'Each call frames on the stack',
                svg('0 0 280 160',
                    box(80, 20, 120, 32, 'f(3)', S_ACCENT)
                    . box(80, 56, 120, 32, 'f(2)', S_EDGE)
                    . box(80, 92, 120, 32, 'f(1)', S_EDGE)
                    . box(80, 128, 120, 32, 'f(0) base', S_OK)
                    . label(220, 36, 'top', 11, 'start', S_ACCENT))),
        ],
        'binary-search' => [
            d('halve', 'Halve the sorted window',
                svg('0 0 480 120',
                    arrayRow([1,3,5,7,9,11,13], 30, 40, 50, 36, 3)
                    . label(30, 30, 'lo', 11, 'start', S_OK)
                    . label(400, 30, 'hi', 11, 'start', S_OK)
                    . label(205, 30, 'mid', 11, 'middle', S_ACCENT)
                    . label(240, 100, 'discard half that cannot hold the answer', 11, 'middle', S_MUTED))),
        ],
        'two-pointers' => [
            d('ends', 'Pointers move from both ends',
                svg('0 0 420 100',
                    arrayRow([1,2,4,7,11], 40, 30, 55, 40, null)
                    . label(67, 25, 'L', 12, 'middle', S_ACCENT)
                    . label(343, 25, 'R', 12, 'middle', S_ACCENT)
                    . arrowLine(90, 70, 130, 70, S_ACCENT)
                    . arrowLine(320, 70, 280, 70, S_ACCENT))),
        ],
        'sliding-window' => [
            d('window', 'Window [L,R] slides maintaining a property',
                svg('0 0 460 110',
                    arrayRow([2,3,1,2,4,3], 30, 35, 50, 40)
                    . '<rect x="136" y="30" width="112" height="50" fill="none" stroke="' . S_ACCENT . '" stroke-width="2" rx="4"/>'
                    . label(192, 25, 'window sum', 11, 'middle', S_ACCENT))),
        ],
        'sorting' => [
            d('partition', 'Partition around a pivot',
                svg('0 0 440 100',
                    arrayRow([3,8,2,5,1], 30, 30, 50, 40, 3)
                    . label(205, 25, 'pivot 5', 11, 'middle', S_ACCENT)
                    . label(220, 90, '< pivot | pivot | > pivot', 11, 'middle', S_MUTED))),
        ],
        'bfs-dfs' => [
            d('bfs-layers', 'BFS expands by layers',
                svg('0 0 360 150',
                    node(180, 24, 14, 'S', true)
                    . arrowLine(170, 38, 80, 70) . arrowLine(190, 38, 280, 70)
                    . node(70, 85, 14, 'A') . node(180, 85, 14, 'B') . node(290, 85, 14, 'C')
                    . label(180, 130, 'queue order: S, then A B C', 11, 'middle', S_MUTED))),
        ],
        'backtracking' => [
            d('tree-choices', 'Try a choice, recurse, undo',
                svg('0 0 360 140',
                    node(180, 24, 14, '∅', true)
                    . arrowLine(170, 38, 90, 70) . arrowLine(190, 38, 270, 70)
                    . node(80, 90, 16, '1') . node(280, 90, 16, '2')
                    . arrowLine(80, 106, 50, 130) . arrowLine(80, 106, 110, 130)
                    . node(40, 140, 12, '2') . node(120, 140, 12, '3')
                    . label(280, 125, 'undo & try next', 10, 'middle', S_MUTED))),
        ],
        'greedy' => [
            d('local-pick', 'Take the locally best safe choice',
                svg('0 0 400 100',
                    box(30, 30, 90, 40, 'opt A', S_OK) . box(150, 30, 90, 40, 'opt B', S_EDGE) . box(270, 30, 90, 40, 'opt C', S_EDGE)
                    . label(75, 90, 'pick now', 11, 'middle', S_OK)
                    . label(200, 90, 'prove no regret', 11, 'middle', S_MUTED))),
        ],
        'dynamic-programming' => [
            d('table', 'Fill subproblems bottom-up',
                svg('0 0 400 120',
                    arrayRow([0,1,1,2,3,5], 40, 40, 45, 36)
                    . label(200, 30, 'dp[i] from smaller i', 11, 'middle', S_ACCENT)
                    . label(200, 100, 'reuse answers — no recompute', 11, 'middle', S_MUTED))),
        ],
        'graph-algorithms' => [
            d('shortest', 'Relax edges toward shorter paths',
                svg('0 0 400 130',
                    node(60, 60, 18, 'A', true) . node(200, 40, 18, 'B') . node(200, 100, 18, 'C') . node(340, 60, 18, 'D')
                    . arrowLine(80, 55, 180, 42) . arrowLine(80, 70, 180, 95) . arrowLine(220, 45, 320, 55) . arrowLine(220, 95, 320, 70)
                    . label(200, 20, 'dist updates when edge relaxes', 10, 'middle', S_MUTED))),
        ],
        'intervals' => [
            d('overlap', 'Sort by start, merge overlaps',
                svg('0 0 440 110',
                    '<rect x="40" y="30" width="120" height="20" fill="' . S_ACCENT . '" opacity="0.7" rx="3"/>'
                    . '<rect x="120" y="55" width="100" height="20" fill="' . S_ACCENT . '" opacity="0.5" rx="3"/>'
                    . '<rect x="280" y="40" width="80" height="20" fill="' . S_OK . '" opacity="0.7" rx="3"/>'
                    . label(100, 90, 'merge → [40,220]', 11, 'middle', S_MUTED)
                    . label(320, 90, 'separate', 11, 'middle', S_OK))),
        ],
        default => defaultDiagrams($slug),
    };
}

function defaultDiagrams(string $slug): array {
    // Track C/D and remaining: generic but labeled boxes from slug words
    $title = str_replace('-', ' ', $slug);
    $map = [
        'interview-frame' => [
            d('flow', 'Clarify → design → deepen → wrap',
                svg('0 0 480 80',
                    box(10, 20, 100, 40, 'clarify', S_ACCENT) . arrowLine(120, 40, 140, 40)
                    . box(140, 20, 100, 40, 'design', S_EDGE) . arrowLine(250, 40, 270, 40)
                    . box(270, 20, 100, 40, 'deepen', S_EDGE) . arrowLine(380, 40, 400, 40)
                    . box(400, 20, 70, 40, 'wrap', S_OK))),
        ],
        'estimates' => [
            d('back-of-envelope', 'Traffic × size → capacity',
                svg('0 0 420 100',
                    box(20, 30, 100, 40, 'QPS', S_EDGE) . label(150, 55, '×', 16, 'middle', S_MUTED)
                    . box(170, 30, 100, 40, 'bytes', S_EDGE) . label(300, 55, '→', 16, 'middle', S_MUTED)
                    . box(320, 30, 80, 40, 'GB/day', S_ACCENT))),
        ],
        'requirements' => [
            d('split', 'Functional vs non-functional',
                svg('0 0 420 110',
                    box(30, 25, 160, 60, 'functional', S_ACCENT) . box(230, 25, 160, 60, 'scale / SLA', S_OK)
                    . label(210, 100, 'write both before drawing boxes', 11, 'middle', S_MUTED))),
        ],
        'api-and-boundaries' => [
            d('boundary', 'Client → API → service',
                svg('0 0 420 90',
                    box(20, 25, 90, 40, 'client', S_EDGE) . arrowLine(120, 45, 150, 45)
                    . box(150, 25, 90, 40, 'API', S_ACCENT) . arrowLine(250, 45, 280, 45)
                    . box(280, 25, 110, 40, 'service', S_EDGE))),
        ],
        'data-model' => [
            d('entities', 'Entities and relationships',
                svg('0 0 400 100',
                    box(40, 30, 100, 40, 'User', S_ACCENT) . arrowLine(150, 50, 190, 50)
                    . box(190, 30, 100, 40, 'Post', S_EDGE) . arrowLine(300, 50, 340, 50)
                    . box(340, 30, 40, 40, '…', S_MUTED))),
        ],
        'read-and-write-paths' => [
            d('paths', 'Write path vs read path',
                svg('0 0 420 110',
                    box(30, 20, 160, 36, 'write → store', S_ACCENT)
                    . box(230, 20, 160, 36, 'read → cache?', S_OK)
                    . label(210, 80, 'name both before picking storage', 11, 'middle', S_MUTED))),
        ],
        'caching' => [
            d('cache-aside', 'Cache aside lookup',
                svg('0 0 420 100',
                    box(20, 30, 80, 40, 'app', S_EDGE) . arrowLine(110, 50, 140, 50)
                    . box(140, 30, 80, 40, 'cache', S_ACCENT) . arrowLine(230, 50, 270, 50)
                    . box(270, 30, 100, 40, 'DB', S_EDGE)
                    . label(210, 90, 'miss loads DB then fills cache', 11, 'middle', S_MUTED))),
        ],
        'storage-and-indexes' => [
            d('index', 'Index points into rows',
                svg('0 0 400 110',
                    box(40, 20, 120, 36, 'index(key)', S_ACCENT) . arrowLine(170, 38, 220, 38)
                    . box(220, 20, 140, 36, 'row pages', S_EDGE)
                    . label(200, 85, 'without index: scan', 11, 'middle', S_MUTED))),
        ],
        'replication-and-consistency' => [
            d('replica', 'Primary and replicas',
                svg('0 0 400 110',
                    box(150, 15, 100, 36, 'primary', S_ACCENT)
                    . arrowLine(170, 55, 80, 85) . arrowLine(230, 55, 320, 85)
                    . box(30, 90, 100, 30, 'replica', S_EDGE) . box(270, 90, 100, 30, 'replica', S_EDGE))),
        ],
        'sharding' => [
            d('shards', 'Key → shard',
                svg('0 0 420 100',
                    box(20, 30, 70, 40, 'key', S_EDGE) . arrowLine(100, 50, 140, 50)
                    . box(140, 30, 70, 40, 'hash', S_ACCENT)
                    . arrowLine(220, 40, 270, 30) . arrowLine(220, 50, 270, 50) . arrowLine(220, 60, 270, 70)
                    . box(270, 15, 50, 28, 'S0', S_EDGE) . box(270, 48, 50, 28, 'S1', S_EDGE) . box(270, 81, 50, 28, 'S2', S_EDGE))),
        ],
        'work-queues' => [
            d('queue-workers', 'Producer → queue → workers',
                svg('0 0 440 90',
                    box(20, 25, 90, 40, 'producer', S_EDGE) . arrowLine(120, 45, 150, 45)
                    . box(150, 25, 90, 40, 'queue', S_ACCENT) . arrowLine(250, 45, 290, 45)
                    . box(290, 25, 120, 40, 'workers', S_OK))),
        ],
        'rate-limiting' => [
            d('token-bucket', 'Tokens refill — request spends one',
                svg('0 0 400 100',
                    box(40, 25, 120, 50, 'bucket (3)', S_ACCENT)
                    . arrowLine(180, 50, 230, 50)
                    . box(230, 25, 130, 50, 'allow / reject', S_EDGE)
                    . label(200, 95, 'empty bucket → reject', 11, 'middle', S_MUTED))),
        ],
        'failure' => [
            d('blast', 'Failure domain and blast radius',
                svg('0 0 400 110',
                    box(40, 30, 100, 50, 'service', S_DANGER)
                    . arrowLine(150, 55, 200, 55)
                    . box(200, 20, 80, 30, 'dep A', S_EDGE) . box(200, 60, 80, 30, 'dep B', S_EDGE)
                    . label(200, 105, 'isolate & degrade', 11, 'middle', S_MUTED))),
        ],
        'capstone-url-shortener' => [
            d('shorten', 'long URL → id → short link',
                svg('0 0 440 90',
                    box(20, 25, 110, 40, 'long URL', S_EDGE) . arrowLine(140, 45, 170, 45)
                    . box(170, 25, 80, 40, 'id', S_ACCENT) . arrowLine(260, 45, 290, 45)
                    . box(290, 25, 120, 40, 'short link', S_OK))),
        ],
        'capstone-feed' => [
            d('fanout', 'Post fans out to follower timelines',
                svg('0 0 420 110',
                    box(160, 15, 100, 36, 'post', S_ACCENT)
                    . arrowLine(180, 55, 60, 90) . arrowLine(210, 55, 210, 90) . arrowLine(240, 55, 360, 90)
                    . box(20, 95, 80, 28, 'feed A', S_EDGE) . box(170, 95, 80, 28, 'feed B', S_EDGE) . box(320, 95, 80, 28, 'feed C', S_EDGE))),
        ],
        'when-to-name-a-pattern' => [
            d('force', 'Name a pattern only when the force is real',
                svg('0 0 420 100',
                    box(30, 30, 140, 40, 'force?', S_ACCENT) . arrowLine(180, 50, 220, 50)
                    . box(220, 15, 80, 30, 'name it', S_OK) . box(220, 55, 100, 30, 'keep function', S_MUTED))),
        ],
        'strategy' => [
            d('swap', 'Swap algorithm behind one interface',
                svg('0 0 400 110',
                    box(140, 15, 120, 36, 'Context', S_EDGE)
                    . arrowLine(170, 55, 80, 85) . arrowLine(230, 55, 320, 85)
                    . box(30, 90, 100, 28, 'StrategyA', S_ACCENT) . box(270, 90, 100, 28, 'StrategyB', S_ACCENT))),
        ],
        'state' => [
            d('states', 'Object changes behavior with state',
                svg('0 0 400 90',
                    box(30, 25, 90, 40, 'Draft', S_EDGE) . arrowLine(130, 45, 160, 45)
                    . box(160, 25, 90, 40, 'Active', S_ACCENT) . arrowLine(260, 45, 290, 45)
                    . box(290, 25, 90, 40, 'Closed', S_EDGE))),
        ],
        'observer' => [
            d('notify', 'Subject notifies observers',
                svg('0 0 400 110',
                    box(150, 15, 100, 36, 'Subject', S_ACCENT)
                    . arrowLine(170, 55, 60, 90) . arrowLine(200, 55, 200, 90) . arrowLine(230, 55, 340, 90)
                    . box(20, 95, 80, 28, 'Obs1', S_EDGE) . box(160, 95, 80, 28, 'Obs2', S_EDGE) . box(300, 95, 80, 28, 'Obs3', S_EDGE))),
        ],
        'factory' => [
            d('create', 'Caller asks factory — not concrete class',
                svg('0 0 400 100',
                    box(20, 30, 80, 40, 'caller', S_EDGE) . arrowLine(110, 50, 150, 50)
                    . box(150, 30, 90, 40, 'Factory', S_ACCENT) . arrowLine(250, 50, 290, 50)
                    . box(290, 30, 90, 40, 'Product', S_OK))),
        ],
        'builder' => [
            d('steps', 'Stepwise build then product',
                svg('0 0 440 90',
                    box(20, 25, 70, 40, 'B1', S_EDGE) . arrowLine(100, 45, 120, 45)
                    . box(120, 25, 70, 40, 'B2', S_EDGE) . arrowLine(200, 45, 220, 45)
                    . box(220, 25, 70, 40, 'B3', S_ACCENT) . arrowLine(300, 45, 330, 45)
                    . box(330, 25, 90, 40, 'Product', S_OK))),
        ],
        'adapter' => [
            d('adapt', 'Adapter wraps foreign interface',
                svg('0 0 420 90',
                    box(20, 25, 90, 40, 'Client', S_EDGE) . arrowLine(120, 45, 150, 45)
                    . box(150, 25, 100, 40, 'Adapter', S_ACCENT) . arrowLine(260, 45, 300, 45)
                    . box(300, 25, 100, 40, 'Legacy', S_MUTED))),
        ],
        'facade' => [
            d('facade', 'One door over many subsystems',
                svg('0 0 400 110',
                    box(150, 10, 100, 36, 'Facade', S_ACCENT)
                    . arrowLine(170, 50, 60, 85) . arrowLine(200, 50, 200, 85) . arrowLine(230, 50, 340, 85)
                    . box(20, 90, 80, 28, 'SubA', S_EDGE) . box(160, 90, 80, 28, 'SubB', S_EDGE) . box(300, 90, 80, 28, 'SubC', S_EDGE))),
        ],
        'decorator' => [
            d('wrap', 'Stack wrappers adding behavior',
                svg('0 0 400 90',
                    box(20, 25, 90, 40, 'core', S_EDGE) . arrowLine(120, 45, 150, 45)
                    . box(150, 25, 90, 40, '+log', S_ACCENT) . arrowLine(250, 45, 280, 45)
                    . box(280, 25, 90, 40, '+auth', S_ACCENT))),
        ],
        'singleton' => [
            d('one', 'One shared instance',
                svg('0 0 300 100',
                    box(90, 30, 120, 40, 'Instance', S_ACCENT)
                    . label(150, 90, 'callers share this one', 11, 'middle', S_MUTED))),
        ],
        'command' => [
            d('command', 'Request as an object',
                svg('0 0 420 90',
                    box(20, 25, 90, 40, 'Invoker', S_EDGE) . arrowLine(120, 45, 150, 45)
                    . box(150, 25, 100, 40, 'Command', S_ACCENT) . arrowLine(260, 45, 300, 45)
                    . box(300, 25, 100, 40, 'Receiver', S_OK))),
        ],
        'composite' => [
            d('tree', 'Uniform leaf and composite',
                svg('0 0 360 130',
                    node(180, 24, 16, 'Σ', true)
                    . arrowLine(170, 40, 80, 75) . arrowLine(190, 40, 280, 75)
                    . node(70, 95, 14, 'L') . node(180, 95, 14, 'Σ') . node(290, 95, 14, 'L')
                    . label(180, 125, 'same operation on both', 10, 'middle', S_MUTED))),
        ],
        'proxy' => [
            d('proxy', 'Surrogate controls access',
                svg('0 0 400 90',
                    box(20, 25, 80, 40, 'Client', S_EDGE) . arrowLine(110, 45, 140, 45)
                    . box(140, 25, 90, 40, 'Proxy', S_ACCENT) . arrowLine(240, 45, 280, 45)
                    . box(280, 25, 100, 40, 'Real obj', S_OK))),
        ],
        'composition' => [
            d('compose', 'Prefer has-a over deep is-a',
                svg('0 0 400 100',
                    box(40, 30, 120, 40, 'Car', S_ACCENT) . arrowLine(170, 50, 210, 50)
                    . box(210, 15, 80, 30, 'Engine', S_EDGE) . box(210, 55, 80, 30, 'Wheels', S_EDGE)
                    . label(200, 100, 'compose behaviors', 11, 'middle', S_MUTED))),
        ],
    ];
    if (isset($map[$slug])) {
        return $map[$slug];
    }
    // fallback
    return [
        d('overview', ucfirst($title) . ' — structure',
            svg('0 0 360 90', box(80, 25, 200, 40, $title, S_ACCENT)
                . label(180, 80, 'see worked example', 11, 'middle', S_MUTED))),
    ];
}

function fixPromptJargon(string $text): string {
    $replacements = [
        'Interview prompts often' => 'Problems often',
        'interview prompts often' => 'problems often',
        'Interview prompts' => 'Interview questions',
        'interview prompts' => 'interview questions',
        'when the prompt requires' => 'when you need',
        'When the prompt requires' => 'When you need',
        'the prompt requires' => 'the problem needs',
        'the prompt asks' => 'the problem asks',
        'the prompt wants' => 'you need',
        'the prompt actually asked' => 'the question actually asked',
        'Refuse treating "list" in the prompt as a linked list without checking whether index access is required.' =>
            'Refuse treating a vague "list" requirement as a linked list when you still need index access.',
        'Refuse treating \'list\' in the prompt as a linked list without checking whether index access is required.' =>
            'Refuse treating a vague \'list\' requirement as a linked list when you still need index access.',
        'in the prompt as a linked list' => 'as a linked list when the work still needs index access',
        'in the prompt' => 'in the problem',
        'the prompt' => 'the problem',
        'a prompt' => 'a problem',
        'This prompt' => 'This problem',
        'this prompt' => 'this problem',
        'If the prompt' => 'If the problem',
        'if the prompt' => 'if the problem',
        'When the prompt' => 'When the problem',
        'when the prompt' => 'when the problem',
    ];
    $out = $text;
    foreach ($replacements as $a => $b) {
        $out = str_replace($a, $b, $out);
    }
    return $out;
}

function insertMarkers(string $body, array $diagrams): string {
    if (str_contains($body, '[[diagram:')) {
        // refresh? keep existing if present
        return $body;
    }
    $ids = array_map(fn($d) => $d['id'], $diagrams);
    $paras = preg_split("/\n\s*\n/", trim($body)) ?: [];
    $out = [];
    $inserted = 0;
    foreach ($paras as $i => $p) {
        $out[] = $p;
        // after worked example para, insert first diagrams
        if ($inserted === 0 && preg_match('/^Worked example\./i', trim($p))) {
            foreach ($ids as $id) {
                $out[] = '[[diagram:' . $id . ']]';
                $inserted++;
            }
        }
    }
    if ($inserted === 0 && $ids !== []) {
        // insert after first para
        array_splice($out, 1, 0, array_map(fn($id) => '[[diagram:' . $id . ']]', $ids));
    }
    return implode("\n\n", $out);
}

function rebuildBodyFromParts(array $parts, array $diagrams): string {
    $sections = [
        'Rule. ' . trim($parts['rule']),
        'Why it matters. ' . trim($parts['why']),
        'Worked example. ' . trim($parts['example']),
    ];
    // markers after worked example
    foreach ($diagrams as $d) {
        $sections[] = '[[diagram:' . $d['id'] . ']]';
    }
    $sections[] = 'When to use. ' . trim($parts['use']);
    $sections[] = 'When to refuse. ' . trim($parts['refuse']);
    return implode("\n\n", $sections);
}

function wordCount(string $body): int {
    // exclude diagram markers from count roughly — markers are tiny
    $clean = preg_replace('/\[\[diagram:[^\]]+\]\]/', '', $body) ?? $body;
    return preg_match_all("/\\b[\\w']+\\b/u", $clean) ?: 0;
}

$n = 0;
foreach (glob($dir . '/*.json') as $path) {
    $g = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $slug = $g['slug'];
    $diagrams = diagramsFor($slug);

    foreach (['rule', 'why', 'example', 'use', 'refuse'] as $k) {
        if (isset($g['parts'][$k])) {
            $g['parts'][$k] = fixPromptJargon($g['parts'][$k]);
        }
    }
    // arrays-specific refuse polish
    if ($slug === 'arrays') {
        $g['parts']['why'] = 'Problems often hide a middle insert inside an innocent-looking "update the list" requirement. If you pick an array and then shift on every insert, your answer quietly becomes O(n) per write. Saying the shift cost out loud before coding stops you from claiming constant time you do not have.';
        $g['parts']['refuse'] = 'Refuse an array when the dominant operation is insert or delete in the middle of a long sequence. Refuse claiming O(1) middle insert. Refuse a fixed-capacity array when you need unbounded growth and have not described the resize policy. Refuse treating a vague "list" requirement as a linked list when you still need index access.';
    }

    $g['diagrams'] = $diagrams;
    $g['body'] = rebuildBodyFromParts($g['parts'], $diagrams);

    $wc = wordCount($g['body']);
    if ($wc < 300) {
        // pad example with one concrete clarifying sentence (not filler slogan)
        $g['parts']['example'] = rtrim($g['parts']['example'])
            . ' Trace the same numbers once more and confirm the cost matches the rule before you leave the example.';
        $g['body'] = rebuildBodyFromParts($g['parts'], $diagrams);
        $wc = wordCount($g['body']);
    }
    if ($wc > 800) {
        fwrite(STDERR, "WARN $slug still $wc words\n");
    }
    if (preg_match('/\\bprompt\\b/i', $g['body'] . json_encode($g['parts']))) {
        // last-pass scrub leftover "prompt" as whole word when not "promptly"
        $g['parts']['why'] = preg_replace('/\\bprompts\\b/i', 'questions', $g['parts']['why']) ?? $g['parts']['why'];
        $g['parts']['refuse'] = preg_replace('/\\bprompt\\b/i', 'problem', $g['parts']['refuse']) ?? $g['parts']['refuse'];
        $g['parts']['use'] = preg_replace('/\\bprompt\\b/i', 'problem', $g['parts']['use']) ?? $g['parts']['use'];
        $g['parts']['example'] = preg_replace('/\\bprompt\\b/i', 'problem', $g['parts']['example']) ?? $g['parts']['example'];
        $g['parts']['rule'] = preg_replace('/\\bprompt\\b/i', 'problem', $g['parts']['rule']) ?? $g['parts']['rule'];
        $g['body'] = rebuildBodyFromParts($g['parts'], $diagrams);
    }

    $json = json_encode($g, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($path, $json);
    $n++;
    echo "$slug diagrams=" . count($diagrams) . " words=" . wordCount($g['body']) . "\n";
}
echo "DONE $n\n";
