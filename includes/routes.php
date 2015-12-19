<?php

$router->addGet('LandingPage.index', '/');

$router->addGet('Article.showAll', '/clanky');
$router->addGet('Article.showSingle', '/clanek/{name}');