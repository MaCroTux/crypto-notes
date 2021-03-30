<?php

namespace CryptoNotes\Infrastructure\Jwt;

use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;

class ValidateJwtService
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function __invoke(Token $token): bool
    {
        $exp = (new DateTimeImmutable(null, new DateTimeZone('Europe/Madrid')))
            ->diff($token->claims()->get('exp'));
        $clock = new SystemClock(new DateTimeZone('Europe/Madrid'));

        return $this->configuration->validator()->validate($token, ...[
            //new StrictValidAt($clock, $exp),
            new LooseValidAt($clock, $exp),
        ]);
    }
}