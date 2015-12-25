<?php

namespace controllers;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', 'base_template', [
            'active' => 'gallery'
        ]);
    }

    public function single(){
        //SQL for image route
        
        $this->view('Gallery/single', 'gallery_template', [
            'active' => 'gallery'
        ]);
    }
}