<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;

class UserController extends Controller{
    public function searchBar(){
        //first checks articles
            //in title
            //in keywords
        //then users
        
        /*
        //max number of results
        $total_count = 7;
        
        $articles['title'] = ArticleQuery::create()
            ->orderByCreatedAt('desc')
            ->filterByTitle('%' . $text . '%')
            ->limit(3)
            ->find();
        
        if(!$articles['title']->isEmpty()){
            $total_count -= $articles['title']->count();
        }
        
        $articles['keywords'] = ArticleQuery::create()
            ->orderByCreatedAt('desc')
            ->filterByKeywords('%' . $text . '%')
            ->limit(3)
            ->find();
        
        if(!$articles['keywords']->isEmpty()){
            $total_count -= $articles['keywords']->count();
        }
        
        $users['username'] = UserQuery::create()
            ->orderByPermissions('desc')
            ->filterByUsername('%' . $text . '%')
            ->limit($total_count)
            ->find();
        
        if(!$users['username']->isEmpty()){
            $total_count -= $users['username']->count();
        }
        
        if($total_count > 0){
            $users['name'] = UserQuery::create()
                ->orderByPermissions('desc')
                ->filterByName('%' . $text . '%')
                ->limit($total_count)
                ->find();
            
            if(!$users['name']->isEmpty()){
                $total_count -= $users['name']->count();
            }
        }
        
        if($total_count > 0){
            $users['surname'] = UserQuery::create()
                ->orderByPermissions('desc')
                ->filterBySurname('%' . $text . '%')
                ->limit($total_count)
                ->find();
            
            if(!$users['surname']->isEmpty()){
                $total_count -= $users['surname']->count();
            }
        }

        $data = array(
            'by_title' = $articles['title'],
            'by_keywords' = $articles['keywords'],
            'by_username' = $users['username'],
            'by_name' = $users['name'],
            'by_surname' = $users['surname'],
        );
        
        return $data;
        */
    }
}