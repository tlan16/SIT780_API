<?php

switch (strtok($_SERVER["REQUEST_URI"], '?')) {
    case '/':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            sendResponse(['Hello', 'API']);
            break;
        }
        break;
    case '/students':
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            include_once "models/student.php";
            sendResponse(Student::getAll());
            break;
        }
        break;
    case '/student':
        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
            if (empty($_GET['id']))
                sendResponse(400, 'missing required query parameter `id`');
            else {
                $id = $_GET['id'];

                include_once "models/student.php";
                try {
                    $student = new Student($id);
                    $student->delete();
                    $student->save();
                    sendResponse();
                } catch (Exception $e) {
                    sendResponse(400, $e->getMessage());
                }
                break;
            }
        }
        break;
}

// 404
//sendResponse(404);