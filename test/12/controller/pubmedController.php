<?php
require_once '../model/XMLUtil.class.php';
require_once '../model/PubMedService.class.php';
$xmlUtil = new XMLUtil();
call_user_func($_GET['action']);


function getPMIDAndSaveToSession()
{
    global $xmlUtil;
    try {
        $articleTitle = $_POST["articleTitle"];
        $sessionIndex = $_POST["sessionIndex"];
        $xmlData = PubMedService::getXMLDataViaArticleTitle($articleTitle);
        $pmidArr = $xmlUtil->extractPMID($xmlData);
        $pubmedArticle["PMID"] = $pmidArr;
        $pubmedArticle["ArticleTitle"] = $articleTitle;
        $xmlUtil->saveResultToSession($pubmedArticle, $sessionIndex);
        echo json_encode(["result" => "Success"]);
    } catch (Exception $e) {
        echo json_encode(["result" => $e]);
    }
}

function saveXML()
{
    global $xmlUtil;
    try {
        $xmlUtil->saveXML();
        echo json_encode(["result" => "Success"]);
    } catch (Exception $e) {
        echo json_encode(["result" => "Failed:" . $e]);
    }
}

?>