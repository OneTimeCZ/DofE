<?php

namespace controllers;

class ArticleController extends Controller{

    public function showAll(){
        //SQL
        
        $this->view('Article/all', [
            'active' => 'blog'
        ]);
    }

    public function showSingle($name){
        //SQL s $id
        
        $this->view('Article/single', [
            'active' => 'blog',
            'name' => $name
        ]);
    }
}