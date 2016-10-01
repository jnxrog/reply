<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/Model/Database.php';
require_once 'app/Repository/SubtaskRepository.php';

/**
 * Description of Task
 *
 * @author jnx
 */
class Task {

    private $id;
    private $description;
    private $status;
    private $userId;
    private $subtasks = [];

    function __construct($id, $description, $status, $userId, array $subtasks) {
        $this->id = $id;
        $this->description = $description;
        $this->status = $status;
        $this->userId = $userId;
        $this->subtasks = $subtasks;
    }

    function getId() {
        return $this->id;
    }

    function getDescription() {
        return $this->description;
    }

    function getStatus() {
        return $this->status;
    }

    function getUserId() {
        return $this->userId;
    }

    function getSubtasks() {
        $subtaskRepository = new SubtaskRepository();
        return $subtaskRepository->selectAllByTaskIdAndUser($this->id, $this->userId);
    }

    function setId($id) {
        $this->id = $id;
    }

    function setDescription($description) {
        $this->description = $description;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setUserId($user) {
        $this->userId = $userId;
    }

    function setSubtasks($subtasks) {
        $this->subtasks = $subtasks;
    }

}
