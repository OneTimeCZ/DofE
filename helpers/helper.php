<?php

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