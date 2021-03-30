<?php

namespace CryptoNotes\Domain;

class NotesCollection implements \Iterator
{
    /** @var Note[] */
    private array $notes;
    private int $index = 0;

    private function __construct(array $notes)
    {
        $this->notes = $notes;
    }

    public static function build(Note ...$notes): self
    {
        return new self($notes);
    }

    public static function buildFromEmpty(): self
    {
        return new self([]);
    }

    public function push(Note $note): void
    {
        $this->notes[] = $note;
    }

    public function delete(string $id): void
    {
        $this->notes = array_filter(
            $this->notes,
            static function (Note $note) use ($id) {
                return $note->id() !== $id;
            }
        );
    }

    public function toArray(): array
    {
        return array_map(
            static function(Note $note) {
                return $note->toArray();
            },
            $this->notes
        );
    }

    /**
     * @return Note|mixed
     */
    public function current()
    {
        return $this->notes[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key()
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return !empty($this->notes[$this->key()]);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}