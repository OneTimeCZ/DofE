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
use Models\BugReport;
use Models\BugReportQuery;
use Models\UserReport;
use Models\UserReportQuery;
use Models\Idea;
use Models\IdeaQuery;
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
    
    public function confirmEmail($username = '', $token = ''){        
        if($username == ''){
            $this->addPopup('danger', 'Při potvrzování emailu je nutné zadat uživatelské jméno.');
            redirectTo('/');
        }
        
        if($token == ''){
            $this->addPopup('danger', 'Při potvrzování emailu je nutné zadat potvrzovací kód. který byl poslán na vaši emailovou adresu.');
            redirectTo('/');
        }
        
        $user = UserQuery::create()
            ->filterByUsername($username)
            ->filterByEmailConfirmToken($token)
            ->findOne();
            
        if(!isset($user)){
            $this->addPopup('danger', 'Při potvrzování emailu byl zadán špatný kód nebo uživatelské jméno.');
            redirectTo("/");
        }
        
        if($user->getEmailConfirmedAt() != NULL){
            $this->addPopup('danger', 'Váš email byl již potvrzen.');
            redirectTo("/");
        }
        
        $user->setEmailConfirmedAt(time());
        $user->setEmailConfirmToken(NULL);
        $user->save();
        
        $this->addPopup('success', 'Vaše emailová adresa byla potvrzena.');
        redirectTo('/');
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
        
        $_SESSION["user"]->setEmailConfirmToken(token(50));
        $_SESSION["user"]->save();
        
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
        
        if(preg_match('/[^a-zA-Z0-9]/', $_POST['regPassword'])){
            $this->addPopup('danger', 'Vaše heslo obsahuje nepovolené znaky nebo mezeru.');
            redirectTo("/registrace");
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) < 8){
            $this->addPopup('danger', 'Vaše heslo je příliš krátké.');
            redirectTo("/registrace");
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) > 32){
            $this->addPopup('danger', 'Vaše heslo je příliš dlouhé.');
            redirectTo("/registrace");
        }
        
        if(preg_match('/[^a-zA-Z0-9]/', $_POST["regUsername"])){
            $this->addPopup('danger', 'Vaše uživatelské jméno obsahuje nepovolené znaky nebo mezeru.');
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
            $token = token(50);
            //EMAIL THE USER WITH CODE FOR EMAIL CONFIRMATION $token
            
            $user = new User();
            $user->setUsername($_POST['regUsername']);
            $user->setPassword(sha1($_POST['regPassword']));
            $user->setEmail($_POST['regEmail']);
            $user->setUrl($_POST['regUsername']);
            $user->setEmailConfirmToken($token);
            $user->setPasswordResetToken(NULL);
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
    
    public function logDofeActivityForm($year = '', $month = '', $day = ''){
        if($year == ''){
            $id_year = date("Y");
            $id_month = date("m");
            $id_day = date("d");
        } else {
            if($month == ''){
                $id_month = 01;
                $id_day = 01;
            } else {
                $id_month = $month;
                $id_day = $day == '' ? 01 : $day;
            }
            
            $id_year = $year;
        }
        
        $this->view('Profile/logDofe', 'base_template', [
            'active' => 'logActivity',
            'title' => 'Nahlášení aktivit',
            'recent' => ArticleQuery::recent(),
            'date' => [
                'year' => $id_year,
                'month' => $id_month,
                'day' => $id_day
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
        if($username == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat uživatelské jméno.');
            redirectTo('/');
        }
        
        if($token == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat potvrzovací kód. který byl poslán na vaši emailovou adresu.');
            redirectTo('/');
        }
        
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
        $user->setPasswordResetToken(NULL);
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
    
    public function reportBugPage(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro nahlášení chyby musíte být přihlášeni.');
            redirectTo("/");
        }
        
        $this->view('Profile/reportBug', 'base_template', [
            'active' => 'reportBug',
            'title' => 'Nahlásit chybu',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function reportBug(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro nahlášení chyby musíte být přihlášeni.');
            redirectTo("/");
        }
        
        if(strlen(utf8_decode($_POST["description"])) >= 1000){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Popis chyby je příliš dlouhý. Popis by měl obsahovat maximálně 1000 znaků.'
            );
        }
        
        if(strlen(utf8_decode($_POST["location"])) >= 200){
            $popups[] = array(
                'type' => 'danger',
                'content' => 'Lokalizace chyby je příliš dlouhá. Lokalizace by měla obsahovat maximálně 200 znaků.'
            );
        }
        
        if(isset($_POST["device"])){
            if(strlen(utf8_decode($_POST["device"])) >= 200){
                $popups[] = array(
                    'type' => 'danger',
                    'content' => 'Popis zařízení je příliš dlouhý. Popis zařízení by měl obsahovat maximálně 200 znaků.'
                );
            }
        }
        
        if(isset($_POST["browser"])){
            if(strlen(utf8_decode($_POST["browser"])) >= 100){
                $popups[] = array(
                    'type' => 'danger',
                    'content' => 'Popis prohlížeče je příliš dlouhý. Popis prohlížeče by měl obsahovat maximálně 100 znaků.'
                );
            }
        }
        
        if(isset($popups)){
            foreach($popups as $pop){
                $this->addPopup($pop["type"], $pop["content"]);
            }
            
            redirectTo("/nastaveni/nahlasit-chybu");
        }
        
        $bug = new BugReport;
        $bug->setIdUser($_SESSION["user"]->getId());
        $bug->setLocation($_POST["location"]);
        $bug->setDescription($_POST["description"]);
        $bug->setSeverity($_POST["severity"]);
        if(isset($_POST["device"])) $bug->setDevice($_POST["device"]);
        if(isset($_POST["browser"])) $bug->setBrowser($_POST["browser"]);
        $bug->save();
        
        $this->addPopup('success', 'Chyba byla úspěšně nahlášena. Děkujeme!');
        redirectTo("/nastaveni");
    }
    
    public function reportUserPage(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro nahlášení uživatele musíte být přihlášeni.');
            redirectTo("/");
        }
        
        $users = UserQuery::create()
            ->find();
        
        if($users->isEmpty()){
            $this->addPopup('danger', 'V systému nejsou žádní uživatelé?!');
            redirectTo("/nastaveni");
        }
        
        $this->view('Profile/reportUser', 'base_template', [
            'active' => 'reportUser',
            'title' => 'Nahlásit uživatele',
            'recent' => ArticleQuery::recent(),
            'users' => $users
        ]);
    }
    
    public function reportUser(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro nahlášení uživatele musíte být přihlášeni.');
            redirectTo("/");
        }
        
        if($_POST["user"] == 0){
            $this->addPopup('danger', 'Pro nahlášení uživatele musíte nějakého zvolit.');
            redirectTo("/nastaveni/nahlasit-uzivatele");
        }
        
        if($_POST["user"] == $_SESSION["user"]->getId()){
            $this->addPopup('danger', 'Nemůžete nahlásit sami sebe...');
            redirectTo("/nastaveni/nahlasit-uzivatele");
        }
        
        if(strlen(utf8_decode($_POST["reason"])) >= 500){
            $this->addPopup('danger', 'Důvod nahlášení je příliš dlouhý. Důvod nahlášení by měl obsahovat maximálně 500 znaků.');
            redirectTo("/nastaveni/nahlasit-uzivatele");
        }
        
        $report = new UserReport;
        $report->setIdUser($_SESSION["user"]->getId());
        $report->setIdUserReported($_POST["user"]);
        $report->setReason($_POST["reason"]);
        $report->save();
        
        $user = UserQuery::create()
            ->findPk($_POST["user"]);
        
        $this->addPopup('success', 'Uživatel ' . $user->getName() . ' ' . $user->getSurname() . ' (<a class=alert-link href=/profil/' . $user->getUrl() . '>' . $user->getUsername() . '</a>) byl úspěšně nahlášen. Děkujeme!');
        redirectTo("/nastaveni");
    }
    
    public function ideaSuggestionPage(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro podání návrhu na zlepšení musíte být přihlášeni.');
            redirectTo("/");
        }
        
        $this->view('Profile/ideaSuggestion', 'base_template', [
            'active' => 'ideaSuggestion',
            'title' => 'Podat návrh',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function ideaSuggestion(){
        if(!$this->isLogged()){
            $this->addPopup('danger', 'Pro podání návrhu na zlepšení musíte být přihlášeni.');
            redirectTo("/");
        }
        
        if(strlen(utf8_decode($_POST["reason"])) >= 500){
            $this->addPopup('danger', 'Důvod je příliš dlouhý. Důvod by měl obsahovat maximálně 500 znaků.');
            redirectTo("/nastaveni/navrhnout-zlepseni");
        }
        
        if(strlen(utf8_decode($_POST["description"])) >= 1000){
            $this->addPopup('danger', 'Popis návrhu je příliš dlouhý. Popis návrhu by měl obsahovat maximálně 1000 znaků.');
            redirectTo("/nastaveni/navrhnout-zlepseni");
        }
        
        $idea = new Idea;
        $idea->setIdUser($_SESSION["user"]->getId());
        $idea->setDescription($_POST["description"]);
        $idea->setReason($_POST["reason"]);
        $idea->save();
        
        $this->addPopup('success', 'Váš návrh byl úspěšně zaznamenán. Děkujeme!');
        redirectTo("/nastaveni");
    }
}