<?php

declare(strict_types=1);

namespace App\Repository;

/**
 * Czujniki wilgotności podłoża (`sensors`, `sensor_readings`, `docker/db/05-sensors.sql`): jeden czujnik na drzewko,
 * odczyty wysyła urządzenie z tokenem. W bazie jest tylko skrót SHA-256 tokenu; wartości surowe zamieniamy na procenty
 * przy odczycie według kalibracji, więc zmiana kalibracji poprawia też historię.
 */
final class SensorRepository
{
    public const TOKEN_PATTERN = '/^kmr_[a-f0-9]{48}$/';
    private const MIN_INTERVAL_SECONDS = 30;
    private const KEEP_READINGS = 500;

    private readonly string $migrationFile;

    public function __construct()
    {
        $this->migrationFile = dirname(__DIR__, 2) . '/docker/db/05-sensors.sql';
    }

    /**
     * Procent wilgotności z odczytu: suchy (dry) = 0%, mokry (wet) = 100%. Działa dla obu biegunowości czujnika
     * (pojemnościowe dają większą wartość w suchym podłożu). Bez kalibracji zwraca null.
     */
    public static function percent(int $raw, ?int $dry, ?int $wet): ?int
    {
        if ($dry === null || $wet === null || $dry === $wet) {
            return null;
        }

        return (int) round(max(0.0, min(100.0, ($raw - $dry) / ($wet - $dry) * 100)));
    }

    /**
     * Czujnik okazu z ostatnimi odczytami (najnowszy pierwszy) albo null.
     *
     * @return array{id: int, name: string, dryRaw: ?int, wetRaw: ?int, createdAt: int, readings: list<array{raw: int, percent: ?int, at: int}>}|null
     */
    public function forTree(int $userId, int $treeId, int $readings = 8): ?array
    {
        return Database::withMigration($this->migrationFile, function () use ($userId, $treeId, $readings): ?array {
            $select = Database::connection()->prepare('SELECT id, name, dry_raw, wet_raw, extract(epoch FROM created_at)::bigint AS created FROM sensors WHERE user_id = :user AND tree_id = :tree');
            $select->execute(['user' => $userId, 'tree' => $treeId]);
            $sensor = $select->fetch();
            if ($sensor === false) {
                return null;
            }

            $dry = $sensor['dry_raw'] === null ? null : (int) $sensor['dry_raw'];
            $wet = $sensor['wet_raw'] === null ? null : (int) $sensor['wet_raw'];

            $rows = Database::connection()->prepare('SELECT raw, extract(epoch FROM at)::bigint AS at FROM sensor_readings WHERE sensor_id = :id ORDER BY at DESC LIMIT ' . max(1, $readings));
            $rows->execute(['id' => $sensor['id']]);

            return [
                'id' => (int) $sensor['id'],
                'name' => (string) $sensor['name'],
                'dryRaw' => $dry,
                'wetRaw' => $wet,
                'createdAt' => (int) $sensor['created'],
                'readings' => array_map(
                    static fn (array $row): array => ['raw' => (int) $row['raw'], 'percent' => self::percent((int) $row['raw'], $dry, $wet), 'at' => (int) $row['at']],
                    $rows->fetchAll(),
                ),
            ];
        });
    }

    /**
     * Najnowszy skalibrowany odczyt każdego okazu konta: id okazu => odczyt.
     *
     * @return array<int, array{raw: int, percent: ?int, at: int}>
     */
    public function latestForUser(int $userId): array
    {
        return Database::withMigration($this->migrationFile, function () use ($userId): array {
            $select = Database::connection()->prepare(
                'SELECT s.tree_id, s.dry_raw, s.wet_raw, r.raw, extract(epoch FROM r.at)::bigint AS at
                 FROM sensors s
                 JOIN LATERAL (SELECT raw, at FROM sensor_readings WHERE sensor_id = s.id ORDER BY at DESC LIMIT 1) r ON true
                 WHERE s.user_id = :user',
            );
            $select->execute(['user' => $userId]);

            $latest = [];
            foreach ($select->fetchAll() as $row) {
                $latest[(int) $row['tree_id']] = [
                    'raw' => (int) $row['raw'],
                    'percent' => self::percent((int) $row['raw'], $row['dry_raw'] === null ? null : (int) $row['dry_raw'], $row['wet_raw'] === null ? null : (int) $row['wet_raw']),
                    'at' => (int) $row['at'],
                ];
            }

            return $latest;
        });
    }

