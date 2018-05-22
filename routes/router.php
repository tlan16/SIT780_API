<?php

include_once dirname(__FILE__) . "/../models/student.php";
include_once dirname(__FILE__) . '/../models/credential.php';
include_once dirname(__FILE__) . '/../models/session.php';

switch (strtok($_SERVER["REQUEST_URI"], '?')) {
    case '/':
        if ($_SERVER['REQUEST_METHOD'] === "GET")
            return sendResponse(200, ['Hello', 'API']);
        break;
    case '/students':
        if ($_SERVER['REQUEST_METHOD'] === "GET")
            return sendResponse(200, Student::getAll());
        break;
    case '/student':
        // delete student
        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
            if (empty($_GET['id']))
                return sendResponse(400, 'missing required query parameter `id`');

            $id = $_GET['id'];
            // check student exist
            $student = Student::getById($id);
            if (!$student instanceof Student)
                return sendResponse(400, "student with id $id does not exist");

            $student->delete();
            $student->save();
            sendResponse();
        // update student
        } else if ($_SERVER['REQUEST_METHOD'] === "PUT") {
            if (empty($_GET['id']))
                return sendResponse(400, 'missing required query parameter `id`');

            $id = $_GET['id'];
            // check student exist
            $student = Student::getById($id);
            if (!$student instanceof Student)
                return sendResponse(400, "student with id $id does not exist");

            // get payload
            parse_str(file_get_contents("php://input"), $payload);

            // validate email
            if (isset($payload['email'])) {
                $email = $payload['email'];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    sendResponse(400, 'invalid email');
                    break;
                } else $student->setEmail($email);
            }

            // update firstname - cannot be empty
            if (!empty($payload['firstname']))
                $student->setFirstname($payload['firstname']);

            // update lastname - cannot be empty
            if (!empty($payload['lastname']))
                $student->setLastname($payload['lastname']);

            // update address
            if (isset($payload['address']))
                $student->setAddress($payload['address']);

            $student->save();
            return sendResponse();
        // create student
        } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // validate payload
            if (empty($_POST['id']))
                return sendResponse(400, 'missing required query parameter `id`');
            $id = $_POST['id'];

            if (empty($_POST['firstname']))
                return sendResponse(400, 'missing required query parameter `firstname`');
            $firstname = $_POST['firstname'];

            if (empty($_POST['lastname']))
                return sendResponse(400, 'missing required query parameter `lastname`');
            $firstname = $_POST['lastname'];

            $email = $_POST['email'] ?: '';
            $address = $_POST['address'] ?: '';

            // validate student NOT exist
            $student = Student::getById($id);
            if ($student instanceof Student)
                return sendResponse(400, "student with id $id already exist");
            $student = new Student($id);

            // email
            if (isset($_POST['email'])) {
                $email = $_POST['email'];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    return sendResponse(400, 'invalid email');
                $student->setEmail($email);
            }

            // firstname - cannot be empty
            if (!empty($_POST['firstname']))
                $student->setFirstname($_POST['firstname']);

            // lastname - cannot be empty
            if (!empty($_POST['lastname']))
                $student->setLastname($_POST['lastname']);

            // address
            if (isset($_POST['address']))
                $student->setAddress($_POST['address']);

            $student->save();
            return sendResponse();
        }
        break;
    case '/sensor':
        $filePath = dirname(__FILE__) . "/asset/sensor.json";
        if (file_exists($filePath))
            return sendResponse(200, json_decode(file_get_contents($filePath), true));
        return sendResponse(500, 'missing sensor data file.');
    case '/login':
        $studentId = $_SERVER['PHP_AUTH_USER'] ?: '';
        $password = $_SERVER['PHP_AUTH_PW'] ?: '';

        $credential = (new Credential())->load($studentId);
        if (
            !$credential instanceof Credential
            || !$credential->verifyPassword($password)
        ) return sendResponse(403);

        $session = Session::create($studentId);
        return sendResponse(200, $session->getToken());
}

// 404
@sendResponse(404);