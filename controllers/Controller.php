<?php

namespace Controllers;

use Models\User;
use Models\UserQuery;

class Controller {
    
    public function __construct(){
        $this->userUpdate();
    }
    
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
    
    //Returns true if user is logged
    public function isLogged(){
        if($_SESSION["user"] != NULL){
            return true;
        }
        
        return false;
    }
    
    //Returns true if user is a DofE participant (permission level 1)
    public function isUser(){
        if($this->isLogged() && $_SESSION["user"]->getPermissions() == 1){
            return true;
        }
        
        return false;
    }
    
    //Returns true if user is an editor (permission level 2)
    public function isEditor(){
        if($this->isLogged() && $_SESSION["user"]->getPermissions() == 2){
            return true;
        }
        
        return false;
    }
    
    //Returns true if user is an admin (permission level 3)
    public function isAdmin(){
        if($this->isLogged() && $_SESSION["user"]->getPermissions() == 3){
            return true;
        }
        
        return false;
    }
    
    public function userUpdate(){
        if($this->isLogged()){
            $_SESSION["user"] = UserQuery::create()
                ->joinWith("Image")
                ->findPk($_SESSION["user"]->getId());
        }
    }
}