<?php

namespace Controllers;

use Models\Article;

class RegisterController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Register/index', 'base_template', [
            'active' => 'register',
            'title' => 'Registrace',
            'recent' => ArticleQuery::recent()
        ]);
    }
}