    /**
     * Zakłada czujnik okazu (albo wystawia nowy token istniejącemu, zachowując kalibrację i odczyty).
     * Zwraca token w postaci jawnej; jest pokazywany użytkownikowi tylko raz.
     */
    public function issueToken(int $userId, int $treeId, string $name): string
    {
        $token = 'kmr_' . bin2hex(random_bytes(24));

        Database::withMigration($this->migrationFile, function () use ($userId, $treeId, $name, $token): void {
            Database::connection()->prepare(
                'INSERT INTO sensors (user_id, tree_id, name, token_hash) VALUES (:user, :tree, :name, :hash)
                 ON CONFLICT (user_id, tree_id) DO UPDATE SET token_hash = EXCLUDED.token_hash, name = CASE WHEN EXCLUDED.name = \'\' THEN sensors.name ELSE EXCLUDED.name END',
            )->execute(['user' => $userId, 'tree' => $treeId, 'name' => $name, 'hash' => hash('sha256', $token)]);
        });

        return $token;
    }

    public function delete(int $userId, int $treeId): void
    {
        Database::withMigration($this->migrationFile, function () use ($userId, $treeId): void {
            Database::connection()->prepare('DELETE FROM sensors WHERE user_id = :user AND tree_id = :tree')
                ->execute(['user' => $userId, 'tree' => $treeId]);
        });
    }

    /**
     * Zapisuje ostatni odczyt jako punkt kalibracji (`dry` = 0%, `wet` = 100%). False, gdy czujnik nie ma jeszcze odczytu.
     */
    public function calibrate(int $userId, int $treeId, string $point): bool
    {
        $column = $point === 'dry' ? 'dry_raw' : 'wet_raw';

        return Database::withMigration($this->migrationFile, function () use ($userId, $treeId, $column): bool {
            $update = Database::connection()->prepare(
                "UPDATE sensors SET {$column} = (SELECT raw FROM sensor_readings WHERE sensor_id = sensors.id ORDER BY at DESC LIMIT 1)
                 WHERE user_id = :user AND tree_id = :tree AND EXISTS (SELECT 1 FROM sensor_readings WHERE sensor_id = sensors.id)",
            );
            $update->execute(['user' => $userId, 'tree' => $treeId]);

            return $update->rowCount() > 0;
        });
    }

    /**
     * Przyjmuje odczyt od urządzenia. Zwraca `unknown` (zły token), `rate` (za szybko) albo `ok` z procentem (null bez kalibracji).
     *
     * @return array{status: string, percent: ?int}
     */
    public function record(string $token, int $raw): array
    {
        return Database::withMigration($this->migrationFile, function () use ($token, $raw): array {
            $pdo = Database::connection();
            $select = $pdo->prepare(
                'SELECT s.id, s.dry_raw, s.wet_raw, EXTRACT(EPOCH FROM (now() - (SELECT max(at) FROM sensor_readings WHERE sensor_id = s.id))) AS since
                 FROM sensors s WHERE s.token_hash = :hash',
            );
            $select->execute(['hash' => hash('sha256', $token)]);
            $sensor = $select->fetch();

            if ($sensor === false) {
                return ['status' => 'unknown', 'percent' => null];
            }
            if ($sensor['since'] !== null && (float) $sensor['since'] < self::MIN_INTERVAL_SECONDS) {
                return ['status' => 'rate', 'percent' => null];
            }

            $pdo->prepare('INSERT INTO sensor_readings (sensor_id, raw) VALUES (:id, :raw)')->execute(['id' => $sensor['id'], 'raw' => $raw]);
            $pdo->prepare(
                'DELETE FROM sensor_readings WHERE sensor_id = :id AND at < (SELECT at FROM sensor_readings WHERE sensor_id = :id2 ORDER BY at DESC OFFSET ' . self::KEEP_READINGS . ' LIMIT 1)',
            )->execute(['id' => $sensor['id'], 'id2' => $sensor['id']]);

            return [
                'status' => 'ok',
                'percent' => self::percent($raw, $sensor['dry_raw'] === null ? null : (int) $sensor['dry_raw'], $sensor['wet_raw'] === null ? null : (int) $sensor['wet_raw']),
            ];
        });
    }
}
