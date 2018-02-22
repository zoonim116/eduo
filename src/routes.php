<?php

// Routes

$app->get('/', \App\Controllers\HomeController::class. ':index');
$app->get('/user/sign_up', \App\Controllers\UserController::class. ':sign_up')->setName('sign_up');
$app->post('/user/sign_up', \App\Controllers\UserController::class. ':sign_up');
$app->get('/user/sign_in', \App\Controllers\UserController::class. ':sign_in')->setName('sign_in');
