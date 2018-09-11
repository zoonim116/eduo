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
use Aptoma\Twig\Extension\MarkdownExtension;
use Aptoma\Twig\Extension\MarkdownEngine;
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;
// DIC configuration

$container = $app->getContainer();

CacheManager::setDefaultConfig([
    "path" => sys_get_temp_dir(),
    "itemDetailedDate" => false
]);

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
    $engine = new MarkdownEngine\MichelfMarkdownEngine();
    $view->addExtension(new MarkdownExtension($engine));
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

$container['WallController'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\WallController($view);
};

$container['NotificationController'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\NotificationController($view);
};
$container['LessonController'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new Twig($settings['template_path'], compact('$settings["cache"]'));
    return new Controllers\LessonController($view);
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

$container['instanceCache'] = function ($c) {
    return CacheManager::getInstance('files');
};

$container['upload_directory'] = __DIR__ . '/../public/uploads';

$app->add(new \App\Middleware\ValidationMiddleware($container));
$app->add(new \App\Middleware\OldInputMiddleware($container));
$app->add(new \App\Middleware\CsrfViewMiddleware($container));
$app->add(new \App\Middleware\AuthMiddleware($container));
$app->add(new \App\Middleware\FlashMiddleware($container));
$app->add(new \App\Middleware\EditorMiddleware($container));
$app->add(new \App\Middleware\NotificationMiddleware($container));
if(!$container->request->isXhr()) {
    $app->add($container->csrf);
}

new \App\Helper($container);

v::with('App\\Validation\\Rules');
