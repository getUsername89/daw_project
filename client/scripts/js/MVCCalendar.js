/**
 * @class Model
 *
 * Manages the data of the application.
 */

let calendarController;

class Model {
    constructor() {
        this.currentEvents = [];
        this.currentDate = new Date();
        var model = this;
        AjaxController.getEventsFromMonth(this.currentDate.getMonth(), this.currentDate.getFullYear(), function (data) {
            model.currentEvents = data;
        });
        this.instace = this;
        model.schedule = schedule;
    }

    instance = null;

    getInstance() {
        if (instance == null) {
            instance = new Model();
        }

        return instance;
    }


    //AJAX CRUD for calendar
    addEvent(title, startHour, date, success) {
        var instance = this.instance;
        AjaxController.createEvent(title, startHour, date, function (data) {
            instance.currentEvents.push({
                "title": title,
                "startHour": startHour,
                "date": date,
            });
            success(data);
        });
    }

    removeEvent(startHour, date, success) {
        var instance = this.instance;
        AjaxController.removeEvent(title, startHour, date, function (data) {
            instance.currentEvents = data;
            success(data);
        });
    }

    updateEvent(title, startHour, date, success) {
        var instance = this.instance;
        AjaxController.updateEvent(title, startHour, date, function (data) {
            instance.currentEvents = data;
            instance.currentEvents.forEach(element => {
                element.startHour
            });
            success(data);
        });
    }

    getEventsFromWeek(date, days) {
        var instance = this.instance;
        AjaxController.getEventsFromWeek(date, days, function (data) {
            instance.currentEvents = data;
            success(data);
        });
    }

    getEventsFromDay(date, days, classroom, success) {
        var instance = this.instance;
        AjaxController.getEventsFromDay(date, days, classroom, function (data) {
            instance.currentEvents = data;
            success(data);
        });
    }

    getSchedule(date, days) {
        var instance = this.instance;
        AjaxController.getSchedule(date, days, function (data) {
            instance.currentEvents = data;
            success(data);
        });
    }
}

/**
 * @class View
 *
 * Visual representation of the model.
 */
class View {
    constructor() {
        this.mainContainer = $('main');

        this.weekFormat = $(`<div id="toolbar"
                class="w-100 rounded d-flex justify-content-around flex-wrap py-3 text-white mb-5 bg-dark align-items-center">

                <div class="form-group form-inline m-0">
                    <label class='d-none d-sm-block' for="monthDatePicker">Month</label>
                    <input type="date" name="monthDatePicker" id="monthDatePicker" class="form-control ml-4">
                </div>
                <div class="form-group form-inline m-0">
                    <label class='d-none d-sm-block' for="weekDatePicker">Week</label>
                    <input type="date" name="weekDatePicker" id="weekDatePicker" class="form-control ml-4">
                </div>
                <div class="form-group form-inline m-0">
                    <label class='d-none d-sm-block' for="weekFormat">Change Week/Month calendar format:</label>
                    <button type="button" id="weekFormat" class="btn ml-4 btn-primary">Week</button>
                </div>
            </div>`);
        this.mainContainer.append(this.weekFormat);
        this.mainContainer.before('<div class="d-block d-sm-none h-25 my-4 w-100">&nbsp;</div>');

        //Weekly calendar
        this.weeklyCalendarContainer = $('<section class="w-100"></section>');
        var weeklyRow = $("<div class='row'></div");
        this.timeTableWeek = $('<div id="timeTableWeek" class="col-md-12 mini-cal row"></div>');
        weeklyRow.append(this.timeTableWeek);
        this.weeklyCalendarContainer.append(weeklyRow);

        //Monthly calendar
        this.monthlyCalendarContainer = $('<section class="w-100"></section>');
        var monthlyRow = $("<div class='row'></div");
        this.monthCalendar = $('<div id="calendar" class="col-md-8 col-12 px-0"></div>');
        this.timeTableDay = $('<div id="timeTable" class="col-md-4 col-12 px-0"></div>');
        monthlyRow.append(this.monthCalendar, this.timeTableDay);
        this.monthlyCalendarContainer.append(monthlyRow);

        this.mainContainer.append(this.weeklyCalendarContainer, this.monthlyCalendarContainer);
        this.weeklyCalendarContainer.hide();

        //Week format
        this.weekFormat.find("#weekFormat").on("click", function () {
            var current = $(this);
            current.toggleClass("btn-primary");
            current.toggleClass("btn-warning");

            if (current.hasClass("btn-warning")) {
                current.text("Month");
            } else {
                current.text("Week");
            }

            $("main section").toggle();
        });
    }

