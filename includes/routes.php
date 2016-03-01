<?php

//---LANDING PAGE---
    $router->addGet('LandingPage.index', '/');

//---ARTICLE---
    $router->addGet('Article.showAllPage', '/clanky{/id}')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    $router->addGet('Article.showByCategoryPage', '/clanky/{category}{/id}')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    $router->addGet('Article.showSingle', '/clanek/{id}');
    $router->addPost('Article.comment', '/clanek/{id}/komentovat')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);

//---GALLERY---
    $router->addGet('Gallery.index', '/galerie');
    $router->addGet('Gallery.single', '/galerie/{id}')
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
    //gallery list
    $router->addGet('Admin.galleryList', '/administrace/galerie');
    //single gallery - photo list
    $router->addGet('Admin.galleryPage', '/administrace/galerie/{id}')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //add a new gallery page
    $router->addGet('Admin.newGalleryPage', '/administrace/galerie/pridat');
    //add a new gallery
    $router->addPost('Admin.saveGallery', '/administrace/galerie/{id}/ulozit')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //add a new photo page
    $router->addGet('Admin.imageAdd', '/administrace/galerie/{id}/nahrat')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //save new photos
    $router->addPost('Admin.imageSave', '/administrace/galerie/{id}/nahrani')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //delete a photo
    $router->addGet('Admin.imageDelete', '/administrace/galerie/{id}/odstranit')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //accept a suggestion
    $router->addGet('Admin.ideaApprove', '/administrace/navrh/{id}/prijmout')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //refuse a suggestion
    $router->addGet('Admin.ideaRefuse', '/administrace/navrh/{id}/zamitnout')
        ->setTokens([
            'id' => '[1-9]\d*',
        ]);
    //user list
    $router->addGet('Admin.userList', '/administrace/uzivatele');
    //set users permissions
    $router->addGet('Admin.setPermissions', '/administrace/prava/{username}/{permissions}')
        ->setTokens([
            'permissions' => '0|1'
        ]);
    //news pages
    $router->addGet('Admin.newUsersPage', '/administrace/co-je-noveho/uzivatele');
    $router->addGet('Admin.newCommentsPage', '/administrace/co-je-noveho/komentare');
    $router->addGet('Admin.newArticlesPage', '/administrace/co-je-noveho/clanky');
    $router->addGet('Admin.newPhotosPage', '/administrace/co-je-noveho/fotografie');
    //bug pages
    $router->addGet('Admin.solvedBugsPage', '/administrace/chyby/vyresene');
    $router->addGet('Admin.unsolvedBugsPage', '/administrace/chyby/nevyresene');
    $router->addGet('Admin.markBugSolved', '/administrace/chyba/{id}/vyreseno')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //delete a bug report
    $router->addGet('Admin.deleteBug', '/administrace/chyba/{id}/smazat')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    $router->addGet('Admin.singleBugPage', '/administrace/chyba/{id}')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //user reports page
    $router->addGet('Admin.userReportsPage', '/administrace/nahlaseni-uzivatele');
    $router->addGet('Admin.singleUserReportPage', '/administrace/nahlaseni-uzivatele/{id}')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //restrict user's access
    $router->addPost('Admin.banUser', '/administrace/nahlaseni-uzivatele/{id}/zabanovat')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //delete an user report
    $router->addGet('Admin.deleteUserReport', '/administrace/nahlaseni-uzivatele/{id}/odstranit')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    //idea suggestions
    $router->addGet('Admin.ideaSuggestionsPage', '/administrace/navrhy/neschvalene');
    $router->addGet('Admin.singleIdeaSuggestionPage', '/administrace/navrh/{id}')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    $router->addGet('Admin.membershipApplications', '/administrace/zadosti-o-clenstvi');
    $router->addGet('Admin.acceptMembershipApplication', '/administrace/zadosti-o-clenstvi/{id}/schvalit')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);
    $router->addGet('Admin.rejectMembershipApplication', '/administrace/zadosti-o-clenstvi/{id}/zamitnout')
        ->setTokens([
            'id' => '[1-9]\d*'
        ]);

//---USER---
    $router->addGet('User.profilePublic', '/profil/{name}');
    $router->addGet('User.profileSettings', '/nastaveni');
    $router->addGet('User.logout', '/odhlasit');
    $router->addPost('User.login', '/prihlasit');
    $router->addPost('User.create', '/registrovat');
    $router->addGet('User.resendEmail', '/nastaveni/potvrzovaci-email');
    $router->addGet('User.confirmEmail', '/potvrdit-email/{username}/{token}')
        ->setTokens([
            'username' => '\w+',
            'token' => '\w{50}',
        ]);
    $router->addGet('User.changePersonalForm', '/nastaveni/zmenit-udaje');
    $router->addPost('User.changeEmail', '/nastaveni/ulozit-email');
    $router->addPost('User.changePassword', '/nastaveni/ulozit-heslo');
    $router->addPost('User.changeUsername', '/nastaveni/ulozit-jmeno');
    $router->addPost('User.changeAvatar', '/nastaveni/ulozit-avatar');
    $router->addGet('User.changeDofeForm', '/nastaveni/zmenit-dofe');
    $router->addPost('User.changeDofe', '/nastaveni/ulozit-dofe');
    $router->addGet('User.chooseDofeActivities', '/nastaveni/vybrat-aktivity');
    $router->addGet('User.saveDofeActivities', '/nastaveni/ulozit-aktivity');
    $router->addGet('User.forgottenPasswordPage', '/zapomenute-heslo');
    $router->addPost('User.forgottenPassword', '/zapomenute-heslo-email');
    $router->addGet('User.resetPasswordPage', '/obnovit-heslo/{username}/{token}')
        ->setTokens([
            'username' => '\w+',
            'token' => '\w{50}',
        ]);
    $router->addPost('User.resetPassword', '/obnoveni-heslo/{username}/{token}')
        ->setTokens([
            'username' => '\w+',
            'token' => '\w{50}',
        ]);
    $router->addGet('User.logDofeActivityForm', '/nahlasit-aktivitu{/year,week}')
        ->setTokens([
            'year' => '\d{4}',
            'week' => '\d{1,2}'
        ]);
    $router->addPost('User.logDofeActivityFormDate', '/nahlasit-aktivitu-datum');
    $router->addPost('User.logDofeActivity', '/ulozit-aktivitu{/year,week}')
        ->setTokens([
            'year' => '\d{4}',
            'week' => '\d{1,2}'
        ]);
    $router->addGet('User.applyForMembershipPage', '/nastaveni/zazadat-o-clenstvi');
    $router->addPost('User.applyForMembership', '/nastaveni/ulozit-zadost-o-clenstvi');
    $router->addGet('User.reportBugPage', '/nastaveni/nahlasit-chybu');
    $router->addPost('User.reportBug', '/nastaveni/nahlasit-chyba');
    $router->addGet('User.reportUserPage', '/nastaveni/nahlasit-uzivatele');
    $router->addPost('User.reportUser', '/nastaveni/nahlasit-uzivatel');
    $router->addGet('User.ideaSuggestionPage', '/nastaveni/navrhnout-zlepseni');
    $router->addPost('User.ideaSuggestion', '/nastaveni/navrh-zlepseni');

//---SEARCH---
    $router->addPost('Search.searchBar', '/vyhledavani');