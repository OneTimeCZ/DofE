<?php

namespace controllers;

use Models\Image;
use Models\ImageQuery;
use Models\User;
use Models\UserQuery;

require_once '/helpers/helper.php';

class AdminController extends Controller{
    
    public function index(){  
        //SQL
        
        $this->view('Admin/index', 'admin_template', [
            'active' => 'admin',
            'title' => 'Administrace',
            'sidebar' => User::sideBarInit()
        ]);
    }
    
    public function articleList(){
        
    }
    
    public function articleAdd(){
        
    }
    
    public function articleEdit(){
        
    }
    
    public function articleDelete(){
        
    }
    
    public function imageList(){
        
    }
    
    public function imageAdd(){
        
    }
    
    public function imageDelete(){
        
    }
}