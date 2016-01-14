<?php

namespace controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\Category;
use Models\CategoryQuery;
use Models\User;
use Models\UserQuery;

require_once '/helpers/helper.php';

class AdminController extends Controller{
    
    public function index(){  
        //SQL
        
        $this->view('Admin/index', 'admin_template', [
            'active' => 'main',
            'title' => 'Administrace',
            'sidebar' => User::sideBarInit()
        ]);
    }
    
    public function articleList(){
    }
    
    public function articleAdd(){
        //Get a list of all available categories
        $categories = CategoryQuery::create()
            ->select('Name')
            ->find();
        
        $this->view('Admin/addArticle', 'admin_template', [
            'active' => 'addArticle',
            'title' => 'Přidat nový článek',
            'sidebar' => User::sideBarInit(),
            'categories' => $categories
        ]);
    }
    
    public function articleEdit(){
        
    }
    
    public function articleDelete(){
        
    }
    
    public function articleSave(){
        //If saving a new article
        if($_POST["save"]=="Přidat") {
            $article = new Article;
            $article->setIdUser($_POST["author"]);
            $article->setIdImage(1);
            $article->setIdCategory($_POST["category"]);
            $article->setTitle($_POST["title"]);
            $article->setUrl("2-test-url");
            $article->setKeywords(str_replace(", ", ",", $_POST["keywords"]));
            $p = array("<p>", "</p>");
            $article->setContent(str_replace($p, "", $_POST["content"]));
            
            $article->save();
            
            //Popup for succesfully created article
            redirectTo("/administrace");
        }
    }
    
    public function imageList(){
        
    }
    
    public function imageAdd(){
        
    }
    
    public function imageDelete(){
        
    }
}