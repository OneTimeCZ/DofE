<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\User;
use Models\UserQuery;

class InfoController extends Controller{

    public function index(){
        $participants = UserQuery::create()
            ->joinWith("Member")
            ->joinWith("Image")
            ->find();
        
        $this->view('Info/index', 'base_template', [
            'active' => 'info',
            'title' => 'Informace',
            'recent' => ArticleQuery::recent(),
            'participants' => $participants
        ]);
    }
}