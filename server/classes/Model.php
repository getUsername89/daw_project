<?php
include_once __DIR__ . '../server/classes/Config.php';
include_once __DIR__ . '../server/libs/bCrypt.php';
include_once __DIR__ . '../server/libs/bDate.php';
include_once __DIR__ . '../server/libs/utils.php';

class Model extends PDO
{
    protected $conexion;
    public static $instance = null;

    public function __construct()
    {
        $this->conexion = new PDO('mysql:host=' . Config::$mvc_bd_hostname . ';dbname=' . Config::$mvc_bd_nombre . '', Config::$mvc_bd_usuario, Config::$mvc_bd_clave);
        // Realiza el enlace con la BD en utf-8
        $this->conexion->exec("set names utf8");
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Model();
        }
        return self::$instance;
    }

    public function query($queryString, $params = [])
    {
        $result = $this->conexion->query($queryString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindValue(":$key", $value);
                /*if (is_int($value)) {
            $result->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
            $result->bindValue(":$key", $value, PDO::PARAM_STR);
            }*/
            }
        }

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cudOperation($insertString, $params = [])
    {
        $result = $this->conexion->prepare($insertString);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $result->bindValue(":$key", $value);
                /*if (is_int($value)) {
            $result->bindValue(":$key", $value, PDO::PARAM_INT);
            } else {
            $result->bindValue(":$key", $value, PDO::PARAM_STR);
            }*/
            }
        }

        return $result->execute();
    }

    public function disable($entityType, $params, $enabled)
    {
        $queryString = "UPDATE FROM $entityType SET enabled=:enabled WHERE ";

        $keys = [];
        foreach ($params as $key => $value) {
            $keys = "$key=:$key";
        }
        $queryString .= $keys . join(" and ");

        $params["enabled"] = $enabled;
        return $this->cudOperation($queryString, $params);
    }

    public function signin($username)
    {
        $params = ["username" => $username];
        $signin = $this->query("SELECT access, password FROM users WHERE username=:username", $params);
        return $signin;
    }

    public function signup($username, $password, $fullname, $email, $image)
    {
        $params = [
            "username" => $username,
        ];
        if (count($this->query("SELECT username FROM users WHERE username=:username", $params)) === 0) {
            $params["password"] = Cryptography::blowfishCrypt($password, $username);
            $params["fullname"] = $fullname;
            $params["email"] = $email;
            $params["image"] = $image;
            $signUp = $this->cudOperation("INSERT INTO users (username, password, fullname, email, type, image) VALUES (:username, :password, :fullname, :email, 1, :image)", $params);
            $this->generateToken($username);
            return $signUp;
        }
        return false;
    }

    public function generateToken($username)
    {
        $token = "";

        do {
            $token = Utils::generateRandomKey();
        } while (count($this->query("SELECT token FROM tokens WHERE token=:token and username=:username"), ["token" => $token, "username" => $username]) === 0);

        $params = [
            "token" => $token,
            "username" => $username,
            "expirationDate" => DateUtils::addDays(date("now"), 2),
            "isTraded" => false,
        ];

        $this->cudOperation("INSERT INTO tokens (token, username, expirationDate) VALUES(:token, :username, :expirationDate)", $params);

        return $token;
    }

    public function isTokenValid($username, $token)
    {
        $queryResult = $this->query("SELECT expirationDate FROM tokens WHERE token=:token and username=:username", ["token" => $token, "username" => $username]);
        if (count($queryResult) === 0) {
            return DateUtils::isDateInTime($queryResult[0]["expirationDate"]);
        }

        return false;
    }

    public function updateTeacher($inputTeacherUsername, $inputTeacherPassword, $inputTeacherName, $inputTeacherEmail)
    {
        $params = [
            "name" => $inputTeacherName,
            "email" => $inputTeacherEmail,
            "username" => $inputTeacherUsername,
            "password" => Cryptography::blowfishCrypt($inputTeacherPassword, $inputTeacherUsername),
        ];

        return $this->cudOperation("UPDATE FROM users SET name=:name, username=:username, password=:password WHERE email=:email type=2", $params);
    }

    public function deleteTeacher($inputTeacherEmail)
    {
        $params = [
            "email" => $inputTeacherEmail,
        ];

        return $this->cudOperation("DELETE FROM users WHERE email=:email", $params);
    }

    public function createClassroom($inputClassroomName, $inputClasroomDescription, $selectClasroomState)
    {
        $params = [
            "name" => $inputClassroomName,
            "description" => $inputClasroomDescription,
            "state" => $selectClasroomState,
        ];

        $queryResult = $this->query("SELECT name FROM name WHERE name=:name", $params);
        if (count($queryResult) === 0) {
            $params["description"] = $inputClasroomDescription;
            $params["state"] = $selectClasroomState;

            return $this->cudOperation("INSERT INTO classrooms (name, description, state) VALUES (:name, :description, :state)", $params);
        }

        return false;
    }

    public function updateClassroom($inputClassroomName, $inputClasroomDescription, $selectClasroomState)
    {
        $params = [
            "name" => $inputClassroomName,
            "description" => $inputClasroomDescription,
            "state" => $selectClasroomState,
        ];

        return $this->cudOperation("UPDATE FROM classrooms SET name=:name, description=:description, state=:state WHERE name=:name", $params);
    }

    public function deleteClassroom($inputClassroomName)
    {
        $params = [
            "name" => $inputClassroomName,
        ];

        return $this->cudOperation("DELETE FROM classrooms WHERE name=:name", $params);
    }

    public function createSchedule($inputScheduleStartHour, $inputScheduleEndHour, $year)
    {
        $params = [
            "startHour" => $inputScheduleStartHour,
            "selectedYear" => $year,
        ];

        $queryResult = $this->query("SELECT selectedYear FROM schedules WHERE startHour=:startHour and selectedYear=:selectedYear", $params);
        if (count($queryResult) === 0) {
            $params["endHour"] = $inputScheduleEndHour;

            return $this->cudOperation("INSERT INTO schedules (startHour, endHour, selectedYear) VALUES (:startHour, :endHour, :selectedYear)", $params);
        }

        return false;
    }

    public function updateSchedule($inputScheduleStartHour, $inputScheduleEndHour, $year)
    {
        $params = [
            "startHour" => $inputScheduleStartHour,
            "endHour" => $inputScheduleEndHour,
            "selectedYear" => $year,
        ];

        return $this->cudOperation("UPDATE FROM schedules SET startHour=:startHour, endHour=:endHour, selectedYear=:selectedYear WHERE startHour=:startHour and selectedYear=:selectedYear", $params);
    }

    public function deleteSchedule($inputScheduleStartHour, $year)
    {
        $params = [
            "startHour" => $inputScheduleStartHour,
            "selectedYear" => $year,
        ];

        return $this->cudOperation("DELETE FROM schedules WHERE startHour=:startHour and selectedYear=:selectedYear", $params);
    }

    public function getEventsFromMonth()
    {
        $params = [
            "month" => Utils::getCleanedData("month"),
            "year" => Utils::getCleanedData("year"),
        ];

        return $this->query("SELECT * FROM events WHERE MONTH(selectedDay)=:month and YEAR(selectedDay)=:year", $params);
    }

    public function getTeachers()
    {
        return $this->query("SELECT * FROM users WHERE type=2 and enabled=true");
    }

    public function getClassrooms()
    {
        return $this->query("SELECT * FROM classrooms WHERE enabled=true");
    }

    public function getSchedules()
    {

        $year = Utils::getAcademicYear(date("now"));
        //$year = "2020";
        $params = ["scheduleYear" => $year];
        return $this->query("SELECT * FROM schedules WHERE enabled=true and YEAR(scheduleYear)=:scheduleYear", $params);
    }

    public function createEvent($title, $startHour, $date)
    {
        $sessions = Sessions::getInstance();

        $params = [
            "title" => $title,
            "startHour" => $startHour,
            "date" => $date,
            "username" => $sessions->getSession("username"),
        ];

        if (count($this->query("SELECT name FROM events WHERE startHour=:startHour and date=:date", $params)) === 0) {
            return $this->query("INSERT INTO FROM events (title, startHour, date, username) VALUES (:title, :startHour, :date, :username)", $params);
        }

        return false;
    }

    public function updateEvent($title, $startHour, $date)
    {
        $params = [
            "title" => $title,
            "startHour" => $startHour,
            "date" => $date,
        ];

        return $this->cudOperation("UPDATE FROM events SET title=:title WHERE startHour=:startHour and date=:date", $params);
    }

    public function deleteEvent($startHour, $date)
    {
        $params = [
            "startHour" => $startHour,
            "date" => $date,
        ];

        return $this->cudOperation("DELETE FROM events WHERE startHour=:startHour and date=:date", $params);
    }

    public function getSchedule()
    {
        $params = [
            "selectedYear" => Utils::getCleanedData("selectedYear"),
        ];

        return $this->query("SELECT * FROM schedules WHERE year=:selectedYear", $params);
    }

    public function getEventsFromDay($selectedDay, $classroom)
    {
        $params = [
            "selectedDay" => $selectedDay,
            "classroom" => $classroom,
            "username" => Sessions::getInstance()->getSession("username"),
        ];

        return $this->query("SELECT startHour as 'event-start-hour', endHour as 'event-end-hour',
        title as 'event-title', users.email as 'teacher-email',
        users.fullname as 'teacher-name', users.username = :username as 'show-schedule', 1 as 'show-schedule'
        FROM
        `schedules` join `events` on (schedules.year = events.year and schedules.orderId = events.orderId)
        join `users` using (username) WHERE selectedDay=:selectedDay and classRoomName=:classRoomName", $params);
    }

    public function getEventsFromWeek($startingDate, $endingDate)
    {
        $params = [
            "startingDate" => $startingDate,
            "endingDate" => $endingDate,
        ];
        return $this->query("SELECT * FROM events WHERE selectedDay between :startingDate AND :endingDate", $params);
    }

    public function getMonthlyNonSchoolDays($selectedYear, $selectedMonth)
    {
        $params = [
            "selectedYear" => $selectedYear,
            "selectedMonth" => $selectedMonth,
        ];

        return $this->query("SELECT * FROM `specialDays` WHERE YEAR(specialDay)=:selectedYear and MONTH(specialDay)=:selectedMonth", $params);
    }

}