<?php
/**
 * @package Adserver
 * @author Sneha J
 */
use \Jacwright\RestServer\RestException;

require_once 'AdsController.php';
require_once 'HttpStatusCodes.php';
require_once 'Campaign.php';

class CampaignController extends AdsController {
    /**
     * Gets all campaign info
     *
     * @url GET /api/campaign/all
     */
    public function getCampaigns()
    {
        
        try {
            
            $result = Campaign::getAllCampaigns();
            return $result;
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Inserts a new campaign into the database
     *
     * @url POST /api/campaign
     * @url PUT /api/campaign
     */
    public function addNewCampaign() {
       $_PUT = $this->getPutData();
       $data = isset($_PUT)?$_PUT:$_POST;
       
        if (!isset($data)) {
          
            throw new RestException(HttpStatusCodes::BAD_REQUEST, "Missing required params");
        }
        try {
            
             if(isset($_PUT['id'])
                     && filter_var($data['id'], FILTER_VALIDATE_INT))
              {
                 $id = Campaign::updateCampaign($_PUT);
                 
                 return array("success" => "Campaign Updated " . $id);
                     
             }else if(isset($_POST['name'])){
                  $id = Campaign::addNewCampaign($data);
                  return array("success" => "Campaign Added " . $id);
             }else{
                 throw new RestException(HttpStatusCodes::BAD_REQUEST, "Not valid data.");
             }
                    
             
             
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }
    
    

    /**
     * delete campaign into the database
     *
     * @url DELETE /api/campaign
     */
    public function deleteCampaign() {
        $_DELETE = $this->getDeleteData();
        if (isset($_DELETE['id'])) {
            $id = $_DELETE['id'];
            
        } else {
            throw new RestException(HttpStatusCodes::BAD_REQUEST, "Missing required params");
        }
        try{
            
            Campaign::deleteCampaign($id);
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }
}