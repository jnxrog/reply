<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// replace the following line, make sure the user has authenticated and assign his id to this variable
$authenticatedUserId = 1;

require_once 'app/Model/Database.php';
require_once 'app/Repository/TaskRepository.php';
require_once 'app/Repository/SubtaskRepository.php';
require_once 'app/Model/Task.php';
require_once 'app/Model/Subtask.php';

$link = Database::getLink();
mysqli_set_charset($link, 'utf8');
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

if (isset($request[0]) && preg_match('/^[a-z]*$/', $request[0])) {
    if ($request[0] === 'tasks') {
        $subject = 'tasks';
    } elseif ($request[0] === 'subtasks') {
        $subject = 'subtasks';
    } else {
        returnHeader(400, $link, 'Bad Request - Bad Resource');
    }
} else {
    returnHeader(400, $link, 'Bad Request - No Resource');
}

if (isset($request[1])) {
    $subject_id = $request[1];
} else {
    $subject_id = null;
}

//allowed subjects per verb - move to external config
$router = array(
    'GET' => ['tasks', 'subtasks'],
    'POST' => ['tasks', 'subtasks'],
    'PUT' => ['tasks', 'subtasks'],
    'DELETE' => ['tasks', 'subtasks'],
);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (!in_array($subject, $router['GET'])) {
            returnHeader(401, $link, 'Unauthorized');
        }
        $action = 'index';
        break;
    case 'POST':
        if (!in_array($subject, $router['POST'])) {
            returnHeader(401, $link, 'Unauthorized');
        }
        $action = 'create';
        break;
    case 'PUT':
        if (!in_array($subject, $router['PUT'])) {
            returnHeader(401, $link, 'Unauthorized');
        }
        $action = 'update';
        break;
    case 'DELETE':
        if (!in_array($subject, $router['DELETE'])) {
            returnHeader(401, $link, 'Unauthorized');
        }
        $action = 'delete';
        break;
    default:
        die('VERB NOT ALLOWED');
}

echo $subject($link, $subject_id, $action, $authenticatedUserId);

mysqli_close($link);
exit(0);

/*
 * 
 * 
 * end of main script
 * 
 * functions following (cases in switches can be integrated as actions in controllers)
 * 
 * 
 */

function returnHeader($code, $link, $message) {
    if ($code >= 400) {
        $status = "fail";
    } else {
        $status = "success";
    }
    $msg = '{"status":"' . $status . '","message":"' . $message . '"}';
    http_response_code($code);
    mysqli_close($link);
    exit($msg);
}

function tasks($link, $subject_id, $action, $userId) {
    switch ($action) {
        case 'index':
            $taskRepository = new TaskRepository();

            if ($subject_id === null) {

                $taskResult = $taskRepository->selectAllByUser($userId);
            } elseif (preg_match('/^[1-9][0-9]*$/', $subject_id)) {

                $getSubtasks = true;
                $taskResult = $taskRepository->selectOneByUser($subject_id, $userId);
            } else {

                returnHeader(400, $link, 'Invalid Resource ID (must be number)');
            }

            if (count($taskResult) > 0) {

                return json_encode($taskResult);
            } else {

                $taskResult = $taskRepository->selectOne($subject_id);
                count($taskResult) > 0 ?
                                //if resource returned it means it exists but does not belong to the user
                                returnHeader(401, $link, 'Resource does not belong to User') :
                                //if resource still not found it does not exist
                                returnHeader(404, $link, 'Resource not found')
                ;
            }

            break;
        case 'create':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            $taskRepository = new TaskRepository();
            if (isset($requestBody['description']) && isset($requestBody['user_id'])) {
                if ($taskRepository->create(
                                mysqli_real_escape_string($link, $requestBody['description']
                                ), mysqli_real_escape_string($link, $requestBody['user_id'])
                        )
                ) {
                    returnHeader(201, $link, "Created");
                }
                returnHeader(500, $link, "Mysql query failed");
            } else {
                returnHeader(412, $link, "Precondition failed - Need 'description' and 'user_id' in POST Body (json format)");
            }
            break;
        case 'update':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            $taskRepository = new TaskRepository();

            if ((isset($requestBody['description']) || isset($requestBody['status'])) && isset($requestBody['id']) && isset($requestBody['user_id'])) {
                isset($requestBody['description']) ?
                                $set = "description='" . trim(mysqli_real_escape_string($link, $requestBody['description'])) . "'" :
                                $set = "";

                if (isset($requestBody['status']) && ($requestBody['status'] === "0" || $requestBody['status'] === "1")) {
                    if (isset($requestBody['description'])) {
                        $set.=",";
                    }
                    $set.= "status='" . mysqli_real_escape_string($link, $requestBody['status']) . "'";
                    $requestBody['status']==="1" ? $statusDone = true : $statusDone = false;
                }

                if ($taskRepository->update($requestBody['id'], $set, $requestBody['user_id'], $statusDone)
                ) {
                    returnHeader(200, $link, "Updated");
                }
                returnHeader(500, $link, "Mysql query failed");
            } else {
                returnHeader(412, $link, "Precondition failed");
            }
            break;
        case 'delete':

            break;
    }
}

