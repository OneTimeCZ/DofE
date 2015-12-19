<?php

namespace controllers;

class Controller{
    
    //Calls a model
    public function model($model){
        require_once 'models/' . $model . '.php';
        return new $model();
    }
    
    //Renders a view, passing $data within
    public function view($view, $data = []){
        require_once 'views/base_template.phtml';
    }
}