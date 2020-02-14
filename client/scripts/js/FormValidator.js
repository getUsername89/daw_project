class FormValidator {
    validateForm(formName, form) {
        var value = false;

        switch (formName) {
            case "signup":
                value = this.validateSignUp(form);
                break;
            case "signin":
                value = this.validateSignIn(form);
                break;
            case "classroom":
                value = this.validateClassroom(form);
                break;
            case "schedule":
                value = this.validateSchedule(form);
                break;
            case "events":
                value = this.validateEvents(form);
                break;
        }

        return value;
    }

    validateSignUp(form) {
        var rules = [{
                "fieldName": "name",
                "rules": "noEmpty,name",
            },
            {
                "fieldName": "username",
                "rules": "noEmpty,username",
            },
            {
                "fieldName": "image",
                "rules": "noEmpty,image",
            },
            {
                "fieldName": "password",
                "rules": "noEmpty,password",
            },
            {
                "fieldName": "email",
                "rules": "noEmpty,email",
            },
        ];
        var inputs = {
            "name": form.find("#inputName"),
            "username": form.find("#inputUsername"),
            "image": form.find("#inputImage"),
            "password": form.find("#inputPassword"),
            "email": form.find("#inputEmail"),
        };

        return Validator.validate(rules, inputs);
    }

    validateSignIn(form) {
        var rules = [{
                "fieldName": "name",
                "rules": "noEmpty,name",
            },
            {
                "fieldName": "username",
                "rules": "noEmpty,username",
            },
        ];
        var inputs = {
            "name": form.find("#inputName"),
            "username": form.find("#inputUsername"),
        };

        return Validator.validate(rules, inputs);
    }

    validateClassroom(form) {
        var rules = [{
                "fieldName": "name",
                "rules": "noEmpty,name",
            },
            {
                "fieldName": "description",
                "rules": "noEmpty,text",
            },
            {
                "fieldName": "state",
                "rules": "noEmpty,state",
            },
        ];
        var inputs = {
            "name": form.find("#inputClassroomName"),
            "description": form.find("#inputClasroomDescription"),
            "state": form.find("#selectClasroomState"),
        };

        return Validator.validate(rules, inputs);
    }

    validateSchedule(form) {
        var rules = [{
                "fieldName": "startHour",
                "rules": "noEmpty,datetime",
            },
            {
                "fieldName": "endHour",
                "rules": "noEmpty,datetime",
            },
        ];
        var inputs = {
            "startHour": form.find("#inputScheduleStartHour"),
            "endHour": form.find("#inputScheduleEndHour"),
        };

        return Validator.validate(rules, inputs);
    }

    validateEvents(form) {
        var rules = [{
            "fieldName": "inputTitle",
            "rules": "noEmpty,name",
        }];
        var inputs = {
            "startHour": form.find("#inputTitle"),
        };

        return Validator.validate(rules, inputs);
    }
}