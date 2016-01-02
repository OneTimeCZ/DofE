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
        $articles = ArticleQuery::create()
            ->joinWith('Image')
            ->joinWith('User')
            ->joinWith('Category')
            ->select(array('Title', 'Url', 'Content', 'Created_at', 'User.Username', 'User.Url', 'Image.Path', 'Category.Url', 'Category.Name'))
            ->limit(10)
            ->find();
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'page' => '1',
            'articles' => $articles
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
        
        $post = ArticleQuery::create()
            ->joinWith('Image')
            ->joinWith('User')
            ->joinWith('Category')
            ->select(array('Title', 'Url', 'Content', 'Keywords', 'Created_at', 'User.Username', 'User.Url', 'Image.Path', 'Category.Name', 'Category.Url'))
            ->findPk($id);
        
        if($post == NULL){
            //popup for nonexistent article
            redirectTo('/clanky');
        }
        
        $comments = CommentQuery::create()
            ->filterByIdArticle($id)
            ->joinWith('User')
            ->useUserQuery()
                ->joinWith('Image')
            ->endUse()
            ->select(array('Content', 'Created_at', 'User.Username', 'User.Url', 'Image.Path'))
            ->find();
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'article' => $post,
            'comments' => $comments
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