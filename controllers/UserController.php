<?php

namespace controllers;

class UserController extends Controller{

    public function login(){
        //SQL
        
        $this->view('Landing/index', [
            'active' => 'landing'
        ]);
    }
    
    public function logout(){
        //SQL
        
        $this->view('Landing/index', [
            'active' => 'landing'
        ]);
    }
}