<?php

namespace controllers;

class LandingPageController extends Controller{
    
    public function index(){
        $this->view('Landing/index', 'base_template', [
            'active' => 'landing'
        ]);
    }
}