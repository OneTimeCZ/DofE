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
    
    public function profilePublic($url){
        $user = UserQuery::create()
            ->joinWith('Image')
            ->filterByUrl($url)
            ->findOne();
        
        if(!$user){
            $this->addPopup('danger', 'Hledaný uživatel nebyl nalezen.');
            redirectTo('/');
        }
        
        $this->view('Profile/index', 'base_template', [
            'active' => 'profile',
            'title' => 'Profil | '.$user->getUsername(),
            'user' => $user,
            'js' => 'scripts/collapse',
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
    
    public function changePersonalForm(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro změnu osobních údajů musíte být přihlášeni.');
            redirectTo('/#');
        }
        
        $this->view('Profile/changePersonal', 'base_template', [
            'active' => 'profile',
            'title' => 'Nastavení',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function changeAvatarForm(){
    
    }
    
    public function logDofeActivityForm(){
    
    }
    
    public function changeDofeForm(){
    
    }
    
    public function changePersonal(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro změnu osobních údajů musíte být přihlášeni.');
            redirectTo('/#');
        }
        
        if($_POST["save"] == 'Změnit jméno') {
            if($_SESSION["user"]->getUsername() == $_POST["username"]) {
                $this->addPopup('danger', 'Toto jméno je již přiřazeno k vašemu účtu.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            $existing = UserQuery::create()
                ->filterByUsername($_POST["username"])
                ->find();
            
            if(!$existing->isEmpty()) {
                $this->addPopup('danger', 'Toto jméno je již přiřazeno k jinému účtu.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            $user = UserQuery::create()
                ->findPk($_SESSION["user"]->getId());
                
            $user->setUsername($_POST["username"]);
            $user->save();
                
            $this->addPopup('success', 'Uživatelské jméno bylo úspěšně změněno.');
            redirectTo('/nastaveni/zmenit-udaje');
            
        } elseif($_POST["save"] == 'Změnit heslo') {
            if($_SESSION["user"]->getPassword() != sha1($_POST["old_password"])) {
                $this->addPopup('danger', 'Špatně zadané původní heslo.');
                redirectTo('/nastaveni/zmenit-udaje');
            }     
            
            if($_POST["new_password1"] != $_POST["new_password2"]) {
                $this->addPopup('danger', 'Nová hesla nejsou stejná.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            if(sha1($_POST["new_password1"]) == $_SESSION["user"]->getPassword()) {
                $this->addPopup('danger', 'Toto heslo je již přiřazeno k vašemu účtu.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            $user = UserQuery::create()
                ->findPk($_SESSION["user"]->getId());
            
            $user->setPassword(sha1($_POST["new_password1"]));
            $user->save();
            
            $this->addPopup('success', 'Heslo bylo úspěšně změněno.');
            redirectTo('/nastaveni/zmenit-udaje');
            
        } elseif($_POST["save"] == 'Změnit email') {
            if($_POST["e-mail"] == $_SESSION["user"]->getEmail()) {
                $this->addPopup('danger', 'Tento email je již přiřazen k vašemu účtu.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            $existing = UserQuery::create()
                ->filterByEmail($_POST["e-mail"])
                ->find();
            
            if(!$existing->isEmpty()) {
                $this->addPopup('danger', 'Tento email je již přiřazen k jinému účtu.');
                redirectTo('/nastaveni/zmenit-udaje');
            }
            
            //mail to existing
            $this->addPopup('info', 'Na váš email byla odeslána žádost o změnu.');
            redirectTo('/nastaveni/zmenit-udaje');
        }
    }
    
    public function changeAvatar(){
    
    }
    
    public function logDofeActivity(){
    
    }
    
    public function changeDofe(){
    
    }
}