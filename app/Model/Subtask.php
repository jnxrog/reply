<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/Model/Database.php';

/**
 * Description of Subtask
 *
 * @author jnx
 */
class Subtask {


    private $id;
    private $description;
    private $status;
    private $taskId;
    private $usedId;
    
    function __construct($id, $description, $status, $taskId, $usedId) {
        $this->id = $id;
        $this->description = $description;
        $this->status = $status;
        $this->taskId = $taskId;
        $this->usedId = $usedId;
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

    function getTaskId() {
        return $this->taskId;
    }

    function getUsedId() {
        return $this->usedId;
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

    function setTaskId($taskId) {
        $this->taskId = $taskId;
    }

    function setUsedId($usedId) {
        $this->usedId = $usedId;
    }

}
