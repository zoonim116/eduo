<?php

// Routes

$app->get('/', \App\Controllers\HomeController::class. ':index')->setName('home');

//For Guest
$app->group('', function () {
    $this->get('/user/sign_up', \App\Controllers\UserController::class. ':sign_up')->setName('sign_up');
    $this->post('/user/sign_up', \App\Controllers\UserController::class. ':sign_up');
    $this->get('/user/sign_in', \App\Controllers\UserController::class. ':sign_in')->setName('sign_in');
    $this->post('/user/sign_in', \App\Controllers\UserController::class. ':sign_in');

})->add(new \App\Middleware\GuestMiddleware($container));


//For Authorized
$app->group('', function () {
    $this->get('/home/dashboard', \App\Controllers\HomeController::class. ':dashboard')->setName('dashboard');
    $this->get('/user/logout', \App\Controllers\UserController::class. ':logout')->setName('logout');
    $this->get('/repository/all', \App\Controllers\RepositoryController::class. ':all')->setName('repository.all');
    $this->post('/repository/all', \App\Controllers\RepositoryController::class. ':all');
    $this->get('/repository/create', \App\Controllers\RepositoryController::class. ':create')->setName('repository.create');
    $this->post('/repository/create', \App\Controllers\RepositoryController::class. ':create');
    $this->get('/repository/delete/{id}', \App\Controllers\RepositoryController::class. ':delete')->setName('repository.delete');
    $this->get('/repository/edit/{id}', \App\Controllers\RepositoryController::class. ':edit')->setName('repository.edit');
    $this->post('/repository/edit/{id}', \App\Controllers\RepositoryController::class. ':edit');
})->add(new \App\Middleware\IsAuthMiddleware($container));
