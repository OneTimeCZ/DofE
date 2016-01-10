<?php

namespace controllers;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery',
            'title' => 'Galerie'
        ]);
    }

    public function single($name){
        //SQL for image route w/ $name
        
        $this->view('Gallery/single', 'gallery_template', [
            'active' => 'gallery',
            'title' => 'Galerie | '.$name
        ]);
    }
}