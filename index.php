<?php
// web/index.php
// carga del modelo y los controladores
require_once __DIR__ . './libs/Config.php';
require_once __DIR__ . './libs/Sessions.php';
require_once __DIR__ . './libs/Validation.php';
require_once __DIR__ . './libs/bExceptions.php';
require_once __DIR__ . './libs/Model.php';
require_once __DIR__ . './libs/Controller.php';
require_once __DIR__ . './libs/AjaxController.php';

$sessions = Sessions::getInstance();

if ($sessions->doesSessionExists("username")) {
    header('Location: ./calendar/');
} else {
    header('Location: ./login/');
}

/*
* Access
* 0 - Guest - ACCESS_LEVEL_GUEST
* 1 - Not activated account - ACCESS_LEVEL_NOT_ACTIVATED
* 2 - Teacher - ACCESS_LEVEL_TEACHER
* 3 - Admin - ACCESS_LEVEL_ADMIN
*/

// enrutamiento
$map = array(
    'signin' => array('controller' =>'Controller', 'action' =>'signin', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'signout' => array('controller' =>'Controller', 'action' =>'signout', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'signup' => array('controller' =>'Controller', 'action' =>'signup', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'calendar' => array('controller' =>'Controller', 'action' =>'calendar', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'admin' => array('controller' =>'Controller', 'action' =>'admin', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'access' => array('controller' =>'Controller', 'action' =>'access', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'confirmEmail' => array('controller' =>'Controller', 'action' =>'confirmEmail', 'access' => Config::$ACCESS_LEVEL_NOT_ACTIVATED),
    'error' => array('controller' =>'Controller', 'action' =>'error', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'notsigned' => array('controller' =>'Controller', 'action' =>'notsigned', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'getMonthFromEvents' => array('controller' =>'AjaxController', 'action' =>'getMonthFromEvents', 'access' => Config::$ACCESS_LEVEL_TEACHER),
);

// Parseo de la ruta
if (isset($_GET['ctl'])) {
    if (isset($map[$_GET['ctl']])) {
        $ruta = $_GET['ctl'];
    } else {
        header('Location: ./error/');
        exit;
    }
} else {
    header('Location: ./calendar/');
}

// Ejecución del controlador asociado a la ruta
$controlador = $map[$ruta];
if (method_exists($controlador['controller'],$controlador['action'])) {
    if ($sessions->getsession("access") >= $controlador['access']) {
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        header('Location: ./access/');
    }
} else {
    header('Location: ./error/');
    exit;
}
?>