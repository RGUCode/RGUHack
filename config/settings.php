<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../views/',
        ],

        // Monolog settings
        'log' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Mailer settings
        'mail' => [
            'from' => [
                'name' => 'RGUHack',
                'address' => 'noreply@rguhack.uk',
            ],
        ],

        // Database
        'database' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'rguhack',
            'username' => 'root',
            'password' => '',
            'charset'     => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'        => '',
        ],
    ],
];
