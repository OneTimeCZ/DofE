<?php

use Models\UserQuery;

//Redirect without executing the rest
function redirectTo($location){
	header("Location: " . $location);
	die();
}

//Includes a file and makes variables usable in it
function includeFile($file, $params = array()){
	extract($params);
	include($file);
}

//Generates a random token
function token($length){
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[rand(0, $max)];
    }
    
    return $str;
}

//If there are any popups to be displayed, generates them
function displayPopups(){
    if(!empty($data["popups"])){
        foreach($data["popups"] as $f){
            require 'views/popup.phtml';
        }
    }
}

//Checks if any popups have been added
function existingPopupsCheck() {
    if(isset($popups)){
        foreach($popups as $p){
            $this->addPopup($p["type"], $p["content"]);
        }
            
        return false;
    }
        
    return true;
}

//Returns a resized img
function resizeImg($img, $w, $h) {
    $width = imagesx($img);
    $height = imagesy($img);
	$imgNew = imagecreatetruecolor($w, $h);
	imagecopyresampled($imgNew, $img, 0, 0, 0, 0, $w, $h, $width, $height);
	
    return $imgNew;
}