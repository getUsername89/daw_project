<!--Page configuration-->
<?php $optionalCSS = ["mini-event-calendar.min.css", "timetable.css"]; ?>
<?php $optionalScripts = ["libs/time-table.js", "libs/mini-event-calendar.js", "js/calendar-controls.js", "js/MVCCalendar.js",]; ?>
<?php $title = "Booking area"; ?>
<?php $mainClasses = ""; ?>
<?php $showFooter = true; ?>
<?php $showHeader = true; ?>

<?php ob_start() ?>

<?php $contenido = ob_get_clean(); ?>

<?php include_once 'layout.php' ?>