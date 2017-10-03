<?php
require_once 'XMLUtil.class.php';
require_once 'PubMedService.class.php';
$xmlUtil = new XMLUtil();
$articleTitle = $_GET["articleTitle"];
$xmlData = PubMedService::getXMLDataViaArticleTitle($articleTitle);
$pmidArr = $xmlUtil->extractPMID($xmlData);
echo json_encode($pmidArr);
?>