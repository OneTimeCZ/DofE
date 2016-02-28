<?php

namespace Models;

use Models\Base\User as BaseUser;

/**
 * Skeleton subclass for representing a row from the 'users' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class User extends BaseUser
{
    public function signInUpdate() {
        $this->setSigninCount($this->getSigninCount()+1);
        $this->setLastSigninAt(date("U"));
        $this->save();
    }
    
    public static function checkRegistrationValidity() {
        if(preg_match('/[^a-zA-Z0-9]/', $_POST['regPassword'])){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo obsahuje nepovolené znaky nebo mezeru.');
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) < 8){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš krátké.');
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) > 32){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš dlouhé.');
        }
        
        if(preg_match('/[^a-zA-Z0-9]/', $_POST["regUsername"])){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše uživatelské jméno obsahuje nepovolené znaky nebo mezeru.');
        }
        
        if(isset($popups)){
            foreach($popups as $p){
                $this->addPopup($p["type"], $p["content"]);
            }
            
            return false;
        }
        
        return true;
    }
    
    public static function checkExistingUsernameEmail() {
        $existing = UserQuery::create()
            ->filterByUsername($_POST["regUsername"])
            ->_or()
            ->filterByEmail($_POST["regEmail"])
            ->findOne();
        
        return $existing;
    }
    
    public static function checkPasswordChangeValidity() {
        if($_POST["new_password1"] != $_POST["new_password2"]) {
            $popups[] = array('type' => 'danger', 'content' => 'Nová hesla nejsou stejná.');
        }
        
        if($_SESSION["user"]->getPassword() != sha1($_POST["old_password"])) {
            $popups[] = array('type' => 'danger', 'content' => 'Špatně zadané původní heslo.');
        }
        
        if(preg_match('/[^a-zA-Z0-9]/', $_POST['regPassword'])){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo obsahuje nepovolené znaky nebo mezeru.');
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) < 8){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš krátké.');
        }
        
        if(strlen(utf8_decode($_POST["regPassword"])) > 32){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše heslo je příliš dlouhé.');
        }
            
        if(sha1($_POST["new_password1"]) == $_SESSION["user"]->getPassword()) {
            $popups[] = array('type' => 'danger', 'content' => 'Toto heslo je již přiřazeno k vašemu účtu.');
        }
        
        if(isset($popups)){
            foreach($popups as $p){
                $this->addPopup($p["type"], $p["content"]);
            }
            
            return false;
        }
        
        return true;
    }
    
    public static function checkUsernameChangeValidity() {
        if($_SESSION["user"]->getUsername() == $_POST["username"]) {
            $popups[] = array('type' => 'danger', 'content' => 'Toto jméno je již přiřazeno k vašemu účtu.');
        }
        
        if(preg_match('/@/', $_POST["username"])){
            $popups[] = array('type' => 'danger', 'content' => 'Vaše uživatelské jméno obsahuje nepovolené znaky.');
        }
        
        if(isset($popups)){
            foreach($popups as $p){
                $this->addPopup($p["type"], $p["content"]);
            }

            return false;
        }

        return true;
    }
    
    public static function checkBugReportValidity() {
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
            foreach($popups as $p){
                $this->addPopup($p["type"], $p["content"]);
            }

            return false;
        }

        return true;
    }
    
    public static function checkPasswordResetValidity() {
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
            foreach($popups as $p){
                $this->addPopup($p["type"], $p["content"]);
            }

            return false;
        }

        return true;
    }
}
