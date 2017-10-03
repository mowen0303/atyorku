<?php
ini_set('max_execution_time', 30000);

$startTime = time();

//---------------------------------

require_once 'XMLUtil.class.php';
require_once 'PubMedService.class.php';

@unlink("group3_result.xml");

$xmlData = simplexml_load_file('4020a1-datasets.xml');
$xmlResultData = simplexml_load_string("<PubmedArticleSet></PubmedArticleSet>");

$xmlUtil = new XMLUtil();
$articleTitleArr = $xmlUtil->extractArticleTitle($xmlData);

$total = count($articleTitleArr);

for($i=0; $i<3; $i++){

    $xmlData = PubMedService::getXMLDataViaArticleTitle($articleTitleArr[$i]);
    $pmidArr = $xmlUtil->extractPMID($xmlData);

//    $pubmedArticleNode = $xmlResultData->addChild('PubmedArticle');
//    foreach ($pmidArr as $pmid){
//        $pubmedArticleNode->addChild("PMID",$pmid);
//    }
//    $pubmedArticleNode->addChild("ArticleTitle",$articleTitleArr[$i]);
//    echo "[".($i+1)."/".$total."]success: ".$articleTitleArr[$i]."<br>";
}

//$xmlUtil->saveXML($xmlResultData);

//-------------------------------------

$elapsedTime =  time() - $startTime;
echo "<br>Elapsed time: ". $elapsedTime ." s<br>";

?>