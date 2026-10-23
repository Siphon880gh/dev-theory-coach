<?php
declare(strict_types=1);

/** Dark-canvas SVG helpers matching assets/css/app.css tokens. */
const S_MUTED = '#8b97a5';
const S_TEXT = '#e8eef4';
const S_ACCENT = '#5b9fd4';
const S_FILL = '#1c222b';
const S_EDGE = '#2a323c';
const S_OK = '#6aab8e';
const S_DANGER = '#d97066';
const S_PANEL = '#161b22';

function svg(string $vb, string $inner, int $w = 520, ?int $h = null): string
{
    [$vw, $vh] = array_map('floatval', explode(' ', '0 0 ' . trim(str_replace('0 0 ', '', $vb))));
    if (str_starts_with(trim($vb), '0 0')) {
        $parts = preg_split('/\s+/', trim($vb));
        $vw = (float) ($parts[2] ?? 520);
        $vh = (float) ($parts[3] ?? 160);
    } else {
        $parts = preg_split('/\s+/', trim($vb));
        $vw = (float) ($parts[0] ?? 520);
        $vh = (float) ($parts[1] ?? 160);
        $vb = "0 0 {$vw} {$vh}";
    }
    $height = $h ?? (int) round($w * $vh / max($vw, 1));

    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' . $vb . '" width="' . $w . '" height="' . $height . '" role="img">' . $inner . '</svg>';
}

function esc(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

function label(float $x, float $y, string $t, int $size = 12, string $anchor = 'middle', string $fill = S_TEXT): string
{
    return '<text x="' . $x . '" y="' . $y . '" fill="' . $fill . '" font-size="' . $size . '" font-family="IBM Plex Sans,sans-serif" text-anchor="' . $anchor . '">' . esc($t) . '</text>';
}

function cell(float $x, float $y, float $w, float $h, string $val, string $idx, bool $hi = false, bool $ghost = false): string
{
    $fill = $hi ? 'rgba(91,159,212,0.25)' : S_FILL;
    $stroke = $hi ? S_ACCENT : ($ghost ? S_MUTED : S_EDGE);
    $dash = $ghost ? ' stroke-dasharray="4 3"' : '';
    $out = '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '" rx="4" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1.5"' . $dash . '/>';
    $out .= label($x + $w / 2, $y + $h / 2 + 4, $val, 13);
    $out .= label($x + $w / 2, $y + $h + 14, $idx, 10, 'middle', S_MUTED);

    return $out;
}

function arrow(float $x1, float $y1, float $x2, float $y2, string $color = S_ACCENT): string
{
    return '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="1.8" marker-end="url(#arrowhead)"/>';
}

/** Simple chevron arrow without markers (sanitizer may drop marker defs). */
function arrowLine(float $x1, float $y1, float $x2, float $y2, string $color = S_ACCENT): string
{
    $out = '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="1.8"/>';
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $len = max(hypot($dx, $dy), 0.001);
    $ux = $dx / $len;
    $uy = $dy / $len;
    $px = -$uy;
    $py = $ux;
    $bx = $x2 - $ux * 8;
    $by = $y2 - $uy * 8;
    $out .= '<path d="M ' . $x2 . ' ' . $y2 . ' L ' . ($bx + $px * 4) . ' ' . ($by + $py * 4) . ' L ' . ($bx - $px * 4) . ' ' . ($by - $py * 4) . ' Z" fill="' . $color . '"/>';

    return $out;
}

function box(float $x, float $y, float $w, float $h, string $t, string $stroke = S_EDGE, string $fill = S_FILL): string
{
    $out = '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="' . $h . '" rx="6" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1.5"/>';
    $out .= label($x + $w / 2, $y + $h / 2 + 4, $t, 12);

    return $out;
}

function node(float $cx, float $cy, float $r, string $t, bool $hi = false): string
{
    $fill = $hi ? 'rgba(91,159,212,0.3)' : S_FILL;
    $stroke = $hi ? S_ACCENT : S_EDGE;
    $out = '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1.5"/>';
    $out .= label($cx, $cy + 4, $t, 12);

    return $out;
}

function arrayRow(array $vals, float $x0 = 40, float $y = 40, float $cw = 56, float $ch = 40, ?int $hi = null, array $ghostIdx = []): string
{
    $out = '';
    foreach ($vals as $i => $v) {
        $out .= cell($x0 + $i * ($cw + 6), $y, $cw, $ch, (string) $v, (string) $i, $hi === $i, in_array($i, $ghostIdx, true));
    }

    return $out;
}
