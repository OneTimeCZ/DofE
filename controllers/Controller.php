<?php

namespace controllers;

class Controller{
    
    //Renders a view, passing $data within
    public function view($view, $data = []){
        require_once 'views/base_template.phtml';
    }
}