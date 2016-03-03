<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;

class SearchController extends Controller{    
    public function index() {
        if(isset($_POST["search"])){
            $s = $_POST["search"];
        }
        
        $users = UserQuery::create()
            ->where('User.Username like ?', '%'.$s.'%')
            ->orderByPermissions('desc')
            ->join('Image')
            ->withColumn('Image.Path', 'AvatarPath')
            ->select('Username', 'AvatarPath')
            ->find();
        
        $articles = ArticleQuery::create()
            ->where('Article.Title like ?', '%'.$s.'%')
            ->_or()
            ->where('Article.Keywords like ?', '%'.$s.'%')
            ->orderByCreatedAt('desc')
            ->join('User')
            ->withColumn('User.Username', 'Author')
            ->join('Category')
            ->withColumn('Category.Name', 'CatgName')
            ->join('Image')
            ->withColumn('Image.Path', 'ImgPath')
            ->select(array('Author', 'CatgName', 'ImgPath', 'Id', 'Title'))
            ->find();
        
        if($articles->isEmpty() && $users->isEmpty()){
            $this->addPopup('danger', 'Nebyl nalezen žádný článek ani uživatel.');
        }
        
        $this->view('Search/index', 'base_template', [
            'active' => 'search',
            'title' => 'Vyhledávání',
            'recent' => ArticleQuery::recent(),
            'articles' => $articles,
            'users' => $users
        ]);
    }
}