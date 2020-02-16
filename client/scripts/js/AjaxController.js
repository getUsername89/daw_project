/*function queryDatabase(requestLocation, success, message = "Ha surgido un error al realizar la operación") {
    request(requestLocation, "POST", {
            select: "",
            from: "table",
            where: "condition",
            params: [
                ["key", "value"],
                ["key", "value"],
                ["key", "value"],
            ],
        }, success,
        function() {
            sendNotification(message, true);
        });
}*/

class AjaxController {
    static request(requestLocation, requestType = "POST", params = {}, success = AjaxController.defaultAjaxSuccessAction, error = AjaxController.defaultAjaxErrorAction, async = true) {
        $.ajax({
            url: 'index.php?ctl=' + requestLocation,
            data: params,
            type: requestType,
            async: async,
            success: success,
            error: error,
        });
    }

    static defaultAjaxSuccessAction(data) {
        //var jsonData = JSON.parse(data);
    }

    static defaultAjaxErrorAction(data) {
        sendNotification("Ha surgido un error al realizar la operación", true);
    }

    static genericAjaxRequest(requestName, params, success, error = null) {
        if (error == null) {
            error = function (data) {
                sendNotification("Couldn't execute operation succesfully", true);
            };
        }

        AjaxController.request(requestName, "POST", params, success, error);
    }

    static getEventsFromMonth(month, year, success) {
        AjaxController.genericAjaxRequest("getEventsFromMonth", {
            "month": month,
            "year": year,
        }, success);
    }

    static createEvent(title, startHour, date, classroom, success) {
        AjaxController.genericAjaxRequest("createEvent", {
            "title": title,
            "startHour": startHour,
            "date": date,
            "classroom": classroom,
        }, success);
    }

    static updateEvent(title, startHour, date, success) {
        AjaxController.genericAjaxRequest("updateEvent", {
            "title": title,
            "startHour": startHour,
            "date": date,
        }, success);
    }

    static deleteEvent(startHour, date, classroom, success) {
        AjaxController.genericAjaxRequest("deleteEvent", {
            "startHour": startHour,
            "date": date,
            "classroom": classroom
        }, success);
    }

    static getTeachers(success) {
        AjaxController.genericAjaxRequest("getTeachers", {}, success);
    }

    static getClassrooms(success) {
        AjaxController.genericAjaxRequest("getClassrooms", {}, success);
    }

    static getSchedules(success) {
        AjaxController.genericAjaxRequest("getSchedules", {}, success);
    }

    static getSchedule(year, success) {
        AjaxController.genericAjaxRequest("getSchedule", {
            "year": year,
        }, success);
    }

    static getEventsFromWeek(startingDay, endingDay, classroom, success) {
        AjaxController.genericAjaxRequest("getEventsFromWeek", {
            "startingDate": startingDay,
            "endingDate": endingDay,
            "classroom": classroom,
        }, success);
    }

    static getEventsFromDay(selectedDay, classroom, success) {
        AjaxController.genericAjaxRequest("getEventsFromDay", {
            "selectedDay": selectedDay,
            "classroom": classroom,
        }, success);
    }

    //Teacher
    static signup(name, username, password, email, success) {
        AjaxController.genericAjaxRequest("signup", {
            "inputTeacherName": name,
            "inputTeacherUsername": username,
            "inputTeacherPassword": password,
            "inputTeacherEmail": email,
        }, success);
    }

    static updateTeacher(name, username, password, email, success) {
        AjaxController.genericAjaxRequest("updateTeacher", {
            "inputTeacherName": name,
            "inputTeacherUsername": username,
            "inputTeacherPassword": password,
            "inputTeacherEmail": email,
        }, success);
    }

    static deleteTeacher(email, success) {
        AjaxController.genericAjaxRequest("deleteTeacher", {
            "inputTeacherEmail": email,
        }, success);
    }

    //Classroom
    static createClassroom(name, description, state, success) {
        AjaxController.genericAjaxRequest("createClassroom", {
            "inputClassroomName": name,
            "inputClasroomDescription": description,
            "selectClasroomState": state,
        }, success);
    }

    static updateClassroom(name, description, state, success) {
        AjaxController.genericAjaxRequest("updateClassroom", {
            "inputClassroomName": name,
            "inputClasroomDescription": description,
            "selectClasroomState": state,
        }, success);
    }

    static deleteClassroom(name, success) {
        AjaxController.genericAjaxRequest("deleteClassroom", {
            "inputClassroomName": name,
        }, success);
    }

    //Schedule
    static createSchedule(name, description, state, success) {
        AjaxController.genericAjaxRequest("createSchedule", {
            "inputClassroomName": name,
            "inputClasroomDescription": description,
            "selectClasroomState": state,
        }, success);
    }

    static updateSchedule(name, description, state, success) {
        AjaxController.genericAjaxRequest("updateSchedule", {
            "inputClassroomName": name,
            "inputClasroomDescription": description,
            "selectClasroomState": state,
        }, success);
    }

    static deleteSchedule(name, description, state, success) {
        AjaxController.genericAjaxRequest("deleteSchedule", {
            "inputClassroomName": name,
            "inputClasroomDescription": description,
            "selectClasroomState": state,
        }, success);
    }

    static doesUsernameExist(year, success) {
        AjaxController.genericAjaxRequest("doesUsernameExist", {
            "year": year,
        }, success);
    }

    static getNonWorkWeeklyDays(success) {
        AjaxController.genericAjaxRequest("getNonWorkWeeklyDays", {}, success);
    }

    static getMonthlyNonSchoolDays(year, month, success) {
        AjaxController.genericAjaxRequest("getMonthlyNonSchoolDays", {
            "year": year,
            "month": month,
        }, success);
    }
}