<?php

namespace Controllers;

use Controllers\Controller;
use Models\Article;
use Models\ArticleQuery;
use Models\User;
use Models\UserQuery;
use Models\Image;
use Models\ImageQuery;
use Models\Activity;
use Models\ActivityQuery;
use Models\ActivityLog;
use Models\ActivityLogQuery;
use Models\ActivityType;
use Models\ActivityTypeQuery;
use Models\Level;
use Models\LevelQuery;
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
            
            if($user->getEmailConfirmedAt() == NULL) {
                $this->addPopup('warning', 'Vaše emailová adresa nebyla doposud ověřena. Nepřišel Vám potvrzovací email? <a href=/nastaveni/potvrzovaci-email class=alert-link>Nechte si jej zaslat znovu.</a>');
            }
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
        
        //CHECK IF USER IS A DOFE MEMBER WITH SET ACTIVITIES ELSE ERROR 
          
        $activities = ActivityQuery::create()
            ->filterByIdUser($user->getId())
            ->joinWith("ActivityType")
            ->joinWith("Level")
            ->leftJoinWith("ActivityLog")
            ->orderByIdActivityType()
            ->orderByIdLevel("desc")
            ->find();
        
        $this->view('Profile/index', 'base_template', [
            'active' => 'profile',
            'title' => 'Profil | '.$user->getUsername(),
            'user' => $user,
            'js' => 'scripts/collapse',
            'recent' => ArticleQuery::recent(),
            'activities' => $activities
        ]);
    }
    
    public function resendEmail(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro znovuodeslání potvrzovacího emailu se musíte přihlásit.');
            redirectTo("/");
        }
        
        if($_SESSION["user"]->getEmailConfirmedAt() != NULL){
            $this->addPopup('danger', 'Váš email byl již potvrzen.');
            redirectTo("/");
        }
        
        do {
            $token = token(50);
            $existing = UserQuery::create()
                ->filterByEmailConfirmToken($token)
                ->count();
        } while ($existing > 0);
        
        $user = UserQuery::create()
            ->findPk($_SESSION["user"]->getId());
        
        $user->setEmailConfirmToken($token);
        $user->save();
        
        //resend confirmation email with email_confirm_token
        
        $this->addPopup('success', 'Potvrzovací email byl odeslán.');
        redirectTo("/");
    }
    
    public function profileSettings(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro zobrazení vašeho profilu se musíte nejprve přihlásit.');
            redirectTo('/');
        }
        
        //SQL ??
        
        $this->view('Profile/settings', 'base_template', [
            'active' => 'profile',
            'title' => 'Nastavení profilu',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function create(){        
		if($_POST['regPassword'] != $_POST['regPassword2']){
			$this->addPopup('danger', 'Hesla se neshodují.');
			redirectTo("/registrace");
        }
        
        $existing = UserQuery::create()
            ->filterByUsername($_POST["regUsername"])
            ->_or()
            ->filterByEmail($_POST["regEmail"])
            ->findOne();
        
        if($existing != NULL) {
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
            
            do {
                $token = token(50);
                $existing = UserQuery::create()
                    ->filterByEmailConfirmToken($token)
                    ->count();
            } while ($existing > 0);
            
            $user->setEmailConfirmToken($token);
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
    
    public function logDofeActivityForm($year = '', $week = ''){
        if($year == ''){
            $id_year = date('Y');
            $id_week = date('W');
        } else {
            if($week == ''){
                $id_year = date('Y');
                $id_week = date('W');
            } else {
                $id_year = $year;
                $id_week = $week;
            }
        }
        
        //SQL w/ week and year
        
        $this->view('Profile/logDofe', 'base_template', [
            'active' => 'logActivity',
            'title' => 'Nahlášení aktivit',
            'recent' => ArticleQuery::recent(),
            'date' => [
                'year' => $id_year,
                'week' => $id_week
            ]
        ]);
    }
    
    public function changeDofeForm(){
        
    }
    
    public function changeUsername(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro změnu osobních údajů musíte být přihlášeni.');
            redirectTo('/#');
        }
        
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
    }
    
    public function changePassword(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro změnu osobních údajů musíte být přihlášeni.');
            redirectTo('/#');
        }
            
        if($_POST["new_password1"] != $_POST["new_password2"]) {
            $this->addPopup('danger', 'Nová hesla nejsou stejná.');
            redirectTo('/nastaveni/zmenit-udaje');
        }
        
        if($_SESSION["user"]->getPassword() != sha1($_POST["old_password"])) {
            $this->addPopup('danger', 'Špatně zadané původní heslo.');
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
    }
    
    public function changeEmail(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro změnu osobních údajů musíte být přihlášeni.');
            redirectTo('/#');
        }
        
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
    
    public function changeAvatar(){
    
    }
    
    public function logDofeActivity(){
    
    }
    
    public function changeDofe(){
    
    }
    
    public function forgottenPassword(){
        if(preg_match('/@/', $_POST["forgPw"])){        
            $user = UserQuery::create()
                ->filterByEmail($_POST["forgPw"])
                ->findOne();
            
            if(!isset($user)){
                $this->addPopup('danger', 'Uživatel s touto emailovou adresou neexistuje.');
                redirectTo("/zapomenute-heslo");
            }
        } else {
            $user = UserQuery::create()
                ->filterByUsername($_POST["forgPw"])
                ->findOne();
            
            if(!isset($user)){
                $this->addPopup('danger', 'Uživatel s tímto uživatelským jménem neexistuje.');
                redirectTo("/zapomenute-heslo");
            }
        }
        
        $user->setPasswordResetToken(token(50));
        $user->save();
        
        //send email to the user with link to reset password
        // adresa.cz/obnovit-heslo/$user->getUsername()/$user->getPasswordResetToken()
        
        $this->addPopup('success', 'Na vaši emailovou adresu byly odeslány instrukce pro obnovu hesla.');
        redirectTo('/');
    }
    
    public function resetPassword($username, $token){
        $user = UserQuery::create()
            ->filterByUsername($username)
            ->filterByPasswordResetToken($token)
            ->findOne();
            
        if(!isset($user)){
            $this->addPopup('danger', 'Při obnově hesla byl zadán špatný kód nebo uživatelské jméno.');
            redirectTo("/");
        }
        
        $new_password = token(12);
        $user->setPassword(sha1($new_password));
        $user->save();
            
        //send email to the user with new password ($new_password)
        
        $this->addPopup('success', 'Na vaši emailovou adresu bylo odesláno nové heslo.');
        redirectTo('/');
    }
    
    public function forgottenPasswordPage(){
        //SQL
        
        $this->view('Profile/forgottenPassword', 'base_template', [
            'active' => 'forgottenPassword',
            'title' => 'Zapomenuté heslo',
            'recent' => ArticleQuery::recent()
        ]);
    }
}