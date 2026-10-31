<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Jednorazowe komunikaty (np. „Zapisano podlanie”) pokazywane po przekierowaniu jako toasty.
 */
final class Flash
{
    private const KEY = 'flash_messages';

    public function __construct(private readonly Session $session)
    {
    }

    /**
     * @param 'success'|'info'|'error' $type
     */
    public function add(string $type, string $message): void
    {
        $messages = $this->session->get(self::KEY, []);
        $messages[] = ['type' => $type, 'message' => $message];
        $this->session->set(self::KEY, $messages);
    }

    /**
     * @return list<array{type: string, message: string}>
     */
    public function pull(): array
    {
        $messages = $this->session->pull(self::KEY, []);

        return is_array($messages) ? array_values($messages) : [];
    }
}
