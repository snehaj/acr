<?php
require_once(__DIR__ . '/../utils/CampaignUtil.php');

class Campaign {

    public static function getAllCampaigns() {
        return CampaignUtil::getAllCampaigns();
    }

    public static function addNewCampaign($data) {
        return CampaignUtil::addNewCampaign($data);
    }
    
    public static function updateCampaign($data) {
        
        return CampaignUtil::updateCampaign($data);
    }

    public static function deleteCampaign($id) {
        return CampaignUtil::deleteCampaign($id);
    }
}