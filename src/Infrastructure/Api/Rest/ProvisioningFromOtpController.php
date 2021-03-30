<?php


namespace CryptoNotes\Infrastructure\Api\Rest;


use OTPHP\TOTP;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;

class ProvisioningFromOtpController
{
    public function __invoke(Request $request, ResponseInterface $response, $args): ResponseInterface
    {

        $otp = TOTP::create($_ENV['SECRET']);
        $otp->setLabel('NoteCrypt');
        $url = $otp->getProvisioningUri();



        $response->getBody()->write(
            json_encode(
                [
                    'image' => 'https://api.qrserver.com/v1/create-qr-code/?data='.$url.'&size=300x300&ecc=M',
                ]
            )
        );

        return $response;
    }
}