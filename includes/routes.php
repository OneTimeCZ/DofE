<?php

$router->addGet('LandingPage.index', '/');

$router->addGet('Article.showAll', '/clanky');
$router->addGet('Article.showAllPage', '/clanky/strana/{id}');
$router->addGet('Article.showByCategory', '/clanky/{category}');
$router->addGet('Article.showSingle', '/clanek/{name}');

$router->addGet('Gallery.index', '/galerie');

$router->addGet('Register.index', '/registrace');

$router->addGet('User.profile', '/profil/{name}');
$router->addGet('User.logout', '/odhlasit');
$router->addPost('User.login', '/prihlasit');
$router->addPost('User.create', '/registrovat');