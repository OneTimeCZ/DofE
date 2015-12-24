<?php

namespace controllers;

use Models\User;
use Models\UserQuery;

class Controller{
    
    //Renders a view, passing $data within
    public function view($view, $template, $data = []){
        require_once 'views/' . $template . '.phtml';
    }
}