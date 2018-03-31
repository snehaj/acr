<?php
/**
 * @package Adserver
 * @author Sneha J
 */
require_once('Repository.php');

class MysqlDB implements Repository {

    var $path;

    public function __construct($storage) {
        $dsn = "mysql:host=" . $storage['DB_HOST'] . ";dbname=" . $storage['DB_NAME'];
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->path = new PDO($dsn, $storage['DB_USER'], $storage['DB_PASS'], $opt);
    }
    /**
     * function to save data
     * @param string $table
     * @param array $data
     * @return int id
     */
    public function insert($table, $data) {
        $result = array();
        $columns = array();
        $values = array();
        foreach ($data as $column => $value) {
            $columns[] = $column;
            $values[] = $value;
        }
        $columns_imploded = implode(",", $columns);
        $values_imploded = "'" . implode("','", $values) . "'";
        $query = "INSERT INTO  $table ($columns_imploded) VALUES($values_imploded)";
        $query = $this->path->prepare($query);
        $result = $query->execute();

        return $this->path->lastInsertId();
    }
    /**
     * funtion to insert multiple items
     * @param string $table
     * @param array $data
     * @return int
     */
    public function multiinsert($table, $data) {

        $result = array();
        $columns = array();
        $values = array();
        $queryString = '';

        foreach ($data as $column => $colvalues) {
            $columns[] = array_keys($colvalues);
            $queryString .= '(';
            $queryString .= "'" . implode("','", array_values($colvalues)) . "'";
            ;
            $queryString .= '),';
        }

        $columns_imploded = implode(",", $columns[0]);
        $values_imploded = substr($queryString, 0, strlen($queryString) - 1);
        $query = "INSERT INTO  $table ($columns_imploded) VALUES $values_imploded;";
        $query = $this->path->prepare($query);
        $result = $query->execute();

        return $result;
    }
    
    
    /**
     * funtion to delete
     * @param string $table
     * @param array $data
     * @return int
     */
    public function delete($table, $data) {
        if (isset($data['id'])) {
            $result = "";
            $where = 'id=' . $data['id'];
            $query = "DELETE  FROM $table
                WHERE  $where ";
            $query = $this->path->prepare($query);
            $result = $query->execute();

            return $result;
        }
    }
    /**
     * funtion to update
     * @param string $table
     * @param array $data
     * @return int
     */

    public function update($table, $data) {
        $result = array();
        $new_values = array();
        if (isset($data['id'])) {
            $where = 'id=' . $data['id'];
            unset($data['id']);
            foreach ($data as $column => $value) {
                $new_values[] = $column . " = " . "'" . $value . "'";
            }
            $new_values_sql = join(",", $new_values);
            $query = "UPDATE  $table
                    SET $new_values_sql
                    WHERE  $where ";
            $query = $this->path->prepare($query);
            $result = $query->execute();

            return $result;
        }
    }
    
    /**
     * funtion to select
     * @param string $table
     * @return array
     */
    public function select($table) {
        $stmt = $this->path->prepare("SELECT * FROM $table");
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $result = null;
        } else {
            $result = $stmt->fetchAll();
        }

        return $result;
    }
    /**
     * funtion to select with filter
     * @param string $table
     * @param array $where
     * @return array
     */
    public function find($table, $where, $select = array()) {
        $args = array();

        foreach ($where as $key => $value) {
            $args[':' . $key] = $value;
        }

        $i = 0;
        foreach ($where as $key => $value) {
            if (!$i)
                $where_sql = "WHERE :{$key} = {$key}";
            else
                $where_sql .= " AND {$key} = {$key}";
            $i++;
        }

        $sql_select = ' * ';
        if (count($select))
            $sql_select = implode(", ", $select);
        $query = "SELECT {$sql_select} FROM {$table} {$where_sql} ";
        return $this->db->query($query, $args);
    }

}
