<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Rysunek drzewka bonsai w SVG z danych (pień, gałęzie, korony, donica). Kształty są wypełnione, a nie kreską:
 * pień i gałęzie zwężają się ku końcom, korony to warstwowe, nieregularne „poduszki” z igłami, nasada ma korzenie.
 * Losowość jest deterministyczna (ziarno ze stylu), więc ten sam styl daje zawsze ten sam rysunek.
 * Kolory to klasy `studio-*`; studio.css zamienia je na tokeny motywu.
 *
 * Spec: `pot` (kind shallow|cascade, x, y, w, h), opcjonalnie `stand` (x, w, top, floor), `trunk` (pts, w0, w1, flare),
 * `branches` (pts, w0, w1, back, group), `pads` (cx, cy, rx, ry, rot, tone, branch). `pts` to łańcuch krzywych
 * sześciennych Béziera: punkt, dwa punkty kontrolne, punkt, dwa kontrolne, punkt…
 */
final class BonsaiArt
{
    /**
     * @param array<string, mixed> $spec
     * @param bool $metrics dodaje do każdej korony niewidoczną elipsę `.studio-sim__leaf` (pole i środek liczy skrypt próbnego cięcia)
     */
    public static function tree(array $spec, string $seed, bool $metrics = false): string
    {
        $rand = self::rng($seed);
        $out = '';

        $out .= self::ground($spec);
        $out .= self::pot($spec['pot'], $rand);

        $branches = $spec['branches'];
        $pads = $spec['pads'];
        $drawBranch = function (int $index, array $branch) use ($pads, $seed, $metrics): string {
            $inner = self::ribbon($branch['pts'], $branch['w0'], $branch['w1'], 'studio-limb' . (($branch['back'] ?? false) ? ' studio-limb--back' : ''));
            foreach ($pads as $padIndex => $pad) {
                if (($pad['branch'] ?? null) === $index) {
                    $inner .= self::pad($pad, $seed . '-pad' . $padIndex, $metrics);
                }
            }

            $group = $branch['group'] ?? null;

            return $group === null || !$metrics
                ? $inner
                : '<g class="studio-sim__branch" data-branch="' . (int) $group . '">' . $inner . '</g>';
        };

        // Gałęzie z tyłu (ura-eda) leżą za pniem.
        foreach ($branches as $index => $branch) {
            if ($branch['back'] ?? false) {
                $out .= $drawBranch($index, $branch);
            }
        }

        $out .= self::roots($spec['trunk'], $rand);
        $out .= self::trunk($spec['trunk']);

        foreach ($branches as $index => $branch) {
            if (!($branch['back'] ?? false)) {
                $out .= $drawBranch($index, $branch);
            }
        }

        foreach ($pads as $padIndex => $pad) {
            if (($pad['branch'] ?? null) === null) {
                $out .= self::pad($pad, $seed . '-pad' . $padIndex, $metrics);
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $spec
     */
    private static function ground(array $spec): string
    {
        $pot = $spec['pot'];
        $floor = (float) ($spec['stand']['floor'] ?? ($pot['y'] + $pot['h'] + 4));
        $out = '<ellipse class="studio-ground-shadow" cx="' . self::n($pot['x']) . '" cy="' . self::n($floor) . '" rx="' . self::n($pot['w'] * 0.66) . '" ry="8"/>';

        if (isset($spec['stand'])) {
            $s = $spec['stand'];
            $left = $s['x'] - $s['w'] / 2;
            $out .= '<rect class="studio-stand" x="' . self::n($left + 10) . '" y="' . self::n($s['top']) . '" width="12" height="' . self::n($floor - $s['top']) . '" rx="2"/>'
                . '<rect class="studio-stand" x="' . self::n($left + $s['w'] - 22) . '" y="' . self::n($s['top']) . '" width="12" height="' . self::n($floor - $s['top']) . '" rx="2"/>'
                . '<rect class="studio-stand studio-stand--top" x="' . self::n($left) . '" y="' . self::n($s['top']) . '" width="' . self::n($s['w']) . '" height="11" rx="3"/>';
        }

        return $out;
    }

    /**
     * Donica z krawędzią, nóżkami, ziemią, mchem i kamykami.
     *
     * @param array<string, mixed> $p
     */
    private static function pot(array $p, \Closure $rand): string
    {
        $x = (float) $p['x'];
        $y = (float) $p['y'];
        $w = (float) $p['w'];
        $h = (float) $p['h'];
        $left = $x - $w / 2;
        $right = $x + $w / 2;
        $inset = $w * ($p['kind'] === 'cascade' ? 0.05 : 0.09);
        $bl = $left + $inset;
        $br = $right - $inset;
        $by = $y + $h;

        $out = '<path class="studio-pot" d="M' . self::n($left) . ',' . self::n($y) . ' L' . self::n($right) . ',' . self::n($y)
            . ' L' . self::n($br) . ',' . self::n($by - 7) . ' Q' . self::n($br - 1) . ',' . self::n($by) . ' ' . self::n($br - 12) . ',' . self::n($by)
            . ' L' . self::n($bl + 12) . ',' . self::n($by) . ' Q' . self::n($bl + 1) . ',' . self::n($by) . ' ' . self::n($bl) . ',' . self::n($by - 7) . ' Z"/>'
            . '<path class="studio-pot-shade" d="M' . self::n($x + $w * 0.18) . ',' . self::n($y + 4) . ' L' . self::n($right) . ',' . self::n($y + 4)
            . ' L' . self::n($br) . ',' . self::n($by - 7) . ' Q' . self::n($br - 1) . ',' . self::n($by) . ' ' . self::n($br - 12) . ',' . self::n($by)
            . ' L' . self::n($x + $w * 0.12) . ',' . self::n($by) . ' Z"/>'
            . '<rect class="studio-pot-foot" x="' . self::n($bl + 14) . '" y="' . self::n($by - 1) . '" width="18" height="7" rx="2"/>'
            . '<rect class="studio-pot-foot" x="' . self::n($br - 32) . '" y="' . self::n($by - 1) . '" width="18" height="7" rx="2"/>'
            . '<rect class="studio-pot-rim" x="' . self::n($left - 5) . '" y="' . self::n($y - 5) . '" width="' . self::n($w + 10) . '" height="11" rx="4"/>'
            . '<path class="studio-pot-glint" d="M' . self::n($left + 14) . ',' . self::n($y + 12) . ' L' . self::n($bl + 10) . ',' . self::n($by - 12) . '"/>';

        // Ziemia ułożona w niski pagórek, na nim mech i kamyki.
        $dome = static fn (float $t): float => $y - 1 - 9 * sin(M_PI * $t);
        $out .= '<path class="studio-soil" d="M' . self::n($left + 6) . ',' . self::n($y + 1) . ' C' . self::n($x - $w * 0.25) . ',' . self::n($y - 11) . ' ' . self::n($x + $w * 0.25) . ',' . self::n($y - 11) . ' ' . self::n($right - 6) . ',' . self::n($y + 1) . ' Z"/>';
        for ($i = 0; $i < 9; $i++) {
            $t = 0.06 + 0.88 * ($i + $rand() * 0.6) / 9;
            $mx = $left + 6 + ($w - 12) * $t;
            $out .= '<ellipse class="studio-moss' . ($i % 3 === 0 ? ' studio-moss--light' : '') . '" cx="' . self::n($mx) . '" cy="' . self::n($dome($t) + 1.5) . '" rx="' . self::n(7 + $rand() * 9) . '" ry="' . self::n(2.6 + $rand() * 2.4) . '"/>';
        }
        for ($i = 0; $i < 3; $i++) {
            $t = 0.12 + $rand() * 0.76;
            $out .= '<ellipse class="studio-pebble" cx="' . self::n($left + 6 + ($w - 12) * $t) . '" cy="' . self::n($dome($t) + 3) . '" rx="' . self::n(2.5 + $rand() * 2) . '" ry="1.8"/>';
        }

        return $out;
    }

    /**
     * Korzenie nasady (nebari) rozchodzą się po ziemi od podstawy pnia.
     *
     * @param array<string, mixed> $trunk
     */
    private static function roots(array $trunk, \Closure $rand): string
    {
        [$bx, $by] = $trunk['pts'][0];
        $out = '';
        foreach ([-168, -138, -42, -12] as $angle) {
            $length = 34 + $rand() * 22;
            $dx = cos(deg2rad($angle)) * $length;
            $dy = 3 + $rand() * 3;
            $out .= self::ribbon([[$bx, $by - 2], [$bx + $dx * 0.35, $by - 4], [$bx + $dx * 0.7, $by + $dy * 0.4], [$bx + $dx, $by + $dy]], 11, 2, 'studio-root');
        }

        return $out;
    }

    /**
     * Pień z korą: wypełniony kształt, cień po prawej, światło po lewej i pęknięcia wzdłuż włókien.
     *
     * @param array<string, mixed> $trunk
     */
    private static function trunk(array $trunk): string
    {
        $flare = (float) ($trunk['flare'] ?? 0);
        $samples = self::samples($trunk['pts']);

        return self::ribbon($trunk['pts'], $trunk['w0'], $trunk['w1'], 'studio-bark', $flare)
            . '<path class="studio-bark-shade" d="' . self::offsetLine($samples, $trunk['w0'], $trunk['w1'], $flare, 0.34) . '"/>'
            . '<path class="studio-bark-light" d="' . self::offsetLine($samples, $trunk['w0'], $trunk['w1'], $flare, -0.3) . '"/>'
            . '<path class="studio-bark-crack" d="' . self::offsetLine($samples, $trunk['w0'], $trunk['w1'], $flare, -0.08) . '"/>'
            . '<path class="studio-bark-crack studio-bark-crack--b" d="' . self::offsetLine($samples, $trunk['w0'], $trunk['w1'], $flare, 0.12) . '"/>';
    }

    /**
     * Korona (poduszka): cień pod spodem, trzy warstwy o nieregularnym brzegu i kępki igieł.
     *
     * @param array<string, mixed> $pad
     */
    private static function pad(array $pad, string $seed, bool $metrics): string
    {
        $rand = self::rng($seed);
        [$cx, $cy, $rx, $ry] = [(float) $pad['cx'], (float) $pad['cy'], (float) $pad['rx'], (float) $pad['ry']];
        $rot = (float) ($pad['rot'] ?? 0);
        $tone = preg_replace('/[^a-z]/', '', (string) ($pad['tone'] ?? 'leaf'));

        $out = '<g class="studio-pad studio-pad--' . $tone . '">'
            . '<path class="studio-pad__shadow" d="' . self::blob($cx + 3, $cy + $ry * 0.42, $rx * 0.94, $ry * 0.7, $rot, $rand) . '"/>'
            . '<path class="studio-pad__deep" d="' . self::lobed($cx, $cy, $rx, $ry, $rot, $rand, 1.0) . '"/>'
            . '<path class="studio-pad__mid" d="' . self::lobed($cx - $rx * 0.04, $cy - $ry * 0.16, $rx * 0.86, $ry * 0.8, $rot, $rand, 0.8) . '"/>'
            . '<path class="studio-pad__light" d="' . self::lobed($cx - $rx * 0.12, $cy - $ry * 0.36, $rx * 0.56, $ry * 0.46, $rot, $rand, 0.6) . '"/>'
            . '<path class="studio-pad__tufts studio-pad__tufts--dark" d="' . self::tufts($cx, $cy, $rx, $ry, $rot, $rand, (int) round($rx * 1.6), false, 3.5, 4.5) . '"/>'
            . '<path class="studio-pad__tufts studio-pad__tufts--light" d="' . self::tufts($cx, $cy - $ry * 0.2, $rx * 0.85, $ry * 0.7, $rot, $rand, (int) round($rx * 1.2), true, 3.5, 4) . '"/>';

        if ($metrics) {
            $out .= '<ellipse class="studio-sim__leaf" cx="' . self::n($cx) . '" cy="' . self::n($cy) . '" rx="' . self::n($rx) . '" ry="' . self::n($ry) . '"/>';
        }

        return $out . '</g>';
    }

    /**
     * Kopułka z kilkoma mniejszymi zgrubieniami na górnym brzegu: korona ma „chmurowy” obrys zamiast gładkiej elipsy.
     */
    private static function lobed(float $cx, float $cy, float $rx, float $ry, float $rot, \Closure $rand, float $scale): string
    {
        $cos = cos(deg2rad($rot));
        $sin = sin(deg2rad($rot));
        $d = self::blob($cx, $cy, $rx, $ry, $rot, $rand, 20, 0.2);
        $lobes = 6;
        for ($i = 0; $i < $lobes; $i++) {
            $angle = M_PI + M_PI * ($i + 0.5 + ($rand() - 0.5) * 0.5) / $lobes;
            $px = cos($angle) * $rx * 0.78;
            $py = sin($angle) * $ry * 0.7;
            $lx = $cx + $px * $cos - $py * $sin;
            $ly = $cy + $px * $sin + $py * $cos;
            $d .= ' ' . self::blob($lx, $ly, $rx * (0.2 + 0.1 * $rand()) * $scale + 3, $ry * (0.5 + 0.2 * $rand()), $rot, $rand, 12, 0.22);
        }

        return $d;
    }

    /**
     * Zwężający się kształt wzdłuż krzywej.
     *
     * @param list<array{0: float|int, 1: float|int}> $pts
     */
    private static function ribbon(array $pts, float|int $w0, float|int $w1, string $class, float $flare = 0.0): string
    {
        $samples = self::samples($pts);
        $left = [];
        $right = [];
        foreach ($samples as $s) {
            $half = self::width((float) $w0, (float) $w1, $flare, $s['u']) / 2;
            $left[] = self::n($s['x'] + $s['nx'] * $half) . ',' . self::n($s['y'] + $s['ny'] * $half);
            $right[] = self::n($s['x'] - $s['nx'] * $half) . ',' . self::n($s['y'] - $s['ny'] * $half);
        }

        return '<path class="' . $class . '" d="M' . implode(' L', $left) . ' L' . implode(' L', array_reverse($right)) . ' Z"/>';
    }

    /**
     * Linia równoległa do osi kształtu (np. cień kory), przesunięta o ułamek szerokości.
     *
     * @param list<array{x: float, y: float, nx: float, ny: float, u: float}> $samples
     */
    private static function offsetLine(array $samples, float|int $w0, float|int $w1, float $flare, float $fraction): string
    {
        $points = [];
        foreach ($samples as $s) {
            // Linie kończą się przed czubkiem, gdzie pień jest za cienki na teksturę.
            if ($s['u'] > 0.92) {
                break;
            }
            $offset = self::width((float) $w0, (float) $w1, $flare, $s['u']) / 2 * $fraction;
            $points[] = self::n($s['x'] + $s['nx'] * $offset) . ',' . self::n($s['y'] + $s['ny'] * $offset);
        }

        return 'M' . implode(' L', $points);
    }

    private static function width(float $w0, float $w1, float $flare, float $u): float
    {
        return $w1 + ($w0 - $w1) * ((1 - $u) ** 1.25) + $flare * max(0.0, 1 - $u / 0.12) ** 2;
    }

    /**
     * Próbki łańcucha krzywych Béziera: położenie, normalna i postęp wzdłuż długości (0-1).
     *
     * @param list<array{0: float|int, 1: float|int}> $pts
     * @return list<array{x: float, y: float, nx: float, ny: float, u: float}>
     */
    private static function samples(array $pts, int $perSegment = 18): array
    {
        $out = [];
        $segments = intdiv(count($pts) - 1, 3);

        for ($s = 0; $s < $segments; $s++) {
            [$p0, $p1, $p2, $p3] = [$pts[3 * $s], $pts[3 * $s + 1], $pts[3 * $s + 2], $pts[3 * $s + 3]];
            for ($i = $s === 0 ? 0 : 1; $i <= $perSegment; $i++) {
                $t = $i / $perSegment;
                $mt = 1 - $t;
                $x = $mt ** 3 * $p0[0] + 3 * $mt * $mt * $t * $p1[0] + 3 * $mt * $t * $t * $p2[0] + $t ** 3 * $p3[0];
                $y = $mt ** 3 * $p0[1] + 3 * $mt * $mt * $t * $p1[1] + 3 * $mt * $t * $t * $p2[1] + $t ** 3 * $p3[1];
                $dx = 3 * $mt * $mt * ($p1[0] - $p0[0]) + 6 * $mt * $t * ($p2[0] - $p1[0]) + 3 * $t * $t * ($p3[0] - $p2[0]);
                $dy = 3 * $mt * $mt * ($p1[1] - $p0[1]) + 6 * $mt * $t * ($p2[1] - $p1[1]) + 3 * $t * $t * ($p3[1] - $p2[1]);
                $length = hypot($dx, $dy) ?: 1.0;
                $out[] = ['x' => $x, 'y' => $y, 'nx' => -$dy / $length, 'ny' => $dx / $length, 'u' => 0.0];
            }
        }

        $total = 0.0;
        $along = [0.0];
        for ($i = 1, $count = count($out); $i < $count; $i++) {
            $total += hypot($out[$i]['x'] - $out[$i - 1]['x'], $out[$i]['y'] - $out[$i - 1]['y']);
            $along[$i] = $total;
        }
        foreach ($out as $i => $sample) {
            $out[$i]['u'] = $total > 0 ? $along[$i] / $total : 0.0;
        }

        return $out;
    }

    /**
     * Nieregularny, kopulasty kształt korony z płaskawym spodem.
     */
    private static function blob(float $cx, float $cy, float $rx, float $ry, float $rot, \Closure $rand, int $count = 20, float $jitter = 0.16): string
    {
        $cos = cos(deg2rad($rot));
        $sin = sin(deg2rad($rot));
        $pts = [];
        for ($i = 0; $i < $count; $i++) {
            $angle = 2 * M_PI * $i / $count;
            $radius = (1 - $jitter / 2 + $jitter * $rand()) * (sin($angle) > 0 ? 0.82 : 1.0);
            $px = cos($angle) * $rx * $radius;
            $py = sin($angle) * $ry * $radius;
            $pts[] = [$cx + $px * $cos - $py * $sin, $cy + $px * $sin + $py * $cos];
        }

        $d = 'M' . self::n($pts[0][0]) . ',' . self::n($pts[0][1]);
        for ($i = 0; $i < $count; $i++) {
            $p0 = $pts[($i - 1 + $count) % $count];
            $p1 = $pts[$i];
            $p2 = $pts[($i + 1) % $count];
            $p3 = $pts[($i + 2) % $count];
            $d .= ' C' . self::n($p1[0] + ($p2[0] - $p0[0]) / 6) . ',' . self::n($p1[1] + ($p2[1] - $p0[1]) / 6)
                . ' ' . self::n($p2[0] - ($p3[0] - $p1[0]) / 6) . ',' . self::n($p2[1] - ($p3[1] - $p1[1]) / 6)
                . ' ' . self::n($p2[0]) . ',' . self::n($p2[1]);
        }

        return $d . ' Z';
    }

    /**
     * Kępki igieł: krótkie, lekko wygięte kreski rozchodzące się ku górze i na boki.
     */
    private static function tufts(float $cx, float $cy, float $rx, float $ry, float $rot, \Closure $rand, int $count, bool $upperOnly, float $minLength, float $spread): string
    {
        $cos = cos(deg2rad($rot));
        $sin = sin(deg2rad($rot));
        $d = '';
        for ($i = 0; $i < $count; $i++) {
            for ($try = 0; $try < 8; $try++) {
                $ux = $rand() * 2 - 1;
                $uy = $rand() * 2 - 1;
                if ($ux * $ux + $uy * $uy <= 0.8 && (!$upperOnly || $uy < 0.1)) {
                    break;
                }
            }
            $px = $ux * $rx;
            $py = $uy * $ry * ($uy > 0 ? 0.7 : 1.0);
            $x = $cx + $px * $cos - $py * $sin;
            $y = $cy + $px * $sin + $py * $cos;

            $angle = deg2rad(-90 + ($rand() - 0.5) * 120 + $rot);
            $length = $minLength + $rand() * $spread;
            $ex = $x + cos($angle) * $length;
            $ey = $y + sin($angle) * $length;
            $bend = ($rand() - 0.5) * $length * 0.5;
            $d .= 'M' . self::n($x) . ',' . self::n($y) . ' Q' . self::n(($x + $ex) / 2 + $bend) . ',' . self::n(($y + $ey) / 2) . ' ' . self::n($ex) . ',' . self::n($ey);
        }

        return $d;
    }

    /** Generator liczb pseudolosowych z ziarna (powtarzalny, bez globalnego stanu `mt_rand`). */
    private static function rng(string $seed): \Closure
    {
        $state = (crc32($seed) & 0x7fffffff) ?: 1;

        return static function () use (&$state): float {
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;

            return $state / 0x7fffffff;
        };
    }

    private static function n(float|int $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.') ?: '0';
    }
}
