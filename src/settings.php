<?php
return [
    'settings' => [
        'appName' => 'Edoo',
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'librariesPath' => __DIR__ . '/Libraries/',
        'uploadDirectory' => __DIR__ . '/../public/uploads',

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache' => __DIR__ . '/tmp/views/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::ERROR,
        ],
    ],
];
