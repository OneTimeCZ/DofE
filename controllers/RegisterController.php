<?php

namespace controllers;
use Models\Article;

class RegisterController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Register/index', 'base_template', [
            'active' => 'register',
            'title' => 'Registrace',
            'recent' => Article::recent()
        ]);
    }
}