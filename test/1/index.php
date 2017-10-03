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

//------------------------------------

//$localXmlData = simplexml_load_file("group3_result.xml");
//$dom = new DOMDocument('1.0');
//$dom->preserveWhiteSpace = false;
//$dom->formatOutput = true;
//$dom->loadXML($localXmlData->asXML());
//$dom->save('group3_result2.xml');

//------------------------------------
$elapsedTime =  time() - $startTime;
echo "Elapsed time: ". $elapsedTime ." s<br>";

?>