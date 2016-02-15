<?php

namespace Controllers;

use Models\User;
use Models\UserQuery;
use Models\Image;
use Models\ImageQuery;
use Models\Member;
use Models\MemberQuery;

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
        return isset($_SESSION["user"]);
    }
    
    //Check user's permissions
    public function checkPermissions($i){
        return $this->isLogged() && $_SESSION["user"]->getPermissions() == $i;
    }
    
    //Returns true if user is a DofE participant (permission level 1)
    public function isUser(){
        return $this->checkPermissions(1);
    }
    
    //Returns true if user is an editor (permission level 2)
    public function isEditor(){
        return $this->checkPermissions(2);
    }
    
    //Returns true if user is an admin (permission level 3)
    public function isAdmin(){
        return $this->checkPermissions(3);
    }
    
    //Updates user session
    public function userUpdate(){
        if($this->isLogged()){
            $_SESSION["user"] = UserQuery::create()
                ->joinWith("Image")
                ->leftJoinWith("Member")
                ->findPk($_SESSION["user"]->getId());
        }
    }
}