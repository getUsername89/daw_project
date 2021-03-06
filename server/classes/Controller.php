<?php
include_once '../libs/utils.php';
include_once '../libs/bEmail.php';
include_once '../libs/bFile.php';
include_once '../libs/bCrypt.php';
include_once './Validation.php';
include_once './Sessions.php';

class Controller
{
    public function error()
    {
        require __DIR__ . '/../templates/error.php';
    }

    public function access()
    {
        require __DIR__ . '/../templates/access.php';
    }

    public function notsigned()
    {
        require __DIR__ . '/../templates/notsigned.php';
    }

    public function notuseragent()
    {
        require __DIR__ . '/../templates/notuseragent.php';
    }

    public function classrooms()
    {
        $classrooms = Model::getInstance()->query("SELECT * FROM classrooms WHERE enabled!=0");

        require __DIR__ . '/../templates/classrooms.php';
    }

    public function confirmEmail()
    {
        $params = ExceptionUtils::tryCatch("Controller", "confirmEmailFunctionality");

        require __DIR__ . '/../templates/email.php';
    }

    public function getFormClassroom()
    {
        return file_get_contents(__DIR__ . '/../templates/forms/formClassroom.html');
    }

    public function getFormEvent()
    {
        return file_get_contents(__DIR__ . '/../templates/forms/formEvent.html');
    }

    public function getFormTeacher()
    {
        return file_get_contents(__DIR__ . '/../templates/forms/formTeacher.html');
    }

    public function getFormSchedule()
    {
        return file_get_contents(__DIR__ . '/../templates/forms/formSchedule.html');
    }

    public function confirmEmailFunctionality()
    {
        $tokenCode = Utils::getCleanedData("token");
        $model = Model::getInstance();
        $sessions = Sessions::getInstance();
        $token = $model->isTokenValid($sessions->getSession("username"), $tokenCode);

        $params = [
            "isInTime" => $token,
            "tokenCode" => $token,
            "tokenDate" => $token,
        ];

        return $params;
    }

    public function signin()
    {
        $result = false;
        if (isset($_REQUEST["signin"])) {
            $result = ExceptionUtils::tryCatch("Controller", "signinFunctionality");

            if ($result) {
                header("Location: ./index.php?ctl=classrooms/");
            } else {
                echo "<div class='p-3 m-5 mb-0 btn btn-danger rounded position-absolute fixed-bottom float-right' onclick='this.remove();'>";
                echo "<p class='m-0'>Error: We couldn't sign you in.</p>\n";
                echo "</div>";
            }
        }
        require __DIR__ . '/../templates/signin.php';
    }

    public function signinFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();
        $sessions = Sessions::getInstance();

        $username = Utils::getCleanedData("inputUsername");
        $password = Utils::getCleanedData("inputPassword");

        $regla = array(
            array(
                'name' => 'username',
                'regla' => 'no-empty,username',
            ),
            array(
                'name' => 'password',
                'regla' => 'no-empty,password',
            ),
        );
        $validation = $validation->rules($regla, ["username" => $username, "password" => $password]);
        $validation = true;

        if ($validation === true) {
            $signin = $model->signin($username);
            if (Cryptography::blowfishCrypt($password, $username) == $signin[0]["password"]) {
                $sessions->setSession("username", $username);
                $sessions->setSession("access", $signin[0]["type"]);
                $sessions->setSession("userImg", $signin[0]["image"]);
                header("Location: index.php?ctl=calendar");
            }
        }

