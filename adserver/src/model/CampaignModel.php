<?php
require_once('Model.php');

class CampaignModel extends Model {
    var $table = 'ad_campaign';
    function __construct() {
        parent::__construct();
    }

    /*
    * Function returns all campaign
    */
    public function getAllCampaigns() {
        return $this->connection->select($this->table);
    }

    /*
    * Function adds new campaign to database
    */
    public function addNewCampaign($data) {
        try {
           
            return $this->connection->insert($this->table,$data);
            
        } catch(Exception $e) {
            
           throw $e;
        }
    }
    
    
    /*
    * Function adds new campaign to database
    */
    public function updateCampaign($data) {
        try {
            
            return $this->connection->update($this->table,$data);
            
        } catch(Exception $e) {
          
           throw $e;
        }
    }

    /*
    * Function deletes campaign from the database
    */
    public function deleteCampaign($id) {
         try {
            
            return $this->connection->delete($this->table,array('id'=>$id));
            
        } catch(Exception $e) {
          
           throw $e;
        }
      
    }

}