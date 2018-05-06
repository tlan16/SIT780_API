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
        // delete student
        if ($_SERVER['REQUEST_METHOD'] === "DELETE") {
            if (empty($_GET['id']))
                sendResponse(400, 'missing required query parameter `id`');
            else {
                $id = $_GET['id'];
                include_once "models/student.php";

                // check student exist
                $student = Student::getById($id);
                if (!$student instanceof Student) {
                    sendResponse(400, "student with id $id does not exist");
                    break;
                }

                $student->delete();
                $student->save();
                sendResponse();
                break;
            }
        // update student
        } else if ($_SERVER['REQUEST_METHOD'] === "PUT") {
            if (empty($_GET['id'])) {
                sendResponse(400, 'missing required query parameter `id`');
                break;
            }
            $id = $_GET['id'];
            include_once "models/student.php";

            // check student exist
            $student = Student::getById($id);
            if (!$student instanceof Student) {
                sendResponse(400, "student with id $id does not exist");
                break;
            }

            // get payload
            parse_str(file_get_contents("php://input"), $payload);

            // validate email
            if (isset($payload['email'])) {
                $email = $payload['email'];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    sendResponse(400, 'invalid email');
                    break;
                }
                else $student->setEmail($email);
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
            sendResponse();
            break;
        // create student
        } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // validate payload
            if (empty($_POST['id'])) {
                sendResponse(400, 'missing required query parameter `id`');
                break;
            } else $id = $_POST['id'];

            if (empty($_POST['firstname'])) {
                sendResponse(400, 'missing required query parameter `firstname`');
                break;
            } else $firstname = $_POST['firstname'];

            if (empty($_POST['lastname'])) {
                sendResponse(400, 'missing required query parameter `lastname`');
                break;
            } else $firstname = $_POST['lastname'];

            $email = $_POST['email'] ?: '';
            $address = $_POST['address'] ?: '';
            include_once "models/student.php";

            // validate student NOT exist
            $student = Student::getById($id);
            if ($student instanceof Student) {
                sendResponse(400, "student with id $id already exist");
                break;
            }
            $student = new Student($id);

            // email
            if (isset($_POST['email'])) {
                $email = $_POST['email'];
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    sendResponse(400, 'invalid email');
                else $student->setEmail($email);
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
            sendResponse();
            break;
        }
        break;
}

// 404
//sendResponse(404);