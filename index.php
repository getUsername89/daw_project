<?php
// web/index.php
// carga del modelo y los controladores
require_once __DIR__ . './classes/Config.php';
error_reporting(Config::$developmentMode);
require_once __DIR__ . './libs/bExceptions.php';
require_once __DIR__ . './libs/bConecta.php';
require_once __DIR__ . './libs/Sessions.php';
require_once __DIR__ . './libs/Validation.php';
require_once __DIR__ . './classes/Model.php';
require_once __DIR__ . './classes/Controller.php';
require_once __DIR__ . './classes/AjaxController.php';

$sessions = Sessions::getInstance();
if (!$sessions->isUserAgentTheSame() && !in_array($ctl, Config::$notuseragent_ctls)) {
    header("Location: index.php?ctl=notuseragent");
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
    'signin' => array('controller' => 'Controller', 'action' => 'signin', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'signout' => array('controller' => 'Controller', 'action' => 'signout', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'signup' => array('controller' => 'Controller', 'action' => 'signup', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'calendar' => array('controller' => 'Controller', 'action' => 'calendar', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'admin' => array('controller' => 'Controller', 'action' => 'admin', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'access' => array('controller' => 'Controller', 'action' => 'access', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'confirmEmail' => array('controller' => 'Controller', 'action' => 'confirmEmail', 'access' => Config::$ACCESS_LEVEL_NOT_ACTIVATED),
    'error' => array('controller' => 'Controller', 'action' => 'error', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'notsigned' => array('controller' => 'Controller', 'action' => 'notsigned', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'notuseragent' => array('controller' => 'Controller', 'action' => 'notuseragent', 'access' => Config::$ACCESS_LEVEL_GUEST),
    'getMonthFromEvents' => array('controller' => 'AjaxController', 'action' => 'getMonthFromEvents', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'createEvent' => array('controller' => 'AjaxController', 'action' => 'createEvent', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'updateEvent' => array('controller' => 'AjaxController', 'action' => 'updateEvent', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'deleteEvent' => array('controller' => 'AjaxController', 'action' => 'deleteEvent', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'getTeachers' => array('controller' => 'AjaxController', 'action' => 'getTeachers', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'getClassrooms' => array('controller' => 'AjaxController', 'action' => 'getClassrooms', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'getSchedules' => array('controller' => 'AjaxController', 'action' => 'getSchedules', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'getEventsFromDay' => array('controller' => 'AjaxController', 'action' => 'getEventsFromDay', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'getEventsFromWeek' => array('controller' => 'AjaxController', 'action' => 'getEventsFromWeeks', 'access' => Config::$ACCESS_LEVEL_TEACHER),
    'updateTeacher' => array('controller' => 'AjaxController', 'action' => 'updateTeacher', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'deleteTeacher' => array('controller' => 'AjaxController', 'action' => 'deleteTeacher', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'createClassroom' => array('controller' => 'AjaxController', 'action' => 'createClassroom', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'updateClassroom' => array('controller' => 'AjaxController', 'action' => 'updateClassroom', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'deleteClassroom' => array('controller' => 'AjaxController', 'action' => 'deleteClassroom', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'createSchedule' => array('controller' => 'AjaxController', 'action' => 'createSchedule', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'updateSchedule' => array('controller' => 'AjaxController', 'action' => 'updateSchedule', 'access' => Config::$ACCESS_LEVEL_ADMIN),
    'deleteSchedule' => array('controller' => 'AjaxController', 'action' => 'deleteSchedule', 'access' => Config::$ACCESS_LEVEL_ADMIN),
);

$ctl = $_GET['ctl'];
// Parseo de la ruta
if (isset($ctl)) {
    if (isset($map[$ctl])) {
        $ruta = $ctl;
    } else {
        header('Location: ./index.php?ctl=error');
        exit;
    }
} else {
    header('Location: ./index.php?ctl=calendar');
}

if (!in_array($ctl, Config::$notsigned_ctls)) {
    //header('Location: ./index.php?ctl=notsigned');
}

// Ejecución del controlador asociado a la ruta
$controlador = $map[$ruta];
if (method_exists($controlador['controller'], $controlador['action'])) {
    if ($sessions->getsession("access") >= $controlador['access']) {
        call_user_func(array(new $controlador['controller'], $controlador['action']));
    } else {
        header('Location: ./index.php?ctl=access');
    }
} else {
    header('Location: ./index.php?ctl=error');
    exit;
}
