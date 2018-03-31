<?php
/**
 * @package Adserver
 * @author Sneha J
 */
require_once('Model.php');

class BannerModel extends Model {
    var $table = 'ad_banner';
    function __construct() {
        parent::__construct();
    }

     /*
    * Function returns all banner
    */
    public function getAllBanners() {
        return $this->connection->select($this->table);
    }

    
    
    public function getBannersByDimensions($data) {
        try {
            
            return $this->connection->find($this->table,$data);
            
        } catch(Exception $e) {
            
           throw $e;
        }
    }
    /*
    * Function adds new banner to database
    */
    public function addNewBanner($data) {
        try {
            
            return $this->connection->insert($this->table,$data);
            
        } catch(Exception $e) {
            
           throw $e;
        }
    }
    
    public function addMultipleBanner($data) {
        try {
            
            return $this->connection->multiinsert($this->table,$data);
            
        } catch(Exception $e) {
            
           throw $e;
        }
    }
    
    
    
    
    /*
    * Function adds new banner to database
    */
    public function updateBanner($data) {
        try {
            
            return $this->connection->update($this->table,$data);
            
        } catch(Exception $e) {
          
           throw $e;
        }
    }

    /*
    * Function deletes banner from the database
    */
    public function deleteBanner($id) {
         try {
            
            return $this->connection->delete($this->table,array('id'=>$id));
            
        } catch(Exception $e) {
          
           throw $e;
        }
      
    }
}