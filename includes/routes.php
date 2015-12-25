<?php

//LANDING PAGE
$router->addGet('LandingPage.index', '/');

//ARTICLE
$router->addGet('Article.showAll', '/clanky');
$router->addGet('Article.showAllPage', '/clanky/{id}')
    ->setTokens([
        'id' => '[1-9]\d*',
    ]);
$router->addGet('Article.showByCategory', '/clanky/{category}');
$router->addGet('Article.showSingle', '/clanek/{name}');
$router->addPost('Article.comment', '/clanek/{name}/komentovat');

//GALLERY
$router->addGet('Gallery.index', '/galerie');
$router->addGet('Gallery.single', '/galerie/{name}')
    ->setTokens([
        'id' => '[1-9]\d*([-]\w+)+',
    ]);

//REGISTER
$router->addGet('Register.index', '/registrace');

//USER
$router->addGet('User.profile', '/profil/{name}');
$router->addGet('User.logout', '/odhlasit');
$router->addPost('User.login', '/prihlasit');
$router->addPost('User.create', '/registrovat');