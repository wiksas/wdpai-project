<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Zdjęcia użytkowników trafiają do `storage/uploads/{userId}/` (poza `public/`) i są serwowane tylko właścicielowi
 * przez `GET /media/{nazwa}`. Typ pliku weryfikujemy po zawartości (finfo), nie po rozszerzeniu.
 */
final class MediaStorage
{
    public const MAX_BYTES = 2 * 1024 * 1024;
    private const TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_SIDE = 6000;

    private readonly string $root;

    public function __construct(?string $root = null)
    {
        $this->root = $root ?? dirname(__DIR__, 2) . '/storage/uploads';
    }

    /**
     * Sprawdza wgrany plik ze `$_FILES` bez zapisywania go. Zwraca ścieżkę pliku tymczasowego albo rzuca wyjątek z komunikatem dla użytkownika.
     *
     * @param array<string, mixed> $file element `$_FILES['pole']`
     */
    public function validateUpload(array $file): string
    {
        return $this->validate($file)['path'];
    }

    /**
     * Zapisuje wgrany plik ze `$_FILES`. Zwraca nazwę pliku (np. `a1b2…c3.jpg`) albo rzuca wyjątek z komunikatem dla użytkownika.
     *
     * @param array<string, mixed> $file element `$_FILES['pole']`
     */
    public function store(int $userId, array $file): string
    {
        ['path' => $path, 'mime' => $mime] = $this->validate($file);

        $directory = $this->directory($userId);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Nie można utworzyć katalogu na zdjęcia.');
        }

        $name = bin2hex(random_bytes(12)) . '.' . self::TYPES[$mime];
        if (!move_uploaded_file($path, $directory . '/' . $name)) {
            throw new \RuntimeException('Nie można zapisać zdjęcia.');
        }

        return $name;
    }

    /**
     * Pełna ścieżka pliku użytkownika albo null, gdy nazwa jest niepoprawna lub plik nie istnieje.
     */
    public function path(int $userId, string $name): ?string
    {
        if (preg_match('/^[a-f0-9]{24}\.(jpg|png|webp)$/', $name) !== 1) {
            return null;
        }

        $path = $this->directory($userId) . '/' . $name;

        return is_file($path) ? $path : null;
    }

    public function mime(string $name): string
    {
        return match (pathinfo($name, PATHINFO_EXTENSION)) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    public function delete(int $userId, string $name): void
    {
        $path = $this->path($userId, $name);
        if ($path !== null) {
            @unlink($path);
        }
    }

    /**
     * @param array<string, mixed> $file
     * @return array{path: string, mime: string}
     */
    private function validate(array $file): array
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new \InvalidArgumentException('Zdjęcie jest zbyt duże (maks. 2 MB).');
        }

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
            throw new \InvalidArgumentException('Nie udało się odebrać pliku. Wybierz zdjęcie i spróbuj ponownie.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_BYTES) {
            throw new \InvalidArgumentException('Zdjęcie jest zbyt duże (maks. 2 MB).');
        }

        $path = (string) $file['tmp_name'];
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $info = @getimagesize($path);

        if (!is_string($mime) || !isset(self::TYPES[$mime]) || $info === false) {
            throw new \InvalidArgumentException('Dozwolone są tylko zdjęcia JPG, PNG lub WebP.');
        }

        if ($info[0] > self::MAX_SIDE || $info[1] > self::MAX_SIDE) {
            throw new \InvalidArgumentException('Zdjęcie ma zbyt dużą rozdzielczość.');
        }

        return ['path' => $path, 'mime' => $mime];
    }

    private function directory(int $userId): string
    {
        return $this->root . '/' . $userId;
    }
}
