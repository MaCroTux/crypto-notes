<?php

namespace CryptoNotes\Domain;

use Exception;

class CryptOperationException extends Exception
{
    private const MESSAGE = 'Error in crypt operation, check correct password.';

    static function build(): self
    {
        return new self(self::MESSAGE);
    }
}