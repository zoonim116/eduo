<?php
use Medoo\Medoo;
use Slim\Views\Twig;
use \App\Src\Controllers;

// DIC configuration

$container = $app->getContainer();


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
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
    $view = new \Slim\Views\Twig($settings['template_path'], compact('$settings["cache"]'));
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $container['request']->getUri()));
    $view->addExtension(new Knlv\Slim\Views\TwigMessages(
        new Slim\Flash\Messages()
    ));
    return $view;
};

$container['flash'] = function () {
    return new \Slim\Flash\Messages();
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

$container['UserModel'] = function ($c) {
    return new App\Src\Models\User($c['db']);
};

$container['upload_directory'] = __DIR__ . '/../public/uploads';
