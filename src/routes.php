<?php

// Routes

$app->get('/', \App\Controllers\HomeController::class. ':index')->setName('home');

//For Guest Only
$app->group('', function () {
    $this->get('/user/sign_up', \App\Controllers\UserController::class. ':sign_up')->setName('sign_up');
    $this->post('/user/sign_up', \App\Controllers\UserController::class. ':sign_up');
    $this->get('/user/sign_in', \App\Controllers\UserController::class. ':sign_in')->setName('sign_in');
    $this->post('/user/sign_in', \App\Controllers\UserController::class. ':sign_in');


})->add(new \App\Middleware\GuestMiddleware($container));

//For Authorized Only
$app->group('', function () {
    $this->get('/home/dashboard', \App\Controllers\HomeController::class. ':dashboard')->setName('dashboard');
    $this->get('/user/logout', \App\Controllers\UserController::class. ':logout')->setName('logout');
    $this->get('/user/settings', \App\Controllers\UserController::class. ':settings')->setName('user.settings');
    $this->post('/user/settings', \App\Controllers\UserController::class. ':settings');
    $this->get('/repository/my', \App\Controllers\RepositoryController::class. ':my')->setName('repository.my');
    $this->post('/repository/my', \App\Controllers\RepositoryController::class. ':my');

    $this->get('/repository/create', \App\Controllers\RepositoryController::class. ':create')->setName('repository.create');
    $this->post('/repository/create', \App\Controllers\RepositoryController::class. ':create');
    $this->get('/repository/delete/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':delete')->setName('repository.delete');
    $this->get('/repository/edit/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':edit')->setName('repository.edit');
    $this->post('/repository/watch', \App\Controllers\RepositoryController::class. ':watch');
    $this->post('/repository/unwatch', \App\Controllers\RepositoryController::class. ':unwatch');

    $this->get('/repository/texts/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':texts')->setName('repository.texts');
    $this->post('/repository/edit/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':edit');
    $this->get('/text/create', \App\Controllers\TextController::class. ':create')->setName('text.create');
    $this->post('/text/create', \App\Controllers\TextController::class. ':create');
    $this->get('/text/delete/{id:[0-9]+}', \App\Controllers\TextController::class. ':delete')->setName('text.delete');
    $this->get('/text/edit/{id:[0-9]+}', \App\Controllers\TextController::class. ':edit')->setName('text.edit');
    $this->post('/text/edit/{id:[0-9]+}', \App\Controllers\TextController::class. ':edit');
    $this->post('/text/highlight', \App\Controllers\TextController::class. ':highlight');
    $this->post('/text/comment/{id:[0-9]+}', \App\Controllers\TextController::class. ':comment');
    $this->post('/text/watch', \App\Controllers\TextController::class. ':watch');
    $this->post('/text/unwatch', \App\Controllers\TextController::class. ':unwatch');
    $this->post('/text/upload', \App\Controllers\TextController::class. ':upload');
})->add(new \App\Middleware\IsAuthMiddleware($container));


// Both
$app->get('/repository/all', \App\Controllers\RepositoryController::class. ':all')->setName('repository.all');
$app->post('/repository/all', \App\Controllers\RepositoryController::class. ':all');
$app->get('/text/view/{id:[0-9]+}', \App\Controllers\TextController::class. ':view')->setName('text.view');
$app->get('/text/presentation/{id:[0-9]+}', \App\Controllers\TextController::class. ':presentation')->setName('text.presentation');
$app->get('/repository/view/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':view')->setName('repository.view');
$app->get('/user/callback', \App\Controllers\UserController::class. ':callback');