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

    public function query($queryString, $params = []) {
        $result = $this->conexion->query($queryString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindParam(":$key", $value);
            }
        }

        return $result->fetchAll();
    }

    public function cudOperation($insertString, $params = []) {
        $result = $this->conexion->query($insertString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindParam(":$key", $value);
            }
        }

        return $result->execute();
    }

    public function disable($entityType, $params, $enabled) {
        $params = [];
        $params["enabled"] = $enabled;
        $identification = array_keys()[0];
        return $this->cudOperation("UPDATE FROM $entityType SET enabled=:enabled WHERE $identification=:$identification", $params);
    }

    public function signin($username) {
        $params = [];
        $params["username"] = $username;
        $signin = $this->query("SELECT access, password FROM users WHERE username=:username", $params);
        return $signin;
    }

    public function signup($username, $password, $fullname, $email) {
        $params = [
            "username" => $username
        ];
        if (count($this->query("SELECT username FROM users WHERE username=:username"), ) !== 0) {
            $params["password"] = blowfishCrypt($password, $username);
            $params["fullname"] = $fullname;
            $params["email"] = $email;
            $signUp = $this->cudOperation("INSERT INTO FROM users (username, password, fullname, email, type) VALUES (:username, :password, :fullname, :email, 2)", $params);
            generateToken($username);
            return $signUp;
        }
        return false;
    }

    public function generateToken($username) {
        $token = "";

        do {
            $token = generateRandomKey();
        }while(count($this->query("SELECT token FROM tokens WHERE token=:token and username=:username"), ["token"=>$token, "username"=>$username]) !== 0);

        $params = [
            "token" => $token,
            "username" => $username,
            "expirationDate" => addDays(date(), 2),
            "isTraded" => false,
        ];

        $this->cudOperation("INSERT INTO tokens VALUES(:token, :username, :expirationDate)", $params);

        return $token;
    }

    public function isTokenValid($username, $token) {
        $queryResult = $this->query("SELECT expirationDate FROM tokens WHERE token=:token and username=:username", ["token"=>$token, "username"=>$username]);
        if (count($queryResult) !== 0) {
            return isDateInTime($queryResult[0]["expirationDate"]);
        }

        return false;
    }

    public function updateTeacher() {
        $username = recoge("username");
        
        $params = [
            "name" => recoge("inputName"),
            "email" => recoge("inputEmail"),
            "username" => recoge("inputUsername"),
            "password" => blowfishCrypt(recoge("inputPassword"), $username),
        ];

        return $this->cudOperation("UPDATE FROM users SET name=:name, username=:username, password=:password WHERE email=:email type=2", $params);
    }

    public function deleteTeacher() {
        $params = [
            "email" => recoge("inputEmail"),
        ];

        return $this->cudOperation("DELETE FROM users WHERE email=:email", $params);
    }

    public function createClassroom() {
        $params = [
            "name" => recoge("inputClassroomName"),
            "description" => recoge("inputClasroomDescription"),
            "state" => recoge("selectClasroomState"),
        ];

        $queryResult = $this->query("SELECT name FROM name WHERE name=:name", $params);
        if (count($queryResult) === 0) {
            $params["description"] = $description;
            $params["state"] = $state;

            return $this->cudOperation("INSERT INTO classrooms (name, description, state) VALUES (name, description, state)", $params);
        }

        return false;
    }

    public function updateClassroom() {
        $params = [
            "name" => recoge("inputClassroomName"),
            "description" => recoge("inputClasroomDescription"),
            "state" => recoge("selectClasroomState"),
        ];

        return $this->cudOperation("UPDATE FROM classrooms SET name=:name, description=:description, state=:state WHERE name=:name", $params);
    }

    public function deleteClassroom() {
        $params = [
            "name" => recoge("inputClassroomName"),
        ];

        return $this->cudOperation("DELETE FROM classrooms WHERE name=:name", $params);
    }

    public function createSchedule() {
        $params = [
            "name" => recoge("inputClassroomName"),
            "description" => recoge("inputClasroomDescription"),
            "state" => recoge("selectClasroomState"),
        ];

        $queryResult = $this->query("SELECT name FROM name WHERE name=:name", $params);
        if (count($queryResult) === 0) {
            $params["description"] = $description;
            $params["state"] = $state;

            return $this->cudOperation("INSERT INTO classrooms (name, description, state) VALUES (name, description, state)", $params);
        }

        return false;
    }

    public function updateSchedule() {
        $params = [
            "name" => recoge("inputClassroomName"),
            "description" => recoge("inputClasroomDescription"),
            "state" => recoge("selectClasroomState"),
        ];

        return $this->cudOperation("UPDATE FROM classrooms SET name=:name, description=:description, state=:state WHERE name=:name", $params);
    }

    public function deleteSchedule() {
        $params = [
            "name" => recoge("inputClassroomName"),
        ];

        return $this->cudOperation("DELETE FROM classrooms WHERE name=:name", $params);
    }

    public function getEventsFromMonth() {
        $params = [
            "month" => recoge("month"),
            "year" => recoge("year"),
        ];

        $this->query("SELECT * FROM horario_seleccionado WHERE MONTH(fecha_seleccionada)=:month and YEAR(fecha_seleccionada)=:year", $params);
    }

    public function getTeachers() {
        $this->query("SELECT * FROM users WHERE type=2 and enabled=true");
    }

    public function getClassrooms() {
        $this->query("SELECT * FROM classrooms WHERE enabled=true");
    }

    public function getSchedules() {
        $params = ["year" => getAcademicYear(date())];
        $this->query("SELECT * FROM schedules WHERE enabled=true and YEAR(year)=:year", $params);
    }
}
