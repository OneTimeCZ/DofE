<?php

$router->addGet('LandingPage.index', '/');

$router->addGet('Article.showAll', '/clanky');
$router->addGet('Article.showSingle', '/clanek/{name}');

$router->addGet('Gallery.index', '/galerie');

$router->addGet('Register.index', '/registrace');

$router->addGet('User.profile', '/profil');
$router->addGet('User.logout', '/odhlasit');
$router->addPost('User.login', '/prihlasit');
$router->addPost('User.create', '/registrovat');