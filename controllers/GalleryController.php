<?php

namespace Controllers;

use Models\Article;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie',
            'recent' => ArticleQuery::recent()
        ]);
    }

    public function single($name){
        //SQL for image route w/ $name
        
        $this->view('Gallery/single', 'gallery_template', [
            'active' => 'gallery',
            'title' => 'Galerie | '.$name,
            'recent' => ArticleQuery::recent()
        ]);
    }
}