<?php

declare(strict_types=1);

use App\Http\Error\HttpErrorAction;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;
use Whoops\Run as WhoopsRun;

// Start session
session_start();

// Run bootstrap and get di container
$bootstrap = require dirname(__DIR__) . '/config/bootstrap.php';
/** @var ContainerInterface $container */
$container = $bootstrap();

// Create application
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register middleware
$httpMiddleWares = require dirname(__DIR__) . '/config/httpAppMiddlewares.php';
$httpMiddleWares($app);

// Register routes
$routes = require dirname(__DIR__) . '/config/routes.php';
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
