<?php


require __DIR__ . '/vendor/jacwright/restserver/source/Jacwright/RestServer/RestServer.php';
require __DIR__ . '/src/rest/BannerController.php';
require __DIR__ . '/src/rest/CampaignController.php';


$server = new \Jacwright\RestServer\RestServer('debug');
$server->addClass('BannerController');
$server->addClass('CampaignController');
$server->handle();