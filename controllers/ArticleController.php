<?php

namespace controllers;

use Models\Article;
use Models\ArticleQuery;
use Models\Category;
use Models\CategoryQuery;
use Models\Comment;
use Models\CommentQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;

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
        
        $article = ArticleQuery::create()->findPK($id);      
        if(!$article) {
            //Popup for non-existing article
            redirectTo('/clanky');
        }
        
        $article_author = UserQuery::create()->findPK($article->getIdUser())->getUsername();
        $article_author_url = UserQuery::create()->findPK($article->getIdUser())->getUrl();
        $article_category = CategoryQuery::create()->findPK($article->getIdCategory())->getName();
        $article_content = $article->getContent();
        $article_title = $article->getTitle();
        $article_keywords = $article->getKeywords();
        $article_url = $article->getUrl();
        $article_date = $article->getCreatedAt()->format('d.m.Y');
        $category_url = CategoryQuery::create()->findPK($article->getIdCategory())->getUrl();
        
        $comment = CommentQuery::create()->filterByIdArticle($id)->find();
        
        $comments = [];
        foreach ($comment as $c) {
            $c_author = UserQuery::create()->findPK($c->getIdUser())->getUsername();
            $c_author_url = UserQuery::create()->findPK($c->getIdUser())->getUrl();
            $c_a = UserQuery::create()->findPK($c->getIdUser())->getIdImage();
            $c_path = ImageQuery::create()->findPK($c_a)->getPath();
            $date = CommentQuery::create()->findPK($c->getId())->getCreatedAt();
            $c_date = $date->format('d.m.Y');
            $c_time = $date->format('H:i:s');
            $c_text = CommentQuery::create()->findPK($c->getId())->getContent();
            
            array_push($comments, array(
                'author' => $c_author,
                'profile_url' => $c_author_url,
                'author_avatar' => $c_path,
                'date' => $c_date,
                'time' => $c_time,
                'content' => $c_text
            ));
        }
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'article_author' => $article_author,
            'article_author_url' => $article_author_url,
            'article_date' => $article_date,
            'category' => $article_category,
            'category_url' => $category_url,
            'content' => $article_content,
            'keywords' => $article_keywords,
            'title' => $article_title,
            'article_url' => $article_url,
            'comments' => $comments
            //'comment_author' => $comment_author
        ]);
    }

    public function showByCategory($category){
        //SQL w/ $category
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'category' => $category
        ]);
    }
    
    public function showByCategoryPage($category, $id){
        //SQL w/ $category
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'category' => $category,
            'page' => $id
        ]);
    }
    
    public function comment($name){
        //Check if logged
        //SQL for comment
        
        //popup with comment/added or error
        redirectTo('/clanek/'.$name."#komentare");
    }
}