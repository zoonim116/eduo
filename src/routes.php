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
    $this->get('/repository/all', \App\Controllers\RepositoryController::class. ':all')->setName('repository.all');
    $this->post('/repository/all', \App\Controllers\RepositoryController::class. ':all');
    $this->get('/repository/create', \App\Controllers\RepositoryController::class. ':create')->setName('repository.create');
    $this->post('/repository/create', \App\Controllers\RepositoryController::class. ':create');
    $this->get('/repository/delete/{id}', \App\Controllers\RepositoryController::class. ':delete')->setName('repository.delete');
    $this->get('/repository/edit/{id}', \App\Controllers\RepositoryController::class. ':edit')->setName('repository.edit');

    $this->get('/repository/texts/{id}', \App\Controllers\RepositoryController::class. ':texts')->setName('repository.texts');
    $this->post('/repository/edit/{id}', \App\Controllers\RepositoryController::class. ':edit');
    $this->get('/text/create', \App\Controllers\TextController::class. ':create')->setName('text.create');
    $this->post('/text/create', \App\Controllers\TextController::class. ':create');
    $this->get('/text/delete/{id}', \App\Controllers\TextController::class. ':delete')->setName('text.delete');
    $this->get('/text/edit/{id}', \App\Controllers\TextController::class. ':edit')->setName('text.edit');
    $this->post('/text/edit/{id}', \App\Controllers\TextController::class. ':edit');
    $this->get('/text/test', \App\Controllers\TextController::class. ':test');
    $this->post('/text/highlight', \App\Controllers\TextController::class. ':highlight');
    $this->post('/text/comment/{id}', \App\Controllers\TextController::class. ':comment');
})->add(new \App\Middleware\IsAuthMiddleware($container));


// Both

$app->get('/text/view/{id}', \App\Controllers\TextController::class. ':view')->setName('text.view');
$app->get('/repository/view/{id}', \App\Controllers\RepositoryController::class. ':view')->setName('repository.view');