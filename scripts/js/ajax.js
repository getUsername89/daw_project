function queryDatabase(requestLocation, success, message = "Ha surgido un error al realizar la operación") {
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
}

function getAllEventsFromMonth(month) {
    request(requestLocation, "POST", {
        select: "",
        from: "table",
        where: "condition",
        params: [
            ["key", "value"],
            ["key", "value"],
            ["key", "value"],
        ],
    }, getAllEventsFromMonth,
    function() {
        sendNotification(message, true);
    });
}

function getAllEventsFromMonth(data) {
    
}