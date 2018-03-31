<?php

/**
 * @package Adserver
 * @author Sneha J
 */
use \Jacwright\RestServer\RestException;

require_once 'AdsController.php';
require_once 'HttpStatusCodes.php';
require_once 'Banner.php';
require_once 'Campaign.php';

class BannerController extends AdsController {

    /**
     * Gets sll banners info
     *
     * @url GET /api/banner/all
     */
    public function getBanners() {

        try {

            $result = Banner::getAllBanners();
            return $result;
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }
    
    
    /**
     * Gets banners by dimensions info
     *
     * @url GET /api/banner/$width/$height
     */
    public function getBannersByDimensions() {

        try {
            $result = Banner::getBannersByDimensions($_GET);
            return $result;
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Inserts a new banner into the database
     *
     * @url POST /api/banner
     * @url PUT /api/banner
     */
    public function addNewBanner() {
        $_PUT = $this->getPutData();
        $data = isset($_PUT) ? $_PUT : $_POST;


        if (!isset($data)) {

            throw new RestException(HttpStatusCodes::BAD_REQUEST, "Missing required params");
        }
        try {
            if (isset($_POST) && isset($data['width']) && filter_var($data['width'], FILTER_VALIDATE_INT) && filter_var($data['height'], FILTER_VALIDATE_INT) && isset($data['height']) && isset($data['name']) && isset($data['content'])) {
                return Banner::addNewBanner($data);
            } else if (isset($_PUT) && filter_var($data['id'], FILTER_VALIDATE_INT)) {
                return Banner::updateBanner($data);
            } else {
                throw new RestException(HttpStatusCodes::BAD_REQUEST, "Not valid data.");
            }

            http_response_code(HttpStatusCodes::CREATED);
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Inserts a new banner into the database
     *
     * @url DELETE /api/banner
     */
    public function deleteBanner() {
        $_DELETE = $this->getDeleteData();
        if (isset($_DELETE['id'])) {
            $id = $_DELETE['id'];
        } else {
            throw new RestException(HttpStatusCodes::BAD_REQUEST, "Missing required params");
        }
        try {

            Banner::deleteBanner($id);
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }

    

    /**
     * Inserts a new banner into the database
     *
     * @url POST /api/campaignbanners
     */
    public function addCampaignBanners() {

        $data = $_POST;


        if (!isset($data)) {

            throw new RestException(HttpStatusCodes::BAD_REQUEST, "Missing required params");
        }
        try {

            $bannerDara = json_decode($data, true);
            $id = Campaign::addNewCampaign(array('name' => $bannerDara['name']));
            foreach ($bannerDara['banners'] as $key => $values) {

                $bannerDara['banners'][$key]['campaign_id'] = $id;
            }
            Banner::addMultipleBanner($bannerDara['banners']);

            return array("success" => "Campaign Banner, Updated " . $id);

            http_response_code(HttpStatusCodes::CREATED);
        } catch (Exception $e) {
            throw new RestException($e->getCode(), $e->getMessage());
        }
    }

}
