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

function isAdmin() {
    if(!isset($_SESSION["user"])){
        return false;
    } elseif(UserQuery::create()->findPk($_SESSION["user"])->getPermissions() < 2) {
        return false;
    } else {
        return true;
    }
}