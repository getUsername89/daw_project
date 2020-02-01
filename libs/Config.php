<?php

class Config
{
    static public $mvc_bd_hostname = "localhost";
    static public $mvc_bd_nombre = "alimentos";
    static public $mvc_bd_usuario = "root";
    static public $mvc_bd_clave = "";
    static public $mvc_vis_css = [
        "bootstrap.min.css",
        "main.css",
    ];
    static public $mvc_vis_scripts = [
        "libs/jquery.min.js",
        "libs/jquery-ui.min.js",
        "libs/bootstrap.min.js",
        "libs/bootstrap.bundle.min.js",
        "js/utils.js",
    ];
    static public $emailSender = "no-reply@iesabastos.org";
    static public $ACCESS_LEVEL_GUEST = 0;
    static public $ACCESS_LEVEL_NOT_ACTIVATED = 1;
    static public $ACCESS_LEVEL_TEACHER = 2;
    static public $ACCESS_LEVEL_ADMIN = 3;
}

?>