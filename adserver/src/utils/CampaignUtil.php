<?php
/**
 * @package Adserver
 * @author Sneha J
 */
require_once(__DIR__ . '/../model/CampaignModel.php');

class CampaignUtil {

    public static function getAllCampaigns() {
        $model = new CampaignModel();
        try {
            $rows = $model->getAllCampaigns();
            return $rows;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }

    public static function addNewCampaign($data) {
        $model = new CampaignModel();
        try {
            return $model->addNewCampaign($data);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function updateCampaign($data) {

        $model = new CampaignModel();
        try {
            return $model->updateCampaign($data);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function deleteCampaign($id) {
        $model = new CampaignModel();
        try {
            return $model->deleteCampaign($id);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

}