        return false;
    }

    public function signup()
    {
        $result = false;
        if (isset($_REQUEST["signup"])) {

            $result = ExceptionUtils::tryCatch("Controller", "signupFunctionality");
        }

        if ($result) {
            header("Location: ./index.php?ctl=signin/");
        } else {
            require __DIR__ . '/../templates/signup.php';
        }
    }

    public function profile()
    {
        $result = false;
        if (isset($_REQUEST["modify"])) {

            $_REQUEST['inputTeacherUsername'] = $_REQUEST['inputUsername'];
            unset($_REQUEST['inputUsername']);

            $_REQUEST['inputTeacherPassword'] = $_REQUEST['inputPassword'];
            unset($_REQUEST['inputPassword']);

            $_REQUEST['inputTeacherName'] = $_REQUEST['inputName'];
            unset($_REQUEST['inputName']);

            $_REQUEST['inputTeacherEmail'] = $_REQUEST['inputEmail'];
            unset($_REQUEST['inputEmail']);

            $result = ExceptionUtils::tryCatch("Controller", "updateTeacherFunctionality");
        }

        if ($result) {
            header("Location: ./index.php?ctl=profile/");
        } else {
            require __DIR__ . '/../templates/signup.php';
        }
    }

    public function signupFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $_REQUEST["inputEmail"] = $_REQUEST["inputEmail"] . "@iesabastos.org";

        $regla = array(
            array(
                'name' => 'inputName',
                'regla' => 'no-empty,name',
            ),
            array(
                'name' => 'inputUsername',
                'regla' => 'no-empty,username',
            ),
            array(
                'name' => 'inputPassword',
                'regla' => 'no-empty,password',
            ),
            array(
                'name' => 'inputEmail',
                'regla' => 'no-empty,email',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            $username = Utils::getCleanedData("inputUsername");
            $file = FileUtils::validateFile("inputImage", "./img_usuarios/", $username);
            if ($file === false) {
                return false;
            }

            $signup = $model->signup(Utils::getCleanedData("inputName"), $username, Utils::getCleanedData("inputPassword"), Utils::getCleanedData("inputEmail"), $file);

            if ($signup !== false) {
                header("Location: index.php?ctl=signin");
            } else {
                unlink($file);
                echo "<div class='p-3 m-5 btn btn-danger rounded position-absolute fixed-bottom float-right' onclick='this.remove();'>";
                echo "<p class='m-0'>Error: We couldn't sign you up.</p>\n";
                echo "</div>";
            }

        }
        return false;
    }

    public function signout()
    {
        $sessions = Sessions::getInstance();

        $sessions->deleteSession("username");
        $sessions->setSession("access", 0);

        header("Location: ./index.php?ctl=signin");
    }

    public function calendar()
    {
        $classroom = Utils::getCleanedData("classroom");

        if (!isset($_REQUEST["classroom"]) || !Model::getInstance()->doesClassroomExist($classroom)) {
            header("Location: index.php?ctl=classrooms");
        }

        $params = [
            "classroom" => $classroom,
        ];

        require __DIR__ . '/../templates/calendar.php';
    }

    public function admin()
    {
        $result = "scope";
        //Teacher
        if (isset($_REQUEST["createTeacher"])) { //Create
            $_REQUEST["inputName"] = $_REQUEST["inputTeacherName"];
            unset($_REQUEST["inputTeacherName"]);

            $_REQUEST["inputUsername"] = $_REQUEST["inputTeacherUsername"];
            unset($_REQUEST["inputTeacherUsername"]);

            $_REQUEST["inputPassword"] = $_REQUEST["inputTeacherPassword"];
            unset($_REQUEST["inputTeacherPassword"]);

            $_REQUEST["inputEmail"] = $_REQUEST["inputTeacherEmail"];
            unset($_REQUEST["inputTeacherEmail"]);

            $result = ExceptionUtils::tryCatch("Controller", "signupFunctionality");
        } else if (isset($_REQUEST["updateTeacher"])) { //Update
            $result = ExceptionUtils::tryCatch("Controller", "updateTeacherFunctionality");
        } else if (isset($_REQUEST["deleteTeacher"])) { //Delete
            $result = ExceptionUtils::tryCatch("Controller", "deleteTeacherFunctionality");
        } else

        //Classroom
        if (isset($_REQUEST["createClassroom"])) { //Create
            $result = ExceptionUtils::tryCatch("Controller", "createClassroomFunctionality");
        } else if (isset($_REQUEST["updateClassroom"])) { //Update
            $result = ExceptionUtils::tryCatch("Controller", "updateClassroomFunctionality");
        } else if (isset($_REQUEST["deleteClassroom"])) { //Delete
            $result = ExceptionUtils::tryCatch("Controller", "deleteClassroomFunctionality");
        } else

        //Schedule
        if (isset($_REQUEST["createSchedule"])) { //Create
            $result = ExceptionUtils::tryCatch("Controller", "createScheduleFunctionality");
        } else if (isset($_REQUEST["updateSchedule"])) { //Update
            $result = ExceptionUtils::tryCatch("Controller", "updateScheduleFunctionality");
        } else if (isset($_REQUEST["deleteSchedule"])) { //Delete
            $result = ExceptionUtils::tryCatch("Controller", "deleteScheduleFunctionality");
        } else {
            require_once __DIR__ . '/../templates/admin.php';
        }

        return $result;
    }

    public function updateTeacherFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputTeacherUsername',
                'regla' => 'no-empty,name',
            ),
            array(
                'name' => 'inputTeacherPassword',
                'regla' => 'no-empty,username',
            ),
            array(
                'name' => 'inputTeacherName',
                'regla' => 'no-empty,password',
            ),
            array(
                'name' => 'inputTeacherEmail',
                'regla' => 'no-empty,email',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->updateTeacher(Utils::getCleanedData("inputTeacherUsername"), Utils::getCleanedData("inputTeacherPassword"), Utils::getCleanedData("inputTeacherName"), Utils::getCleanedData("inputTeacherEmail"));
        }
        return false;
    }

    public function deleteTeacherFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputTeacherEmail',
                'regla' => 'no-empty,email',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->deleteTeacher(Utils::getCleanedData("inputTeacherEmail"));
        }

        return false;
    }

    public function createClassroomFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputClassroomName',
                'regla' => 'no-empty',
            ),
            array(
                'name' => 'inputClassroomDescription',
                'regla' => 'no-empty,text',
            ),
            array(
                'name' => 'selectClassroomState',
                'regla' => 'no-empty,text',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->createClassroom(Utils::getCleanedData("inputClassroomName"), Utils::getCleanedData("inputClassroomDescription"), Utils::getCleanedData("selectClassroomState"));
        }

        return false;
    }

    public function updateClassroomFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputClassroomName',
                'regla' => 'no-empty,name',
            ),
            array(
                'name' => 'inputClassroomDescription',
                'regla' => 'no-empty,text',
            ),
            array(
                'name' => 'selectClassroomState',
                'regla' => 'no-empty,state',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->updateClassroom(Utils::getCleanedData("inputClassroomName"), Utils::getCleanedData("inputClassroomDescription"), Utils::getCleanedData("selectClassroomState"));
        }

        return false;
    }

    public function deleteClassroomFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputClassroomName',
                'regla' => 'no-empty',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->deleteClassroom(Utils::getCleanedData("inputClassroomName"));
        }

        return false;
    }

    public function createScheduleFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputScheduleStartHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'inputScheduleEndHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'year',
                'regla' => 'no-empty,numeric',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->createSchedule(Utils::getCleanedData("inputScheduleStartHour"), Utils::getCleanedData("inputScheduleEndHour"), Utils::getCleanedData("year"));
        }

        return false;
    }

    public function updateScheduleFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputScheduleStartHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'inputScheduleEndHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'year',
                'regla' => 'no-empty,datetime',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->updateSchedule(Utils::getCleanedData("inputScheduleStartHour"), Utils::getCleanedData("inputScheduleEndHour"), Utils::getCleanedData("year"));
        }

        return false;
    }

    public function deleteScheduleFunctionality()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'inputScheduleStartHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'year',
                'regla' => 'no-empty,datetime',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->deleteSchedule(Utils::getCleanedData("inputScheduleStartHour"), Utils::getCleanedData("year"));
        }

        return false;
    }

    public function getEventsFromMonth()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'month',
                'regla' => 'no-empty,numeric',
            ),
            array(
                'name' => 'year',
                'regla' => 'no-empty,numeric',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->getEventsFromMonth(Utils::getCleanedData("month"), Utils::getCleanedData("year"));
        }

        return false;
    }

    public function getTeachers()
    {
        $model = Model::getInstance();

        return $model->getTeachers();
    }

    public function getClassrooms()
    {
        $model = Model::getInstance();

        return $model->getClassrooms();
    }

    public function getSchedules()
    {
        $model = Model::getInstance();

        return $model->getSchedules();
    }

    public function createEvent()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'title',
                'regla' => 'no-empty,name',
            ),
            array(
                'name' => 'startHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'date',
                'regla' => 'no-empty,date',
            ),
            array(
                'name' => 'classroom',
                'regla' => 'no-empty',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->createEvent(Utils::getCleanedData("title"), Utils::getCleanedData("startHour"), Utils::getCleanedData("date"), Utils::getCleanedData("classroom"));
        }

        return false;
    }

    public function updateEvent()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'title',
                'regla' => 'no-empty,name',
            ),
            array(
                'name' => 'startHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'date',
                'regla' => 'no-empty,date',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->updateEvent(Utils::getCleanedData("title"), Utils::getCleanedData("startHour"), Utils::getCleanedData("date"));
        }

        return false;
    }

    public function deleteEvent()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'startHour',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'date',
                'regla' => 'no-empty,date',
            ),
            array(
                'name' => 'classroom',
                'regla' => 'no-empty',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->deleteEvent(Utils::getCleanedData("startHour"), Utils::getCleanedData("date"), Utils::getCleanedData("classroom"));
        }

        return false;
    }

    public function getSchedule()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'selectedYear',
                'regla' => 'no-empty,date',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->getSchedule(Utils::getCleanedData("selectedYear"));
        }

        return false;
    }

    public function getEventsFromDay()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'selectedDay',
                'regla' => 'no-empty,date',
            ),
            array(
                'name' => 'classroom',
                'regla' => 'no-empty',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return $model->getEventsFromDay(Utils::getCleanedData("selectedDay"), Utils::getCleanedData("classroom"));
        }

        return false;
    }

    public function getEventsFromWeek()
    {
        $model = Model::getInstance();
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'startingDate',
                'regla' => 'no-empty,date',
            ),
            array(
                'name' => 'endingDate',
                'regla' => 'no-empty,date',
            ),
            array(
                'name' => 'classroom',
                'regla' => 'no-empty',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            $dates = DateUtils::getAllDatesFromInterval(Utils::getCleanedData("startingDate"), Utils::getCleanedData("endingDate"));
            $events = [];
            $classroom = Utils::getCleanedData("classroom");
            foreach ($dates as $selectedDay) {
                $events[] = [
                    "date" => $selectedDay,
                    "events" => $model->getEventsFromDay($selectedDay, $classroom),
                ];
            }

            return $events;
        }

        return false;
    }

    public function doesUsernameExist()
    {
        $validation = Validation::getInstance();
        $username = Utils::getCleanedData("username");

        $regla = array(
            array(
                'name' => 'username',
                'regla' => 'no-empty,username',
            ),
        );
        if ($validation->rules($regla, $_REQUEST) === true) {
            return count(Model::getInstance()->query("SELECT `username` FROM `users` WHERE `username`=:username", ["username" => $username])) > 0;
        }

        return true;
    }

    public function getNonWorkWeeklyDays()
    {
        return Model::getInstance()->query("SELECT `nonWorkDay` FROM `nonWorkWeeklyDays`");
    }

    public function getMonthlyNonSchoolDays()
    {
        $validation = Validation::getInstance();

        $regla = array(
            array(
                'name' => 'startingDate',
                'regla' => 'no-empty,datetime',
            ),
            array(
                'name' => 'endingDate',
                'regla' => 'no-empty,date',
            ),
        );
        $validation = $validation->rules($regla, $_REQUEST);

        if ($validation === true) {
            return Model::getInstance()->getMonthlyNonSchoolDays(Utils::getCleanedData("year"), Utils::getCleanedData("month"));
        }

        return false;
    }

    public function test()
    {
        $model = Model::getInstance();
        $sessions = Sessions::getInstance();

        //return Cryptography::blowfishCrypt("test", "test");

        $signin = $model->signin("test");
        if (Cryptography::blowfishCrypt("test", "test") == $signin[0]["password"]) {
            $sessions->setSession("username", "test");
            $sessions->setSession("access", $signin[0]["access"]);
            return true;
        }

        return false;

        /* $params = [
        "orderId" => '94',
        "startHour" => '7:55',
        "endHour" => '8:50',
        "currentyear" => '2019',
        ]; */
        /* $params = [
        "currentyear" => '2020',
        ];
        $orderId = '1';
        return $model->getSchedules(); */
        /* return $model->cudOperation("INSERT INTO `schedules`
        (`orderId`, `startHour`, `endHour`, `currentyear`)
        VALUES
        (:orderId, :startHour, :endHour, :currentyear)",
        $params); */
        /*return $model->query("SELECT * FROM `schedules` WHERE currentyear=:currentyear)",
        $params);*/
        /* $year = "2019";
        $test = $model->conexion->prepare("SELECT * FROM schedules WHERE currentyear=:currentyear");

        $test->bindValue(":currentyear", $year, PDO::PARAM_STR);

        $test->execute(["currentyear" => $year]);
        return $test->fetchAll(PDO::FETCH_ASSOC); */
        /*$test = $model->conexion->prepare("SELECT * FROM schedules WHERE orderId=:orderId");

        $test->bindParam(":orderId", $orderId);

        $test->execute();
        return $test->fetchAll(PDO::FETCH_ASSOC);*/

        //return count($model->query("SELECT * FROM `schedules`", $params));
        //return $model->cudOperation("INSERT INTO `schedules` (`orderId`, `startHour`, `endHour`, `year`) VALUES (:orderId, :startHour, :endHour, :year)", $params);
        //return $model->cudOperation("INSERT INTO `schedules` (`orderId`, `startHour`, `endHour`, `year`) VALUES ('7', '9:45', '10:40', '2019')", $params);
        //return $_REQUEST["year"];
        //return $model->getTeachers();
    }
}