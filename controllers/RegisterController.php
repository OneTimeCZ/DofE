<?php

namespace controllers;

class RegisterController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Register/index', [
            'active' => 'register'
        ]);
    }
    
    public function create(){
        //SQL
        
        //Přidat popup při vytvoření uživatele/chybě
        $this->view('Landing/index', [
            'active' => 'landing'
        ]);
    }
}