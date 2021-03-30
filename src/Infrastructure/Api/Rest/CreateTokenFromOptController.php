<?php

namespace CryptoNotes\Infrastructure\Api\Rest;

use DateTimeImmutable;
use DateTimeZone;
use Lcobucci\JWT\Configuration;
use OTPHP\TOTP;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class CreateTokenFromOptController
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function __invoke(Request $request, ResponseInterface $response, $args): ResponseInterface
    {
        $pin = $args['pin'] ?? null;

        $otp = TOTP::create($_ENV['SECRET']);
        if (!$otp->verify($pin)) {
            $response->getBody()->write(
                json_encode(
                    [
                        'result' => 'fail',
                        'message' => 'OTP verify error',
                    ]
                )
            );

            return $response;
        }

        $token = $this->configuration
            ->builder()
            ->identifiedBy(uniqid())
            ->relatedTo(md5(uniqid()))
            ->issuedAt(new DateTimeImmutable(null, new DateTimeZone('Europe/Madrid')))
            ->expiresAt(new DateTimeImmutable('+30 days', new DateTimeZone('Europe/Madrid')))
            ->getToken($this->configuration->signer(), $this->configuration->signingKey());

        setcookie('token', $token->toString() ?? null, time() + (60 * 60 * 24), "/", $_ENV['HOST'], false, true);

        $response->getBody()->write(
            json_encode(
                [
                   'token' => $token->toString()
                ]
            )
        );

        return $response;
    }
}