<?php

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            json_response(['Hello', 'API']);
            break;
        }
        break;
    case '/students':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            include_once "models/student.php";
            json_response(Student::getAll());
            break;
        }
        break;
}

// 404
ob_start();
header("X-PHP-Response-Code: 404", true, 404);
ob_end_flush();
ob_flush();
flush();