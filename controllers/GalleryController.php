<?php

namespace controllers;
use Models\Article;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie',
            'recent' => Article::recent()
        ]);
    }

    public function single($name){
        //SQL for image route w/ $name
        
        $this->view('Gallery/single', 'gallery_template', [
            'active' => 'gallery',
            'title' => 'Galerie | '.$name,
            'recent' => Article::recent()
        ]);
    }
}