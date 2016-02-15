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
    public function searchBar(){
        //first checks articles
            //in title
            //in keywords
        //then users
        
        //max number of results
        $total_count = 7;
        $text = $_POST["searchdata"];
        
        $articles = ArticleQuery::create()
            ->orderByCreatedAt('desc')
            ->where('Article.Title like ?', '%'.$text.'%')
            ->_or()
            ->where('Article.Keywords like ?', '%'.$text.'%')
            ->limit(4)
            ->find();
        
        $users = UserQuery::create()
            ->orderByPermissions('desc')
            ->where('User.Username like ?', '%'.$text.'%')
            ->limit(2)
            ->find();

        if($users->isEmpty())
        
        echo json_encode($data);
    }
}