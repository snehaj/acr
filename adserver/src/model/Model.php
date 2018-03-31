<?php
require_once('DBFactory.php');

class Model {
    
    var $connection;

    function __construct() {
        try {
            $json = file_get_contents(__DIR__ . "/../dbserver.json");
            $storage = json_decode($json, true);
            if($storage['DB_STORAGE']=='mysql'){
                $this->connection =  DBFactory::createDB('mysql',$storage);
            } else {
                 $this->connection =  DBFactory::createDB('json',$storage);
            }
            

        } catch(Exception $e) {
            throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }
}



