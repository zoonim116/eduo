<?php
use Medoo\Medoo;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Knlv\Slim\Views\TwigMessages;
use Slim\Flash\Messages;
use \App\Controllers;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use \App\Validation\Validator;
// DIC configuration

$container = $app->getContainer();


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// medoo
$container['db'] = function () {
    return new Medoo([
        'database_type' => 'mysql',
        'database_name' => 'eduo',
        'server' => 'localhost',
        'username' => 'root',
        'password' => 'root'
    ]);
};

$container['view'] = function($container) {
    $settings = $container->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
    $view->addExtension(new TwigMessages(
        new Messages()
    ));
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

$container['validator'] = function ($c) {
    return new Validator;
};

$container['UserModel'] = function ($c) {
    return new App\Models\User($c['db']);
};

$container['upload_directory'] = __DIR__ . '/../public/uploads';

$app->add(new \App\Middleware\ValidationMiddleware($container));
