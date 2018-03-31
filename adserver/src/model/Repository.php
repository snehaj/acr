<?php
/**
 * @package Adserver
 * @author Sneha J
 */
interface Repository {

    public function insert($table, $data);

    public function delete($table, $data);

    public function multiinsert($table, $data);

    public function update($table, $data);

    public function find($table, $where, $select = array());

    public function select($table);
}
