<?php

/**
 * @package Adserver
 * @author Sneha J
 */
require_once(__DIR__ . '/../utils/BannerUtil.php');

class Banner {

    public static function getAllBanners() {
        return BannerUtil::getAllBanners();
    }
    
    public static function getBannersByDimensions($data) {
        return BannerUtil::getBannersByDimensions($data);
    }

    public static function addNewBanner($data) {
        return BannerUtil::addNewBanner($data);
    }

    public static function addMultipleBanner($data) {
        return BannerUtil::addMultipleBanner($data);
    }

    public static function updateBanner($data) {

        return BannerUtil::updateBanner($data);
    }

    public static function deleteBanner($id) {
        return BannerUtil::deleteBanner($id);
    }

}
