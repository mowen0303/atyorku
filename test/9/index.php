<?php
ini_set('max_execution_time', 30000);

$startTime = time();

//---------------------------------
require_once 'XmlTool.class.php';

//$xmlTool = new XmlTool();
//@unlink("group3_result.xml");
//$localXmlData = simplexml_load_file("data.xml");
//$urlArr = $xmlTool->extractLocalXml($localXmlData, "Title");
//$xmlTool->extractServerXmlAndSaveToLocal($urlArr);
//
//$localXmlData = simplexml_load_file("group3_result.xml");
//var_dump($localXmlData->children()->children());

$xmlData = simplexml_load_string("<PubmedArticleSet></PubmedArticleSet>");
$item = $xmlData->addChild("ID","PubmedA&rticle");

//------------------------------------
$elapsedTime =  time() - $startTime;
echo "Elapsed time: ". $elapsedTime ." s<br>";

?>