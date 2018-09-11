<?php

// Routes

$app->get('/', \App\Controllers\HomeController::class. ':index')->setName('home');

//For Guest Only
$app->group('', function () {
    $this->get('/user/sign_up', \App\Controllers\UserController::class. ':sign_up')->setName('sign_up');
    $this->post('/user/sign_up', \App\Controllers\UserController::class. ':sign_up');
    $this->get('/user/sign_in', \App\Controllers\UserController::class. ':sign_in')->setName('sign_in');
    $this->post('/user/sign_in', \App\Controllers\UserController::class. ':sign_in');
    $this->get('/user/fb_sign_in', \App\Controllers\UserController::class. ':fb_sign_in');

})->add(new \App\Middleware\GuestMiddleware($container));

//For Authorized Only
$app->group('', function () {
    $this->group('/user', function () {
        $this->get('/logout', \App\Controllers\UserController::class. ':logout')->setName('logout');
        $this->get('/settings', \App\Controllers\UserController::class. ':settings')->setName('user.settings');
        $this->get('/delete', \App\Controllers\UserController::class. ':delete')->setName('user.delete');
        $this->post('/settings', \App\Controllers\UserController::class. ':settings');
        $this->post('/watch', \App\Controllers\UserController::class. ':watch');
        $this->post('/unwatch', \App\Controllers\UserController::class. ':unwatch');
    });

    $this->group('/repository', function () {
        $this->map(['GET', 'POST'], '/create', \App\Controllers\RepositoryController::class. ':create')->setName('repository.create');
        $this->get('/delete/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':delete')->setName('repository.delete');
        $this->map(['GET', 'POST'] ,'/edit/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':edit')->setName('repository.edit');
        $this->post('/watch', \App\Controllers\RepositoryController::class. ':watch');
        $this->post('/unwatch', \App\Controllers\RepositoryController::class. ':unwatch');
        $this->get('/texts/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':texts')->setName('repository.texts');
        $this->map(['GET', 'POST'], '/my', \App\Controllers\RepositoryController::class. ':my')->setName('repository.my');
    });

    $this->group('/text', function () {
        $this->map(['GET', 'POST'], '/create', \App\Controllers\TextController::class. ':create')->setName('text.create');
        $this->get('/delete/{id:[0-9]+}', \App\Controllers\TextController::class. ':delete')->setName('text.delete');
        $this->map(['GET', 'POST'], '/edit/{id:[0-9]+}', \App\Controllers\TextController::class. ':edit')->setName('text.edit');
        $this->post('/highlight', \App\Controllers\TextController::class. ':highlight');
        $this->post('/comment/{id:[0-9]+}', \App\Controllers\TextController::class. ':comment');
        $this->post('/watch', \App\Controllers\TextController::class. ':watch');
        $this->post('/unwatch', \App\Controllers\TextController::class. ':unwatch');
        $this->post('/upload', \App\Controllers\TextController::class. ':upload');
    });

    $this->group('/lessons', function () {
        $this->get('/view/{id:[0-9]+}', \App\Controllers\LessonController::class. ':view')->setName('lessons.view');
        $this->map(['GET', 'POST'], '/create/', \App\Controllers\LessonController::class. ':create')->setName('lessons.create');
        $this->get('/delete/{id:[0-9]+}', \App\Controllers\LessonController::class. ':delete')->setName('lessons.delete');
        $this->map(['GET', 'POST'], '/edit/{id:[0-9]+}', \App\Controllers\LessonController::class. ':edit')->setName('lessons.edit');
    });

    $this->get('/home/dashboard', \App\Controllers\HomeController::class. ':dashboard')->setName('dashboard');

    $this->post('/wall/add', \App\Controllers\WallController::class. ':add')->setName('wall.add');
    $this->get('/wall/delete/{id:[0-9]+}', \App\Controllers\WallController::class. ':delete')->setName('wall.delete');

    $this->get('/notifications', \App\Controllers\NotificationController::class. ':index')->setName('notification.index');

})->add(new \App\Middleware\IsAuthMiddleware($container));


// Both

$app->map(['GET', 'POST'], '/repository/all', \App\Controllers\RepositoryController::class. ':all')->setName('repository.all');
$app->get('/text/view/{id:[0-9]+}', \App\Controllers\TextController::class. ':view')->setName('text.view');
$app->get('/text/presentation/{id:[0-9]+}', \App\Controllers\TextController::class. ':presentation')->setName('text.presentation');
$app->get('/search', \App\Controllers\TextController::class. ':search')->setName('text.search');
$app->get('/repository/view/{id:[0-9]+}', \App\Controllers\RepositoryController::class. ':view')->setName('repository.view');
$app->get('/user/callback/{type}', \App\Controllers\UserController::class. ':callback');
$app->get('/user/profile/{id:[0-9]+}', \App\Controllers\UserController::class.  ':profile')->setName('user.profile');
$app->get('/categories/', \App\Controllers\HomeController::class. ':category_page')->setName('category.list');
$app->get('/category/view/{id:[0-9]+}', \App\Controllers\HomeController::class. ':category_view')->setName('category.view');
$app->post('/wall/parse_url/', \App\Controllers\WallController::class. ':parse_url')->setName('wall.parse_url');
$app->get('/users/all', \App\Controllers\UserController::class. ':all')->setName('users.all');