<?php

namespace CryptoNotes;

use CryptoNotes\Domain\CryptInterface;
use CryptoNotes\Domain\CryptOperationException;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class CryptService implements CryptInterface
{
    /**
     * @param string $password
     * @param string $dataEncrypt
     * @return string
     * @throws CryptOperationException
     */
    public function decrypt(string $password, string $dataEncrypt): string
    {
        if (null === $password) {
            return $dataEncrypt;
        }

        $crypto = new Crypto();
        try {
            return $crypto->decryptWithPassword($dataEncrypt, $password);
        } catch (EnvironmentIsBrokenException|WrongKeyOrModifiedCiphertextException $e) {
            throw CryptOperationException::build();
        }
    }

    /**
     * @param string $data
     * @param string|null $password
     * @return string
     * @throws CryptOperationException
     */
    public function encrypt(string $data, ?string $password): string
    {
        if (null === $password) {
            return $data;
        }

        $crypto = new Crypto();

        try {
            return $crypto->encryptWithPassword($data, $password);
        } catch (EnvironmentIsBrokenException $e) {
            throw CryptOperationException::build();
        }
    }
}