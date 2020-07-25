<?php

declare(strict_types=1);

use App\Http\Error\HttpErrorAction;
use Config\RegisterEventListeners;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;
use Whoops\Run as WhoopsRun;

// Start session
session_start();

// Run bootstrap and get di container
$bootstrap = require dirname(__DIR__) . '/config/bootstrap.php';
$container = $bootstrap();
assert($container instanceof ContainerInterface);

// Register event listeners
$container->get(RegisterEventListeners::class)();

// Create application
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register middleware
$httpMiddleWares = require dirname(__DIR__) . '/config/httpAppMiddlewares.php';
$httpMiddleWares($app);

$app->addBodyParsingMiddleware();

// Register routes
$routes = require dirname(__DIR__) . '/config/Routes/index.php';
$routes($app);

// Use factory to get the ServerRequest
$request = ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();

// Register error handlers is Whoops does not exists
if (! class_exists(WhoopsRun::class)) {
    $errorMiddleware = $app->addErrorMiddleware(false, false, false);
    $errorMiddleware->setDefaultErrorHandler($container->get(HttpErrorAction::class));
}

// Emit response from app
$responseEmitter = $container->get(ResponseEmitter::class);
$responseEmitter->emit($app->handle($request));
