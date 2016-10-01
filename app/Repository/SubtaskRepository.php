<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('app/Model/Subtask.php');

/**
 * Description of SubtaskRepository
 *
 * @author jnx
 */
class SubtaskRepository {

    public function create($description, $taskId, $userId) {
        $stmt = "INSERT INTO subtask(status,description,task_id,user_id) VALUES(0,'$description',$taskId,$userId)";
        return Database::execQuery($stmt);
    }

    public function update($id, $set, $userId) {
        $stmt = "UPDATE subtask SET $set WHERE id=$id AND user_id=$userId";
        return Database::execQuery($stmt);
    }

    public function delete($id, $userId) {
        $stmt = "DELETE FROM subtask WHERE id=$id AND user_id=$userId";
        return Database::execQuery($stmt);
    }

    public function selectOne($id) {
        $stmt = "SELECT * FROM subtask WHERE id=$id";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectOneByUser($id, $userId) {
        $stmt = "SELECT * FROM subtask WHERE id=$id AND user_id=$userId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectAllByTaskIdAndUser($taskId, $userId) {
        $stmt = "SELECT * FROM subtask WHERE task_id=$taskId AND user_id=$userId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectAllByTaskId($taskId) {
        $stmt = "SELECT * FROM subtask WHERE task_id=$taskId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function selectAllByUser($userId) {
        $stmt = "SELECT * FROM subtask WHERE user_id=$userId";
        return $this->returnArray(Database::execQuery($stmt));
    }

    public function returnArray($result) {
        $return = array();
        foreach ($result as $current) {
            $return[] = array(
                'id' => $current['id'],
                'descr' => $current['description'],
                'status' => $current['status'],
                'task_id' => $current['task_id'],
                'user_id' => $current['user_id'],
            );
        }
        return $return;
    }

}
