<?php

namespace controllers;

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
        //SQL w/ $name
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'name' => $name
        ]);
    }

    public function showByCategory($category){
        //SQL w/ $category
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'category' => $category
        ]);
    }
}