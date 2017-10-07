<?php
require_once '../model/XMLUtil.class.php';
require_once '../model/PubMedService.class.php';
$xmlUtil = new XMLUtil();
call_user_func($_GET['action']);

/**
 * load Database XML and extract article title
 */
function getArticleTitle(){
    global $xmlUtil;
    try{
        session_start();
        $_SESSION = [];
        $xmlUtil = new XMLUtil();
        $xmlData = simplexml_load_file('../asset/4020a1-datasets.xml');
        echo json_encode(["result"=>$xmlUtil->extractArticleTitle($xmlData)]);
    }catch (Exception $e){
        echo $e;
    }
}

/**
 * It is invoked by front-end (View layer) for Async purpose. Get the article title from front-end and invoking model methods to get pmid and save pmid and article title into session.
 */
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

/**
 * invoking saveXML API to retrieve session's date and save into a xml file.
 */
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