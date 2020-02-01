<?php

class Sessions {

    public static $instance = null;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Sessions();
            self::$instance->initSession();
        }

        return self::$instance;
    }

    public function __construct() {
        
    }

    private function initSession() {
        session_start();
        startingParams();
        initializeValues();
    }

    private function startingParams() {

    }

    private function initializeValues() {
        setSession("access", 0);
    }

    public function getSessionID() {
        return session_id();
    }

    public function getSession($session_name) {
        return $_SESSION[$session_name];
    }

    public function setSession($session_name , $data) {
        $_SESSION[$session_name] = $data;
    }

    public function deleteSession($session_name = '') {
        if(!empty($session_name)) {
            unset($_SESSION[$session_name]);
        } else{
            unset($_SESSION);
        }
    }

    public function doesSessionExist($session_name) {
        return isset($_SESSION[$session_name]);
    }

    public function insertData($session_name , array $data) {
        if(is_array($_SESSION[$session_name])) {
            array_push($_SESSION[$session_name], $data);
        } else {
            setSession($session_name, $data);
        }
    }
}

?>