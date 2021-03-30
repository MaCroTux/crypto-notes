<?php

use CryptoNotes\Infrastructure\Api\Rest\ProvisioningFromOtpController;
use CryptoNotes\Infrastructure\Factory\AddNoteUseCaseFactory;
use CryptoNotes\Infrastructure\Factory\DeleteNoteUseCaseFactory;
use CryptoNotes\Infrastructure\Factory\ListNotesAndDecryptUseCaseFactory;
use CryptoNotes\CryptService;
use CryptoNotes\Infrastructure\Api\Rest\AddNoteController;
use CryptoNotes\Infrastructure\Api\Rest\AddPasswordController;
use CryptoNotes\Infrastructure\Api\Rest\CreateTokenFromOptController;
use CryptoNotes\Infrastructure\Api\Rest\DeleteNoteController;
use CryptoNotes\Infrastructure\Api\Rest\IndexController;
use CryptoNotes\Infrastructure\Api\Rest\Middleware\TokenValidateMiddleware;
use CryptoNotes\Infrastructure\Jwt\ValidateJwtService;
use DI\Container;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
$dotenv->safeLoad();

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$jwtConfiguration = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::plainText($_ENV['SECRET'])
);

$container->set(
    TokenValidateMiddleware::class,
    new TokenValidateMiddleware(new ValidateJwtService($jwtConfiguration))
);

$container->set(
    IndexController::class,
    new IndexController(
        ListNotesAndDecryptUseCaseFactory::create($_ENV['NOTE_FILE']),
        new CryptService()
    )
);

$container->set(
    AddNoteController::class,
    new AddNoteController(
        AddNoteUseCaseFactory::create($_ENV['NOTE_FILE']),
        new CryptService()
    )
);

$container->set(
    DeleteNoteController::class,
    new DeleteNoteController(
        DeleteNoteUseCaseFactory::create($_ENV['NOTE_FILE'])
    )
);

$container->set(
    AddPasswordController::class,
    new AddPasswordController(new CryptService())
);

$container->set(
    CreateTokenFromOptController::class,
    new CreateTokenFromOptController($jwtConfiguration)
);

$container->set(
    ProvisioningFromOtpController::class,
    new ProvisioningFromOtpController()
);

$app->get('/{password}', IndexController::class)
    ->add(TokenValidateMiddleware::class);
$app->get('/password/add/{password}', AddPasswordController::class)
    ->add(TokenValidateMiddleware::class);

$app->post('/note/add', AddNoteController::class)
    ->add(TokenValidateMiddleware::class);
$app->get('/note/delete/{id}', DeleteNoteController::class)
    ->add(TokenValidateMiddleware::class);

$app->get('/otp/validate/{pin}', CreateTokenFromOptController::class);
$app->get('/otp/provisioning', ProvisioningFromOtpController::class)
    ->add(TokenValidateMiddleware::class);

$app->run();
