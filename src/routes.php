<?php

// Routes

$app->get('/', \App\Src\Controllers\HomeController::class. ':index');
$app->get('/user/sign_up', \App\Src\Controllers\UserController::class. ':sign_up')->setName('sign_up');
$app->get('/user/sign_in', \App\Src\Controllers\UserController::class. ':sign_in')->setName('sign_in');
