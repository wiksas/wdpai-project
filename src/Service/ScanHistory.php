<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserStateRepository;

/**
 * Historia skanów zdjęć na koncie (z Kliniki i z kart okazów): dopisywanie z limitem, najstarsze znikają razem z plikami.
 */
final class ScanHistory
{
    public const MAX = 30;

    public function __construct(
        private readonly UserStateRepository $states,
        private readonly MediaStorage $storage,
    ) {
    }

    /**
     * @param array<string, mixed> $scan
     */
    public function add(int $userId, array $scan): void
    {
        $removed = [];
        $this->states->update($userId, static function (array $state) use ($scan, &$removed): array {
            $state['scans'][] = $scan;
            while (count($state['scans']) > self::MAX) {
                $removed[] = array_shift($state['scans'])['file'] ?? '';
            }

            return $state;
        });

        foreach ($removed as $file) {
            $this->storage->delete($userId, (string) $file);
        }
    }
}