    fadeOutItem(item, miliseconds = 250) {
        item.fadeOut(250);
        setTimeout(() => {
            item.hide();
        }, miliseconds);
    }

    fadeInItem(item) {
        item.show();
        item.fadeIn(250);
    }

    instance = null;

    getInstance() {
        if (instance == null) {
            instance = new View();
        }

        return instance;
    }
}

/**
 * @class Controller
 *
 * Links the user input and the view output.
 *
 * @param model
 * @param view
 */
class Controller {
    instance = null;

    constructor(model, view) {
        this.model = model;
        this.view = view;
        var controller = this;


        $("input[type=date]").val(printDateWithFormat(new Date(), "Y-m-d"));
        $("input[type=date]").on("change", function () {
            var value = $(this).val();

            $("input[type=date]").val(value);
            controller.model.currentDate = new Date(Date.parse(value));
            var newWeekDate = getWeekFromDate(controller.model.currentDate);
            controller.updateWeekCalendar(newWeekDate[0], newWeekDate[newWeekDate.length - 1], false);
            //controller.start(controller);

        });

        controller.start(this);
        controller.view.timeTableWeek.TT({
            events: [],
            schedule: controller.model.schedule,
            day: controller.model.currentDate,
            weekFormat: true,
            onWeekChange: controller.updateWeekCalendar,
        });
        var startingWeekDates = getWeekFromDate(new Date());
        this.updateWeekCalendar(startingWeekDates[0], startingWeekDates[startingWeekDates.length - 1])
    }

    //When the week changes it triggers this event
    updateWeekCalendar(startingDate, endingDate, updateInput = true) {
        if (updateInput) {
            $("input[type=date]").val(printDateWithFormat(startingDate, "Y-m-d"));
        }
        AjaxController.getEventsFromWeek(printDateWithFormat(startingDate, "Y-m-d"), printDateWithFormat(endingDate, "Y-m-d"), $("#classroomId").text(), function (data) {
            /* console.log("startingDate", printDateWithFormat(startingDate, "Y-m-d"),
                "endingDate", printDateWithFormat(endingDate,
                    "Y-m-d"),
                "classroomId", $("#classroomId").text()); */
            calendarController.view.timeTableWeek.TT({
                events: JSON.parse(data),
                schedule: calendarController.model.schedule,
                day: startingDate,
                weekFormat: true,
                onWeekChange: calendarController.updateWeekCalendar,
            });
        });
    }

    //To start/restart the calendar
    start(controller) {
        //console.log(controller.model);

        //getEventsFromWeek(date, days);

        controller.view.timeTableDay.TT({
            events: controller.model.getEventsFromDay(controller.model.currentDate.getUTCDate()),
            schedule: controller.model.schedule,
            day: controller.model.currentDate,
            weekFormat: false
        });

        controller.view.monthCalendar.html("");
        /*var inputDate = $("input[type=date]").first().val();
        var parts = inputDate.split('-');
        var newDate = new Date(parts[0], parts[1] - 1, parts[2]);
        console.log(newDate);*/

        controller.calendarControls = new CalendarControls(
            controller.view.monthCalendar,
            controller.model.currentEvents,
            new Date(Date.parse($("*[type=date]").val()))
        );

        controller.calendarControls.onDayClick = function () {
            controller.onDayClick($(this), controller);
        };

        controller.calendarControls.setOnMonthChanged(function (month, year) {
            controller.onMonthChanged(month, year, controller);
            var newDate = new Date(year, month, 1);
            $("input[type=date]").val(printDateWithFormat(newDate, "Y-m-d"));
            controller.model.currentDate = new Date(newDate);
        });


    }

