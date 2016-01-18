<?php

namespace Controllers;

use Models\ArticleQuery;

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