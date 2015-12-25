<?php

namespace controllers;

use Models\Article;
use Models\ArticleQuery;

class ArticleController extends Controller{

    public function showAll(){
        //SQL
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'page' => '1'
        ]);
    }
    
    public function showAllPage($id){
        //SQL /w page $id 
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'page' => $id
        ]);
    }

    public function showSingle($name){
        $id = explode('-', $name);
        $id = $id[0];
        $article = ArticleQuery::create()->filterById($id)->findOne();
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'article' => $article
        ]);
    }

    public function showByCategory($category){
        //SQL w/ $category
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'category' => $category
        ]);
    }
    
    public function comment($name){
        //SQL for comment
        
        //popup with comment/added or error
        redirectTo('/clanek/'.$name."#komentare");
    }
}