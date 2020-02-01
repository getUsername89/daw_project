<?php
include_once ('Config.php');
include_once ('bCrypt.php');
include_once ('bDate.php');

class Model extends PDO
{
    protected $conexion;
    public static $instance = null;

    public function __construct() {
        $this->conexion = new PDO('mysql:host=' . Config::$mvc_bd_hostname . ';dbname=' . Config::$mvc_bd_nombre . '', Config::$mvc_bd_usuario, Config::$mvc_bd_clave);
        // Realiza el enlace con la BD en utf-8
        $this->conexion->exec("set names utf8");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Model();
        }
        return self::$instance;
    }

    private function query($queryString, $params = [])
    {
        $result = $this->conexion->query($queryString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindParam(":$key", $value);
            }
        }

        return $result->fetchAll();
    }

    private function cudOperation($insertString, $params = [])
    {
        $result = $this->conexion->query($insertString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindParam(":$key", $value);
            }
        }

        return $result->execute();
    }

    private function disable($entityType, $params, $enabled)
    {
        $params = [];
        $params["enabled"] = $enabled;
        $identification = array_keys()[0];
        return cudOperation("UPDATE FROM $entityType SET enabled=:enabled WHERE $identification=:$identification", $params);
    }

    private function signin($username)
    {
        $params = [];
        $params["username"] = $username;
        $signin = query("SELECT access, password FROM users WHERE username=:username", $params);
        return $signin;
    }

    private function signup($username, $password, $fullname, $email)
    {
        $params = [
            "username" => $username
        ];
        if (count(query("SELECT username FROM users WHERE username=:username"), ) !== 0) {
            $params["password"] = $password;
            $params["fullname"] = $fullname;
            $params["email"] = $email;
            $signUp = cudOperation("INSERT INTO FROM users VALUES (:username, :password, :fullname, :email)", $params);
            generateToken($username);
            return $signUp;
        }
        return false;
    }

    function generateToken($username) {
        $token = "";

        do {
            $token = generateRandomKey();
        }while(count(query("SELECT token FROM tokens WHERE token=:token and username=:username"), ["token"=>$token, "username"=>$username]) !== 0);

        $params = [
            "token" => $token,
            "username" => $username,
            "expirationDate" => date('Y-m-d', strtotime(date() . ' + 2 days')),
            "isTraded" => false,
        ];

        cudOperation("INSERT INTO tokens VALUES(:token, :username, :expirationDate)", $params);

        return $token;
    }

    public function isTokenValid($username, $token) {
        $queryResult = query("SELECT expirationDate FROM tokens WHERE token=:token and username=:username"), ["token"=>$token, "username"=>$username];
        if (count($queryResult) !== 0) {
            return isDateInTime($queryResult[0]["expirationDate"]);
        }

        return false;
    }
}
