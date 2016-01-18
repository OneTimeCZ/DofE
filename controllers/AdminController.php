<?php

namespace Controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\Category;
use Models\CategoryQuery;
use Models\Comment;
use Models\CommentQuery;
use Models\User;
use Models\UserQuery;

require_once '/helpers/helper.php';

class AdminController extends Controller{
    
    public function index(){  
        //SQL
        
        $this->view('Admin/index', 'admin_template', [
            'active' => 'main',
            'title' => 'Administrace'
        ]);
    }
    
    public function articleList(){
        if($_SESSION["user"]->getPermissions() == 3) {
            $articles = ArticleQuery::create()
                ->joinWith("User")
                ->orderByCreatedAt("desc")
                ->find();
        } else {
            $articles = ArticleQuery::create()
                ->filterByIdUser($_SESSION["user"]->getId())
                ->orderByCreatedAt("desc")
                ->find();
        }
        
        $this->view('Admin/articleList', 'admin_template', [
            'active' => 'list',
            'title' => 'Seznam článků',
            'articles' => $articles
        ]);
    }
    
    public function articleAdd(){
        //Get a list of all available categories
        $categories = CategoryQuery::create()
            ->find();
        
        $this->view('Admin/addArticle', 'admin_template', [
            'active' => 'addArticle',
            'title' => 'Přidat nový článek',
            'js' => array('tinymce/tinymce.min', 'tinymceinit'),
            'categories' => $categories
        ]);
    }
    
    public function articleEdit($name){
        $id = explode('-', $name);
        $id = $id[0]; 
        
        $article = ArticleQuery::create()
            ->findPk($id);
        
        $categories = CategoryQuery::create()
            ->find();
        
        $this->view('Admin/editArticle', 'admin_template', [
            'active' => 'addArticle',
            'title' => 'Upravit článek',
            'js' => array('tinymce/tinymce.min', 'tinymceinit'),
            'categories' => $categories,
            'article' => $article
        ]);
    }
    
    public function articleDelete($name){
        
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
            $article->setContent($_POST["content"]);
            
            $article->save();
            
            //Popup for succesfully created article
            redirectTo("/administrace");
        } elseif($_POST["save"]=="Upravit") {
            $article = ArticleQuery::create()
                ->findPk($_POST["article"]);
            $article->setIdCategory($_POST["category"]);
            $article->setTitle($_POST["title"]);
            $article->setKeywords(str_replace(", ", ",", $_POST["keywords"]));
            $article->setContent($_POST["content"]);
            
            $article->save();
            
            //Popup for succesfully updated article
            redirectTo("/administrace/clanky");
        }
    }
    
    public function imageList(){
        
    }
    
    public function imageAdd(){
        
    }
    
    public function imageDelete($name){
        
    }
    
    public function commentList($name){
        $id = explode('-', $name);
        $id = $id[0];
        
        $comments = CommentQuery::create()
            ->joinWith("User")
            ->filterByIdArticle($id)
            ->orderByCreatedAt("desc")
            ->find();
            
        $this->view('Admin/commentList', 'admin_template', [
            'active' => 'commentList',
            'title' => 'Seznam komentářů',
            'article_id' => $id,
            'comments' => $comments
        ]);
    }
    
    public function commentDelete($name, $comment){
        $id = explode('-', $name);
        $article_id = $id[0];
        $comment_id = $comment;
        
        $comment = CommentQuery::create()
            ->findPk($comment_id)
            ->delete();
        
        //Popup for succesfully deleted comment
        redirectTo("/administrace/clanek/".$article_id."/komentare");
    }
}