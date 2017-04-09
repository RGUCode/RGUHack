<?php
// Routes

$app->get('/', 'Site\Controller\MainController:index');
$app->post('/sponsor', 'Site\Controller\MainController:sponsor')->setName('sponsor');
$app->post('/register', 'Site\Controller\MainController:register')->setName('student_register');

$app->get('/email/confirm', 'Site\Controller\EmailController:confirm');
$app->map(['GET', 'POST'], '/confirm/{token:[0-9a-f]+}', 'Site\Controller\MainController:confirm')->setName('confirm');
