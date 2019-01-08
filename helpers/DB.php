<?php
namespace app\helpers;
class DB {
    public static function getDBConnection() {
        return \Yii::$app->db;
    }

    public static function queryAll($sql) {
        return static::getDBConnection()->createCommand($sql)->queryAll();
    }

    public static function transaction(callable $callback, $isolationLevel = null) {
        return static::getDBConnection()->transaction($callback, $isolationLevel);
    }
}