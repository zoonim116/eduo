<?php
use Medoo\Medoo;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Slim\Flash\Messages;
use \App\Controllers;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \App\Validation\Validator;
use Respect\Validation\Validator as v;
use Dotenv\Dotenv;
use \Slim\Csrf\Guard;
// DIC configuration

$container = $app->getContainer();


$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['view'] = function($container) {
    $settings = $container->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
    $view->addExtension(new \App\AppExtension());
    return $view;
};

$container['flash'] = function () {
    return new Messages();
};

$container['HomeController'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\HomeController ($view);
};

$container['UserController'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\UserController($view);
};
$container['RepositoryController'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\RepositoryController($view);
};

$container['TextController'] = function($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\TextController($view);
};

$container['validator'] = function ($c) {
    return new Validator;
};

$container['flash'] = function ($c) {
    return new Slim\Flash\Messages;
};


$container['csrf'] = function ($c) {
    return new Guard;
};

$container['auth'] = function ($c) {
    return new \App\Auth;
};

$container['upload_directory'] = __DIR__ . '/../public/uploads';

$app->add(new \App\Middleware\ValidationMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add(new \App\Middleware\AuthMiddleware($container));
$app->add(new \App\Middleware\FlashMiddleware($container));
$app->add($container->csrf);


v::with('App\\Validation\\Rules');
