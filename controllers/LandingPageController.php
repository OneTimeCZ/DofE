<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;

class LandingPageController extends Controller{
    
    public function index(){
        $this->view('Landing/index', 'base_template', [
            'active' => 'landing',
            'title' => 'Hlavní stránka',
            'recent' => ArticleQuery::recent()
        ]);
    }
}