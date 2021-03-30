<?php

namespace CryptoNotes\Infrastructure\Persistence\File;

use CryptoNotes\Domain\Note;
use CryptoNotes\Domain\NotesCollection;
use CryptoNotes\Domain\NotesRepository;

class JsonNotesRepository implements NotesRepository
{
    private NotesCollection $notes;
    private string $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->notes = $this->readRawFile();
    }

    private function readRawFile(): NotesCollection
    {
        if (!file_exists($this->fileName)) {
            file_put_contents($this->fileName, '[]');
        }

        $rawFile = file_get_contents($this->fileName);
        $notesArray = json_decode($rawFile, true);

        if (empty($notesArray)) {
            return NotesCollection::buildFromEmpty();
        }

        $notes = array_map(
            function (array $note) {
                return $this->arrayToNote($note);
            },
            $notesArray
        );

        return NotesCollection::build(...$notes);
    }

    private function arrayToNote(array $note): Note
    {
        return Note::fromArray($note);
    }

    public function all(): NotesCollection
    {
        return $this->notes;
    }

    public function save(Note $note): void
    {
        $this->notes->push($note);

        $this->saveRawFile();
    }

    public function delete(string $noteId): void
    {
        $this->notes->delete($noteId);

        $this->saveRawFile();
    }

    private function saveRawFile(): void
    {
        $rawNotes = json_encode($this->notes->toArray());

        file_put_contents($this->fileName, $rawNotes);
    }
}