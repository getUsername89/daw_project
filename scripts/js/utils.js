function minNumberOfDigits(number, numberOfDigits = 2) {
    return number.toLocaleString("es", {
        minimumIntegerDigits: numberOfDigits,
    });
}

function getWeekFromDate(dayDate) {
    var weekDates = [];
    var workDate = dayDate;
    var dayInNumber = dayDate.getDay();
    var startingNumber = 1;
    if (dayInNumber >= 1) {
        startingNumber = 0 - dayInNumber;
    }
    
    var currentDateInFor = workDate;
    currentDateInFor.setDate(currentDateInFor.getDate() + (startingNumber));
    for (var index = 0; index < 7; index++) {
        currentDateInFor.setDate(currentDateInFor.getDate() + 1);
        weekDates.push(new Date(currentDateInFor.getTime()));
        startingNumber++;
    }

    return weekDates;
}

function sendNotification(message, error = false) {
    setTimeout(() => {

    }, timeout);
}

var schedule = [
    ["7:55", "8:50"],
    ["8:50", "9:45"],
    ["9:45", "10:40"],
    ["11:00", "11:55"],
    ["11:55", "12:50"],
    ["12:50", "13:45"],
    ["14:05", "15:00"],
    ["15:00", "15:55"],
    ["15:55", "16:50"],
    ["16:50", "17:45"],
    ["18:05", "19:00"],
    ["19:00", "19:55"],
    ["19:55", "20:50"],
    ["20:50", "21:10"],
    ["21:10", "22:05"]
];