function subtasks($link, $subject_id, $action, $userId) {
    switch ($action) {
        case 'index':
            $subtaskRepository = new SubtaskRepository();

            if ($subject_id === null) {

                $subtaskResult = $subtaskRepository->selectAllByUser($userId);
            } elseif (preg_match('/^[1-9][0-9]*$/', $subject_id)) {

                $subtaskResult = $subtaskRepository->selectOneByUser($subject_id, $userId);
            } else {

                returnHeader(400, $link, 'Invalid Resource ID (must be number)');
            }

            if (count($subtaskResult) > 0) {

                return json_encode($subtaskResult);
            } else {

                $subtaskResult = $subtaskRepository->selectOne($subject_id);
                count($subtaskResult) > 0 ?
                                //if resource returned it means it exists but does not belong to the user
                                returnHeader(401, $link, 'Resource does not belong to User') :
                                //if resource still not found it does not exist
                                returnHeader(404, $link, 'Resource not found')
                ;
            }

            break;
        case 'create':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            $subtaskRepository = new SubtaskRepository();
            if (isset($requestBody['description']) && isset($requestBody['task_id']) && isset($requestBody['user_id'])) {
                if ($subtaskRepository->create(
                                trim(mysqli_real_escape_string($link, $requestBody['description']
                                )), mysqli_real_escape_string($link, $requestBody['task_id']
                                ), mysqli_real_escape_string($link, $requestBody['user_id'])
                        )
                ) {
                    returnHeader(201, $link, "Created");
                }
                returnHeader(500, $link, "Mysql query failed");
            } else {
                returnHeader(412, $link, "Precondition failed - Need 'description' 'task_id' and 'user_id' in POST Body (json format)");
            }
            break;
        case 'update':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            $subtaskRepository = new SubtaskRepository();

            if ((isset($requestBody['description']) || isset($requestBody['status'])) && isset($requestBody['id']) && isset($requestBody['user_id'])) {
                isset($requestBody['description']) ?
                                $set = "description='" . trim(mysqli_real_escape_string($link, $requestBody['description'])) . "'" :
                                $set = "";

                if (isset($requestBody['status']) && ($requestBody['status'] === "0" || $requestBody['status'] === "1")) {
                    if (isset($requestBody['description'])) {
                        $set.=",";
                    }
                    $set.= "status='" . mysqli_real_escape_string($link, $requestBody['status']) . "'";
                }

                if ($subtaskRepository->update($requestBody['id'], $set, $requestBody['user_id'])
                ) {
                    returnHeader(200, $link, "Updated");
                }
                returnHeader(500, $link, "Mysql query failed");
            } else {
                returnHeader(412, $link, "Precondition failed");
            }
            break;
        case 'delete':

            break;
    }
}
