<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;
use Models\Quotes;
use Models\QuotesQuery;

class LandingPageController extends Controller{
    
    public function index(){
        $participants = array(1,8,7);
        
        $users = UserQuery::create()
            ->filterById($participants)
            ->joinWith('Image')
            ->joinWith('Quote')
            ->find();
        
        $this->view('Landing/index', 'base_template', [
            'active' => 'landing',
            'title' => 'Hlavní stránka',
            'recent' => ArticleQuery::recent(),
            'js' => 'plugins/fotorama/fotorama',
            'css' => 'plugins/fotorama/fotorama',
            'users' => $users
        ]);
    }
}