    //When a day is clicked
    onDayClick(current, controller) {
        var dayInNumber = parseInt(current.text());

        var newDate = new Date(
            controller.model.currentDate.getFullYear(),
            controller.model.currentDate.getMonth(),
            dayInNumber
        );

        //console.log("Day  - " + newDate.toString());
        //console.log("Day  - " + dayInNumber);

        var date = printDateWithFormat(newDate, "Y-m-d");
        $("input[type=date]").val(printDateWithFormat(newDate, "Y-m-d"));

        var classroom = $("#classroomId").text();

        AjaxController.getEventsFromDay(date, classroom, function (data) {
            //console.log("AJAX result", data);
            var parsedData = JSON.parse(data);
            //console.log("Parsed JSON", parsedData);
            controller.model.currentEvents = parsedData;
            controller.model.currentDate.setDate(newDate.getDate());
            controller.view.timeTableDay.TT({
                events: controller.model.currentEvents,
                schedule: controller.model.getSchedule(),
                day: newDate
            });

            setTimeout(() => {
                $("event-card").each(function () {
                    var shadowRoot = $(this.shadowRoot);

                    shadowRoot.find("#pickEvent").on("click", pickEvent);
                    shadowRoot.find("#pickEvent").on("touch", pickEvent);

                    shadowRoot.find("#removeEvent").on("click", removeEvent);
                    shadowRoot.find("#removeEvent").on("touch", removeEvent);
                });
            }, 100);
        });
    }

    //Remove an event from a day
    removeEvent() {
        var focused = $(".focused");

        if (focused.length == 1) {
            Modal.confirmModal(function () {
                AjaxController.deleteEvent(shadowRoot.find("#eventStartHour").text(), printDateWithFormat(controller.model.currentDate, "Y-m-d"), $("#classroomId").html(), function success(data) {
                    focused.prev().trigger("click");
                    focused.trigger("click");
                });
            });
        }
    }

    //Select and event from a day
    pickEvent() {
        var focused = $(".focused");

        if (focused.length == 1) {
            Modal.genericModalWithForm("Event", false, function (modalContent) {
                $("*[type=submit]").on("click", function (event) {
                    var event = event || window.event
                    event.preventDefault();
                    modalContent.close();
                    return false;
                });

                var form = $("form .form");
                form.prepend("<input type='hidden' name='classroom' id='classroom' value='" + $("#classroomId").html() + "'>");
                form.prepend("<input type='hidden' name='date' id='date' value='" + printDateWithFormat(controller.model.currentDate, "Y-m-d") + "'>");
                form.prepend("<input type='hidden' name='startHour' id='startHour' value='" + shadowRoot.find("#eventStartHour").text() + "'>");
                form.find(":submit").on("click", function (event) {
                    var event = event || window.event;
                    event.preventDefault();

                    AjaxController.createEvent(form.find("#title").val(), form.find("#startHour").val(), form.find("#date").val(), form.find("#classroom").val(), function success(data) {
                        focused.prev().trigger("click");
                        focused.trigger("click");
                    });
                })
            });
        }
    }

    //When month changes
    onMonthChanged(month, year, controller) {
        controller.model.currentDate = new Date(year, month, 2);
        controller.calendarControls.setOnDayClick();
    }

    static getInstance() {
        if (!Controller.instance) {
            Controller.instance = new Controller(new Model(), new View());
        }

        return Controller.instance;
    }
}

//calendarController = new Controller(new Model(), new View());
calendarController = Controller.getInstance();