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
use Models\Member;
use Models\MemberQuery;
use Models\MembershipApplication;
use Models\MembershipApplicationQuery;
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
            ->joinWith('Member')
            ->joinWith('Image')
            ->filterByUrl($url)
            ->findOne();
        
        if(!$user){
            $this->addPopup('danger', 'Hledaný uživatel nebyl nalezen nebo není členem organizace.');
            redirectTo('/');
        }
          
        $activities = ActivityQuery::create()
            ->filterByIdMember($user->getMember()->getId())
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
            $this->addPopup('danger', 'Pro zobrazení nastavení profilu se musíte nejprve přihlásit.');
            redirectTo('/');
        }
        
        $activities = ActivityQuery::create()
            ->filterByIdMember($_SESSION["user"]->getIdMember())
            ->count();
        
        $this->view('Profile/settings', 'base_template', [
            'active' => 'profile',
            'title' => 'Nastavení profilu',
            'recent' => ArticleQuery::recent(),
            'activities' => $activities
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
            $user->setIdImage(6);
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
    
    public function logDofeActivityFormDate(){
        if(!isset($_POST["date"])){
            $this->addPopup('danger', 'Pro nahlášení aktivit musíte specifikovat přesné datum.');
            redirectTo('/nahlasit-aktivitu');
        }
        
        $date = new DateTime($_POST["date"]);
        $year = $date->format("Y");
        $week = $date->format("W");
        
        redirectTo('/nahlasit-aktivitu/' . $year . '/' . $week);
    }
    
    public function logDofeActivityForm($year = '', $week = ''){
        if($_SESSION["user"]->getIdMember() == NULL){
            $this->addPopup('danger', 'Pro nahlášení aktivit musíte být členem DofE týmu.');
            redirectTo('/');
        }
        
        //either both year and week, or none of them have to be specified
        if($year == ''){
            $date = new DateTime;
            
            $a = new DateTime($date->format('Y') . '-12-28');
            $week_count = $a->format('W');
        } else {
            if($week == ''){
                $this->addPopup('danger', 'Pro nahlášení aktivit musíte specifikovat rok i číslo týdne.');
                redirectTo('/nahlasit-aktivitu');
            }
            
            //28th December is always in the last week - coresponding to ISO-8601
            $a = new DateTime($year . '-12-28');
            $week_count = $a->format('W');
            
            //checks if user tries to show invalid week
            if($week > $week_count){
                $this->addPopup('danger', 'Zadaný týden neexistuje.');
                redirectTo('/nahlasit-aktivitu');
            }
            
            //if the current year is specified, check if user tries to log activity to future
            if(($year == date('Y') && $week > date('W')) || $year > date('Y')){
                $this->addPopup('danger', 'Aktivity není možno nahlašovat do budoucna.');
                redirectTo('/nahlasit-aktivitu');
            }
            
            $date = new DateTime();
            $date->setISODate($year, $week);
            
            $from = $_SESSION["user"]->getMember()->getMemberFrom();
            
            if($from->format("Y") > $date->format("Y") || ($from->format("Y") == $date->format("Y") && $from->format("W") > $date->format("W"))){
                $this->addPopup('danger', 'Aktivity není možno nahlašovat v datu před vstupem do programu.');
                redirectTo('/nahlasit-aktivitu');
            }
        }
        
        //SQL for users logged weeks and activities
        
        $this->view('Profile/logDofe', 'base_template', [
            'active' => 'logActivity',
            'title' => 'Nahlášení aktivit',
            'recent' => ArticleQuery::recent(),
            'date' => $date,
            'week_count' => $week_count
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
        
        if(preg_match('/@/', $_POST["username"])){
            $this->addPopup('danger', 'Vaše uživatelské jméno obsahuje nepovolené znaky.');
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
    
    public function applyForMembershipPage(){
        if($_SESSION["user"]->getIdMember() != NULL){
            $this->addPopup('danger', 'Nemůžete žádat o členství, protože již jste členem organizace.');
            redirectTo('/nastaveni');
        }
        
        $existing = MembershipApplicationQuery::create()
            ->filterByIdUser($_SESSION["user"]->getId())
            ->filterByState("pending")
            ->find();
        
        if(!$existing->isEmpty()){
            $this->addPopup('danger', 'Nemůžete žádat o členství, protože jste již o členství zažádali.');
            redirectTo('/nastaveni');
        }
        
        $this->view('Profile/membershipApplication', 'base_template', [
            'active' => 'membershipApplication',
            'title' => 'Žádost o členství',
            'recent' => ArticleQuery::recent()
        ]);
    }
    
    public function applyForMembership(){
        if($_SESSION["user"]->getIdMember() != NULL){
            $this->addPopup('danger', 'Nemůžete žádat o členství, protože již jste členem organizace.');
            redirectTo('/nastaveni');
        }
        
        $existing = MembershipApplicationQuery::create()
            ->filterByIdUser($_SESSION["user"]->getId())
            ->filterByState("pending")
            ->find();
        
        if(!$existing->isEmpty()){
            $this->addPopup('danger', 'Nemůžete žádat o členství, protože jste již o členství zažádali.');
            redirectTo('/nastaveni');
        }
        
        $app = new MembershipApplication;
        $app->setName($_POST["name"]);
        $app->setSurname($_POST["surname"]);
        $app->setIdUser($_SESSION["user"]->getId());
        $app->setState("pending");
        $app->save();
        
        $this->addPopup("success", "Vaše žádost byla úspěšně odeslána.");
        redirectTo("/nastaveni");
    }
    
    public function logDofeActivity(){
    
    }
    
    public function changeDofe(){
    
    }
    
    public function saveDofeActivities(){
    
    }
    
    public function chooseDofeActivities(){
        //SQL
        
        $this->view('Profile/chooseDofeActivities', 'base_template', [
            'active' => 'chooseActivities',
            'title' => 'Vybrat aktivity',
            'recent' => ArticleQuery::recent()
        ]);
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
    
    public function resetPasswordPage($username, $token){
        if($this->isLogged()){
            $this->addPopup('danger', 'Obnova hesla není možná, pokud jste přihlášeni.');
            redirectTo('/');
        }
        
        if($username == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat uživatelské jméno.');
            redirectTo('/');
        }
        
        if($token == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat potvrzovací kód, který byl poslán na vaši emailovou adresu.');
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
        
        $this->view('Profile/newPassword', 'base_template', [
            'active' => 'newPassword',
            'title' => 'Nové heslo',
            'recent' => ArticleQuery::recent(),
            'username' => $username,
            'token' => $token
        ]);
    }
    
    public function resetPassword($username, $token){
        if($this->isLogged()){
            $this->addPopup('danger', 'Obnova hesla není možná, pokud jste přihlášeni.');
            redirectTo('/');
        }
        
        if($username == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat uživatelské jméno.');
            redirectTo('/');
        }
        
        if($token == ''){
            $this->addPopup('danger', 'Při obnově hesla je nutné zadat potvrzovací kód, který byl poslán na vaši emailovou adresu.');
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
        
        if($_POST['password'] != $_POST['password_again']){
			$popups[] = array('type' => 'danger', 'content' => 'Hesla se neshodují.');
        }
        
        if(preg_match('/[^a-zA-Z0-9]/', $_POST['password'])){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo obsahuje nepovolené znaky nebo mezeru.');
        }
        
        if(strlen(utf8_decode($_POST["password"])) < 8){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš krátké.');
        }
        
        if(strlen(utf8_decode($_POST["password"])) > 32){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš dlouhé.');
        }
        
        if(isset($popups)){
            foreach ($popups as $pop){
                $this->addPopup($pop["type"], $pop["content"]);
            }
            
            redirectTo("/");
        }
        
        $user->setPassword(sha1($_POST["password"]));
        $user->setPasswordResetToken(NULL);
        $user->save();
        
        $this->addPopup('success', 'Vaše heslo bylo úspěšně změněno.');
        redirectTo("/");
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