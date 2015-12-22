<?php

namespace controllers;

use Models\User;
use Models\UserQuery;

class Controller{
    
    //Renders a view, passing $data within
    public function view($view, $data = []){
        require_once 'views/base_template.phtml';
    }
}