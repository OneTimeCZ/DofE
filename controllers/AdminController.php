<?php

namespace controllers;

require_once '/helpers/helper.php';

class AdminController extends Controller{

    public function index(){        
        if(!isAdmin()){
            redirectTo("/");
        }
        
        //SQL
        
        $this->view('Admin/index', 'admin_template', [
            'active' => 'admin'
        ]);
    }
    
    public function articleList(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function articleAdd(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function articleEdit(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function articleDelete(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function imageList(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function imageAdd(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
    
    public function imageDelete(){
        if(!isAdmin()){
            redirectTo("/");
        }
    }
}