<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author jnx
 */
class Database {

    public static function getLink() {
        require_once('app/dbconf.php');
        $config = getConfig();
        return mysqli_connect($config['host'], $config['user'], $config['password'], $config['db']);
    }

    public static function execQuery($stmt) {
        $link = self::getLink();
        $result = mysqli_query($link, $stmt);
        mysqli_close($link);
        return $result;
    }
}
