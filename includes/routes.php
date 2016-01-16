<?php

//---LANDING PAGE---
    $router->addGet('LandingPage.index', '/');

//---ARTICLE---
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
    $router->addGet('Article.showSingle', '/clanek/{id}');
    $router->addPost('Article.comment', '/clanek/{id}/komentovat');

//---GALLERY---
    $router->addGet('Gallery.index', '/galerie');
    $router->addGet('Gallery.single', '/galerie/{name}')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);

//---REGISTER---
    $router->addGet('Register.index', '/registrace');

//---INFORMATIONS---
    $router->addGet('Info.index', '/informace');

//---ADMIN BACKGROUND---
    //admin manpage
    $router->addGet('Admin.index', '/administrace');
    //article list
    $router->addGet('Admin.articleList', '/administrace/clanky');
    //article editation page
    $router->addGet('Admin.articleEdit', '/administrace/clanek/{name}/upravit');
    //add a new article page
    $router->addGet('Admin.articleAdd', '/administrace/clanky/pridat');
    //save an article
    $router->addPost('Admin.articleSave', '/administrace/clanky/ulozit');
    //delete an article
    $router->addGet('Admin.articleDelete', '/administrace/clanek/{name}/odstranit');
    //comment list
    $router->addGet('Admin.commentList', '/administrace/clanek/{name}/komentare');
    //delete a comment
    $router->addGet('Admin.commentDelete', '/administrace/clanek/{name}/komentar/{comment}/odstranit');
    //photo list
    $router->addGet('Admin.imageList', '/administrace/fotografie');
    //add a new photo page
    $router->addGet('Admin.imageAdd', '/administrace/fotografie/nahrat');
    //save a new photo
    $router->addPost('Admin.imageSave', '/administrace/fotografie/ulozit');
    //delete a photo
    $router->addGet('Admin.imageDelete', '/administrace/fotografie/{name}/odstranit');

//---USER---
    $router->addGet('User.profile', '/profil/{name}');
    $router->addGet('User.logout', '/odhlasit');
    $router->addPost('User.login', '/prihlasit');
    $router->addPost('User.create', '/registrovat');