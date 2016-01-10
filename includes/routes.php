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
$router->addGet('Article.showByCategoryPage', '/clanky/{category}/{id}')
    ->setTokens([
        'id' => '[1-9]\d*',
    ]);
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

//INFORMATIONS
$router->addGet('Info.index', '/informace');

//ADMIN BACKGROUND
$router->addGet('Admin.index', '/administrace');
$router->addGet('Admin.articleList', '/administrace/clanky');
$router->addGet('Admin.articleEdit', '/administrace/clanek/{name}/upravit');
$router->addPost('Admin.articleEdit', '/administrace/clanek/{name}/upravit');
$router->addGet('Admin.articleAdd', '/administrace/clanky/pridat');
$router->addPost('Admin.articleAdd', '/administrace/clanky/ulozit');
$router->addPost('Admin.articleDelete', '/administrace/clanek/{name}/odstranit');
$router->addGet('Admin.imageList', '/administrace/fotografie');
$router->addGet('Admin.imageAdd', '/administrace/fotografie/nahrat');
$router->addPost('Admin.imageAdd', '/administrace/fotografie/ulozit');
$router->addPost('Admin.imageDelete', '/administrace/fotografie/{name}/odstranit');

//USER
$router->addGet('User.profile', '/profil/{name}');
$router->addGet('User.logout', '/odhlasit');
$router->addPost('User.login', '/prihlasit');
$router->addPost('User.create', '/registrovat');