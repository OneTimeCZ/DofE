<?php

namespace Controllers;

use Models\User;
use Models\UserQuery;

class Controller {
    
    //Renders a view, passing $data within
    public function view($view, $template, $data = []){
        $data['popups'] = isset($_SESSION['popups']) ? $_SESSION['popups'] : array();
		$_SESSION['popups'] = array();
        require_once 'views/' . $template . '.phtml';
    }
    
    //Displays after redirect
    public function addPopup($type = 'info', $content){
        array_push($_SESSION['popups'], ['type' => $type, 'content' => $content]);
    }
    
    //Displays immediately
    public function addPopupNow($type = 'info', $content){
        array_push($this->data['popups'], ['type' => $type, 'content' => $content]);
    }
}