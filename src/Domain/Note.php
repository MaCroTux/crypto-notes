<?php

namespace CryptoNotes\Domain;

use DateTimeImmutable;

class Note
{
    private string $id;
    private string $name;
    private string $contents;
    private DateTimeImmutable $createAt;
    private bool $visible;

    private function __construct(string $id, string $name, string $contents, DateTimeImmutable $createAt, bool $visible)
    {
        $this->id = $id;
        $this->name = $name;
        $this->contents = $contents;
        $this->createAt = $createAt;
        $this->visible = $visible;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function createAt(): DateTimeImmutable
    {
        return $this->createAt;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function withDecryptContents(string $decryptContents): self
    {
        return new self(
            $this->id,
            $this->name,
            $decryptContents,
            $this->createAt,
            true
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contents' => true === $this->visible ? $this->contents : '...',
            'create_at' => $this->createAt->getTimestamp(),
            'visible' => $this->visible,
        ];
    }

    public static function fromArray(array $note) {
        return new Note(
            $note['id'] ?? uniqid(),
            $note['name'] ?? '',
            $note['contents'] ?? '',
            new \DateTimeImmutable('@' . $note['create_at'] ?? ''),
            true
        );
    }

    public static function fromNoVisible(array $note) {
        return new Note(
            $note['id'] ?? uniqid(),
            $note['name'] ?? '',
            $note['contents'] ?? '',
            new \DateTimeImmutable('@' . $note['create_at'] ?? ''),
            false
        );
    }

    public static function build(string $id, string $name, string $contents, bool $visible = true): self
    {
        return new self($id, $name, $contents, new DateTimeImmutable(), $visible);
    }
}