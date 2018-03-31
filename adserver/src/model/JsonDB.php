<?php

/**
 * @package Adserver
 * @author Sneha J
 */
require_once('Repository.php');

class JsonDB implements Repository {

    var $path;

    public function __construct($storage) {
        $this->path = $storage['DATA_FOLDER'];
    }

    /**
     * Function to save to file
     * @param string $table
     * @param array $data
     * @return int
     */
    public function insert($table, $data) {

        if (!file_exists($this->path . '/' . $table)) {
            file_put_contents($this->path . '/' . $table, null, FILE_APPEND);
        }
        $jsonData = $this->read($this->path . '/' . $table);
        $totalCount = count($jsonData) - 1;
        $data['id'] = $jsonData[$totalCount]['id'] + 1;
        $jsonData[] = $data;
        file_put_contents($this->path . '/' . $table, json_encode($jsonData));

        return $data['id'];
    }

    /**
     * Function to delete  from file
     * @param string $table
     * @param array $data
     * @return int
     */
    public function delete($table, $data) {
        $jsonData = $this->read($this->path . '/' . $table);
        $id = 0;
        // get array index to delete
        $arr_index = array();
        foreach ($jsonData as $key => $value) {
            if ($value['id'] == $data['id']) {
                $id = $key;
                unset($jsonData[$key]);
            }
        }
        // rebase array
        $jsonData = array_values($jsonData);

        // encode array to json and save to file
        file_put_contents($this->path . '/' . $table, json_encode($jsonData));
        return $id;
    }

    /**
     * Function to update  from file
     * @param string $table
     * @param array $data
     * @return int
     */
    public function update($table, $data) {

        $jsonData = $this->read($this->path . '/' . $table);
        $id = 0;
        foreach ($jsonData as $key => $value) {

            if ($value['id'] == $data['id']) {
                $id = $key;
                $jsonData[$key] = $data;
            }
        }

        // encode array to json and save to file
        file_put_contents($this->path . '/' . $table, json_encode($jsonData));
        return $id;
    }

    /**
     * Function to select all  from file
     * @param string $table
     * @param array $data
     * @return int
     */
    public function select($table) {
        $data = $this->read($this->path . '/' . $table);
        if (is_array($data)) {
            ksort($data);
            return $data;
        } else {
            return array();
        }
    }

    /**
     * Function to bulkinsert  from file
     * @param string $table
     * @param array $data
     * @return int
     */
    public function multiinsert($table, $data) {
        $jsonData = $this->read($this->path . '/' . $table);
        $totalCount = count($jsonData) - 1;
        $newID = $jsonData[$totalCount]['id'] + 1;
        $dataArray = array();
        foreach ($data as $column => $colvalues) {
            $data[$column]['id'] = $newID;
            $newID = $newID + 1;
        }
        $jsonData[] = $data;
        $appendData = str_replace(array('[', ']'), '', htmlspecialchars(json_encode($jsonData), ENT_NOQUOTES));
        file_put_contents($this->path . '/' . $table, '[' . $appendData . ']');

        return true;
    }

    private function read($path) {
        return json_decode(file_get_contents($path), true);
    }

    /**
     * funtion to select with filter
     * @param string $table
     * @param array $where
     * @return array
     */
    public function find($table, $where, $select = array()) {

        $found = array();
        print_r($where);
        $jsonData = $this->read($this->path . '/' . $table);
        foreach ($jsonData as $aKey => $aVal) {
            $coincidences = 0;
            foreach ($where as $pKey => $pVal) {
                if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {
                    $coincidences++;
                }
            }
            if ($coincidences == count($where)) {
                $found[$aKey] = $aVal;
            }
        }

        return $found;
    }

}
