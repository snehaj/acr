<?php
require_once('DBFactory.php');
require_once('MysqlDB.php');
require_once('JsonDB.php');
class DBFactory
{
    /**
     * 
     * @param string $DBType
     * @return DB
     */
    static public function createDB($DBType,$storage){
        switch ($DBType) {
            case 'mysql':
                $obj = new MysqlDB($storage);
                break;
            case 'json':
                $obj = new JsonDB($storage);
                break;
            default:
                $obj = new MysqlDB($storage);
                break;
        }
        return $obj;
    }
}


