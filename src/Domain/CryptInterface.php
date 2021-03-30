<?php

namespace CryptoNotes\Domain;

interface CryptInterface
{
    /**
     * @param string $password
     * @param string $dataEncrypt
     * @return string
     * @throws CryptOperationException
     */
    public function decrypt(string $password, string $dataEncrypt): string;

    /**
     * @param string $data
     * @param string|null $password
     * @return string
     * @throws CryptOperationException
     */
    public function encrypt(string $data, ?string $password): string;
}