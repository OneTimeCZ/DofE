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
        $article = ArticleQuery::create()->findPk($id);
        $user = UserQuery::create()->findPk($article->getIdUser());
        $category = CategoryQuery::create()->findPk($article->getIdCategory());
        $image = ImageQuery::create()->findPk($article->getIdImage());
        $comments = CommentQuery::create()->filterByIdArticle($id)->findOne();
        $comment_author = UserQuery::create()->findPk($comments->getIdUser())->getUsername();
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'article' => $article,
            'author' => $user->getUsername(),
            'category' => $category,
            'imgurl' => $image->getPath(),
            'comments' => $comments,
            'comment_author' => $comment_author
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
        //SQL for comment
        
        //popup with comment/added or error
        redirectTo('/clanek/'.$name."#komentare");
    }
}