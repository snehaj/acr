<?php 

/*
* Base class for Controllers
*/
class AdsController {
    public function getDeleteData() {
        $_DELETE = array();
        parse_str(file_get_contents("php://input"), $_DELETE);
        return $_DELETE;
    }

    public function getPutData() {
        $_PUT = array();
        parse_str(file_get_contents("php://input"), $_PUT);
        return $_PUT;
    }
}