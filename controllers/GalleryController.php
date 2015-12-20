<?php

namespace controllers;

class GalleryController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Gallery/index', [
            'active' => 'gallery'
        ]);
    }
}