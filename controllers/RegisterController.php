<?php

namespace controllers;

class RegisterController extends Controller{

    public function index(){
        //SQL
        
        $this->view('Register/index', [
            'active' => 'register'
        ]);
    }
}