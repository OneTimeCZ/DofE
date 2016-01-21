<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\User;
use Models\UserQuery;
use Models\Image;
use Models\ImageQuery;
use \DateTime;

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
            $user->setSigninCount($user->getSigninCount()+1);
            $user->setLastSigninAt(date("U"));
            $user->save();
            $this->addPopup('success', 'Byli jste úspěšně přihlášeni!');
        }
        
        redirectTo('/');
    }
    
    public function logout(){
        if(isset($_SESSION['user'])) {
            unset($_SESSION['user']); 
        
            $this->addPopup('success', 'Byli jste úspěšně odhlášeni!');       
        }
        
        redirectTo('/');
    }
    
    public function profilePublic($name){
        //SQL /w $name
        
        $this->view('Profile/index', 'base_template', [
            'active' => 'profile',
            'title' => 'Profil | '.$name,
            'name' => $name,
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function profileSettings(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro zobrazení vašeho profilu se musíte nejprve přihlásit.');
            redirectTo('/');
        }
        
        //SQL
        
        $this->view('Profile/settings', 'base_template', [
            'active' => 'profile',
            'title' => 'Nastavení profilu',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function create(){  
        $existing = UserQuery::create()
            ->filterByUsername($_POST["regUsername"])
            ->_or()
            ->filterByEmail($_POST["regEmail"])
            ->findOne();
        
		if($_POST['regPassword'] != $_POST['regPassword2']){
			$this->addPopup('danger', 'Hesla se neshodují.');
			redirectTo("/registrace");
        } elseif($existing != NULL) {
            if($existing->getUsername() == $_POST["regUsername"]){
                $this->addPopup('danger', 'Uživatel se stejným uživatelským jménem je již zaregistrován.');
            } elseif($existing->getEmail() == $_POST["regEmail"]){
                $this->addPopup('danger', 'Uživatel se stejnou emailovou adresou je již zaregistrován.');
            } 
            
            redirectTo("/registrace");
        } else {
            $user = new User();
            $user->setUsername($_POST['regUsername']);
            $user->setPassword(sha1($_POST['regPassword']));
            $user->setEmail($_POST['regEmail']);
            $user->setEmailConfirmToken(token(50));
            $user->setPasswordResetToken(token(50));
            $user->setPermissions(0);
            $user->setSigninCount(0);

            $user->save();

            $this->addPopup('success', 'Registrace proběhla úspěšně! Nyní se můžete přihlásit.');
            redirectTo("/");
        }
    }
}