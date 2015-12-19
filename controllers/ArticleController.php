<?php

namespace controllers;

class ArticleController extends Controller{

    public function showAll(){
        //SQL
        
        //$this->view('Article/all', []);
        $this->view('Article/all', []);
    }

    public function showSingle($name){
        //SQL s $id
        
        $this->view('Article/single', [
            'name' => $name
        ]);
    }
}