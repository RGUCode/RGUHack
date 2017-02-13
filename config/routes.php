<?php
// Routes

$app->get('/', 'Site\Controller\MainController:index');
$app->post('/sponsor', 'Site\Controller\MainController:sponsor');
$app->post('/register', 'Site\Controller\MainController:register');
