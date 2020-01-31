<!--Page configuration-->
<?php $optionalCSS = ["login.css"]; ?>
<?php $optionalScripts = []; ?>
<?php $title = "Admin"; ?>
<?php $mainClasses = "flex-column container col-md-10 px-md-0"; ?>
<?php $showFooter = true; ?>
<?php $showHeader = true; ?>

<?php ob_start() ?>

<form class="form-signin col-md" method="POST">
    <div class="text-center mb-4 text-white">
        <h3>Teacher</h3>
    </div>

    <div class="form-row d-flex justify-content-around align-items-center">
        <div class="form-group col-md-2">
            <label for="inputTeacherUsername">Username (*)</label>
            <input type="text" class="form-control" id="inputTeacherUsername" aria-describedby="teacherUsernameHelp" placeholder="Enter Username" required>
            <small id="teacherUsernameHelp" class="form-text text-muted">✓ Is available.</small>
        </div>

        <div class="form-group col-md">
            <label for="inputTeacherPassword">Password (*)</label>
            <input type="password" class="form-control" id="inputTeacherPassword" aria-describedby="teacherPasswordHelp" placeholder="Password" required>
            <small id="teacherPasswordHelp" class="form-text text-muted">Alphanumeric combination, 8 to 24 characters.</small>
        </div>

        <div class="form-group col-md">
            <label for="inputTeacherName">Full Name (*)</label>
            <input type="text" class="form-control" id="inputTeacherName" aria-describedby="teacherNameHelp" placeholder="Enter name" required>
            <small id="teacherNameHelp" class="form-text text-muted">Enter your full name
                <!--, the one you are legally recognized with-->.</small>
        </div>

        <div class="form-group col-md-3">
            <label for="inputTeacherEmail">Email address (*)</label>
            <input type="email" class="form-control" id="inputTeacherEmail" aria-describedby="teacherEmailHelp" placeholder="Enter email" required>
            <small id="teacherEmailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>

        <div class="form-group col-md-1">
            <button class="btn btn-lg btn-primary btn-block" name="teacherAdd" type="submit">Add</button>
        </div>
    </div>
</form>
<div>
    <br><br>
</div>
<form class="form-signin col-md" method="POST">
    <div class="text-center mb-4 text-white">
        <h3>Classroom</h3>
    </div>

    <div class="form-row d-flex justify-content-around align-items-center">
        <div class="form-group col-md-1">
            <label for="inputClassroomName">Name (*)</label>
            <input type="text" class="form-control" id="inputClassroomName" aria-describedby="classroomNameHelp" placeholder="208" required>
            <small id="classroomNameHelp" class="form-text text-muted">Code class.</small>
        </div>

        <div class="form-group col-md">
            <label for="inputClasroomDescription">Short description (optional)</label>
            <input type="text" class="form-control" id="inputClasroomDescription" aria-describedby="classroomDescriptionHelp" placeholder="Every student is going to pass">
            <small id="classroomDescriptionHelp" class="form-text text-muted">Short description about it's details.</small>
        </div>

        <div class="form-group col-md-3">
            <label for="selectClasroomState">State (*)</label>
            <select class="form-control" id="selectClasroomState" aria-describedby="clasroomStateHelp">
                <option>Perfect</option>
                <option>On repair</option>
                <option>Left out</option>
            </select>
            <small id="clasroomStateHelp" class="form-text text-muted">How's the classroom?</small>
        </div>

        <div class="form-group col-md-1">
            <button class="btn btn-lg btn-primary btn-block" name="classroomAdd" type="submit">Add</button>
        </div>
    </div>
</form>
<div>
    <br>
</div>
<form class="form-signin col-md" method="POST">
    <div class="text-center mb-4 text-white">
        <h3>Schedule</h3>
    </div>

    <div class="form-row d-flex justify-content-center align-items-center">
        <div class="form-group col-md-1">
            <label for="inputScheduleStartHour">Start hour (*)</label>
            <input type="text" class="form-control" id="inputScheduleStartHour" aria-describedby="scheduleStartHourHelp" placeholder="7:55" required>
            <small id="scheduleStartHourHelp" class="form-text text-muted">Event starts at.</small>
        </div>

        <div class="form-group col-md-1">
            <label for="inputScheduleEndHour">End hour (*)</label>
            <input type="text" class="form-control" id="inputScheduleEndHour" aria-describedby="scheduleEndHourHelp" placeholder="7:55" required>
            <small id="scheduleEndHourHelp" class="form-text text-muted">Event ends at.</small>
        </div>

        <div class="form-group col-md-1">
            <button class="btn btn-lg btn-primary btn-block" name="scheduleAdd" type="submit">Add</button>
        </div>
    </div>
</form>

<?php $contenido = ob_get_clean() ?>

<?php include 'layout.php' ?>