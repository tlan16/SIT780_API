<?php

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        json_response(['Hello', 'API']);
        break;
    case '/students':
        include_once "models/student.php";
        json_response(Student::getAll());
        break;
}