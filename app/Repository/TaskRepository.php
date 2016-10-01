<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('app/Model/Task.php');
require_once('app/Model/Subtask.php');

/**
 * Description of TaskRepository
 *
 * @author jnx
 */
class TaskRepository {

    public function create($description, $userId) {
        $stmt = "INSERT INTO task(status,description,user_id) VALUES(0,'$description',$userId)";
        return Database::execQuery($stmt);
    }

    public function update($id, $set, $userId, $statusDone = false) {
        if ($statusDone) {
            self::updateSubtaskStatus($id, 1, $userId);
        }
        $stmt = "UPDATE task SET $set WHERE id=$id AND user_id=$userId";
        return Database::execQuery($stmt);
    }

    public function delete($id, $userId) {
        $stmt = "DELETE FROM task WHERE id=$id AND user_id=$userId";
        return Database::execQuery($stmt);
    }

    public function selectOneByUser($id, $userId) {
        $stmt = "SELECT * FROM task WHERE id=$id AND user_id=$userId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectOne($id) {
        $stmt = "SELECT * FROM task WHERE id=$id";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectAllByUser($userId) {
        $stmt = "SELECT * FROM task WHERE user_id=$userId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function updateSubtaskStatus($id, $status, $userId) {
        $subtask = new SubtaskRepository();
        $subtaskList = $subtask->selectAllByTaskIdAndUser($id, $userId);
        foreach ($subtaskList as $currentSubtask) {
            $subtask->update($currentSubtask['id'], "status=$status", $userId);
        }
    }

    public function returnArray($result) {
        $subtaskRepository = new SubtaskRepository();
        $return = array();
        foreach ($result as $current) {
            $subtasks = $subtaskRepository->selectAllByTaskIdAndUser($current['id'], $current['user_id']);
            if(count($subtasks)===0){
                $subtasks = "";
            }
            $return[] = array(
                'id' => $current['id'],
                'descr' => $current['description'],
                'status' => $current['status'],
                'user_id' => $current['user_id'],
                'subtasks' => $subtasks,
            );
        }
        return $return;
    }

}
