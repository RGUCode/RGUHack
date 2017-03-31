<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['view'] = function ($c) {
    $settings = $c->get('settings')['view'];

    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['log'] = function ($c) {
    $settings = $c->get('settings')['log'];

    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// mail
$container['mail'] = function ($c) {
    $settings = $c->get('settings')['mail'];

    $mailer = new PHPMailer;
    $mailer->setFrom($settings['from']['address'], $settings['from']['name']);

    return $mailer;
};

// database
$container['db'] = function ($c) {
    $settings = $c->get('settings')['database'];

    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
