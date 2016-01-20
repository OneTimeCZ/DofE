<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie',
            'recent' => ArticleQuery::recent(),
            'js' => 'plugins/fotorama/fotorama',
            'css' => 'plugins/fotorama/fotorama'
        ]);
    }
}