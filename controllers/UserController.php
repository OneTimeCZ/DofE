<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\User;
use Models\UserQuery;
use Models\Image;
use Models\ImageQuery;

require_once '/helpers/helper.php';

class UserController extends Controller{

    public function login(){
        $user = UserQuery::create()
            ->joinWith("Image")
            ->filterByUsername($_POST['inputUsername'])
            ->filterByPassword(sha1($_POST['inputPassword']))
            ->findOne();
        
        if ($user == NULL) {
            $_SESSION['user'] = NULL;
            $this->addPopup('danger', 'Zadali jste nesprávné přihlašovací údaje. Zkuste to prosím znovu.');
        } else {
            $_SESSION['user'] = $user;
            $this->addPopup('success', 'Byli jste úspěšně přihlášeni!');
        }
        
        redirectTo('/');
    }
    
    public function logout(){
        $_SESSION['user'] = NULL;
        
        redirectTo('/');
    }
    
    public function profile($name){
        //SQL /w $name
        
        $this->view('Profile/index', 'base_template', [
            'active' => 'profile',
            'title' => 'Profil | '.$name,
            'name' => $name,
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function create(){        
		if($_POST['regPassword'] != $_POST['regPassword2']){
            
			//Add popup for passwords don't match
			redirectTo("/registrace");
		}
        
		$user = new User();
        $user->setUsername($_POST['regUsername']);
        $user->setPassword(sha1($_POST['regPassword']));
        $user->setEmail($_POST['regEmail']);
        $user->setEmailConfirmToken(token(50));
        $user->setPasswordResetToken(token(50));
        $user->setPermissions(1);
        $user->setSigninCount(1);
        
		$user->save();
        
		//Add popup for successful registration
		redirectTo("/");
    }
}