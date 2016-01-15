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
            ->orderByCreatedAt("desc")
            ->limit(10)
            ->find();
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'title' => 'Blog',
            'page' => '1',
            'articles' => $articles
        ]);
    }
    
    public function showAllPage($id){
        //SQL /w page $id 
        
        $this->view('Article/all', 'base_template', [
            'active' => 'blog',
            'title' => 'Blog',
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
            ->find();
        
        $this->view('Article/single', 'base_template', [
            'active' => 'blog',
            'title' => $post->getTitle(),
            'article' => $post,
            'comments' => $comments
        ]);
        
    }

    public function showByCategory($category){
        //SQL w/ $category
        
        $this->view('Article/category', 'base_template', [
            'active' => 'blog',
            'title' => 'Blog | '.ucfirst($category),
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