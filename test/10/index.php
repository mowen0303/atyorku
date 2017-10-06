<?php
require_once 'XmlTool.class.php';
ini_set('max_execution_time', 30000);
$xmlTool = new XmlTool();
$time =  time();
//get local data from xml

//$localXmlData = simplexml_load_file("data.xml");
//$arr = $xmlTool->extractLocalXml($localXmlData, "Title");
//
//echo "Title extract is done.<br>Extracted data:<br><br>";
//foreach($arr as $v){
//    echo $v."<br>";
//}

//@unlink("group3_result.xml");
$pid = pcntl_fork();
$urlArr = ["American journal of epidemiology","The American journal of physiology","American journal of physiology Cell physiology","American journal of physiology Endocrinology and metabolism"];
$pmidArr = $xmlTool->extractESearchXml($urlArr);
//$articleArr = $xmlTool->extractEFetchXml($pmidArr);

$time2 = time() - $time;
echo  $time2."<br>";
echo count($pmidArr);
//foreach($pmidArr as $i=> $v){
//    echo $i.":".$v."<br>";
//};
//
//$fp = fopen("https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=28938717,28938717&retmode=xml", 'r');
//$data = stream_get_contents($fp);
//$xmlData = simplexml_load_string($data);
//fclose($fp);
//
//$subArr =[];
//foreach($xmlData->children() as $children){
//    $children = $children->children()->children();
//    $subArr[]= (string)$children->PMID;
//    $children = $children->Article->children();
//    $subArr[]= (string)$children->ArticleTitle;
//}

//print_r($subArr);

//echo  date("h:i:sa")."<br>";





?>