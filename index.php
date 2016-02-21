<?php
mb_internal_encoding("UTF-8");

//Includes Composer stuffs
require_once 'vendor/autoload.php';

use Aura\Router\RouterFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Propel\Runtime\Propel;

include("generated-conf/config.php");

//GET or POST
if($_SERVER['REQUEST_METHOD'] == "GET"){
	$params = var_export($_GET, true);
}
else{
	$params = var_export($_POST, true);
}

//Autoload - require files
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ ;
    $file = $base_dir . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once($file);
    }
});

session_start();

require_once 'helpers/helper.php';

//Instantiate AuraRouter as $router
$router_factory = new RouterFactory;
$router = $router_factory->newInstance();

//Load routes from file
require_once 'includes/routes.php';

//Create new logger
$handler = new StreamHandler('app.log', Logger::INFO);
$handler->setFormatter(new \Monolog\Formatter\LineFormatter("[%datetime%] %level_name%: %message%\n"));
$log = new Logger('defaultLogger');
$log->pushHandler($handler);

//Add logger to Propel
Propel::getServiceContainer()->setLogger('defaultLogger', $log);

//Get route
$route = $router->match(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), $_SERVER);

//Dispatch route
if (isset($route->params['action'])){
	$params = $route->params;
	$parts = explode('.', $params['action']);
    
    $method = $parts[1];
    
    $controller = "controllers\\".$parts[0]."Controller";
	$instance = new $controller;
    
	unset($params['action']);
    
	call_user_func_array(array($instance, $method), $params);
}

//Errors
else{
	$failure = $router->getFailedRoute();
    //Do budoucna neechovat, viewovat error page
	if ($failure->failedMethod()) {
		header("HTTP/1.0 405 Method Not Allowed");
		echo("405 Method Not Allowed");
        $log->addInfo("405");
	} elseif ($failure->failedAccept()) {
		header("HTTP/1.0 406 Not Acceptable");
		echo("406 Not Acceptable");
        $log->addInfo("406");
	} else {
		header("HTTP/1.0 404 Not Found");
		echo("404 Not found");
        $log->addInfo("404");
	}
}