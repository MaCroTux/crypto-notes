<?php

namespace CryptoNotes\Domain;

interface NotesRepository
{
    public function all(): NotesCollection;

    public function save(Note $note): void;

    public function delete(string $noteId): void;
}