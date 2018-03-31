<?php

/**
 * @package Adserver
 * @author Sneha J
 */
require_once(__DIR__ . '/../model/BannerModel.php');

class BannerUtil {

    public static function getAllBanners() {
        $model = new BannerModel();
        try {
            $rows = $model->getAllBanners();
            return $rows;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }
    
    public static function getBannersByDimensions($data) {
        $model = new BannerModel();
        try {
            $rows = $model->getBannersByDimensions($data);
            return $rows;
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }

    public static function addNewBanner($data) {
        $model = new BannerModel();
        try {
            return $model->addNewBanner($data);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function addMultipleBanner($data) {
        $model = new BannerModel();
        try {
            return $model->addMultipleBanner($data);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function updateBanner($data) {

        $model = new BannerModel();
        try {
            return $model->updateBanner($data);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function deleteBanner($id) {
        $model = new BannerModel();
        try {
            return $model->deleteBanner($id);
        } catch (Exception $e) {
            if ($e->getCode() != 0) {
                throw $e;
            } else {
                throw new Exception($e->getMessage(), HttpStatusCodes::INTERNAL_SERVER_ERROR);
            }
        }
    }

}
