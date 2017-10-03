<?php
ini_set('max_execution_time', 30000);

$startTime = time();

//---------------------------------

require_once 'XmlTool.class.php';

$xmlTool = new XmlTool();
@unlink("group3_result.xml");
$localXmlData = simplexml_load_file("data.xml");
$urlArr = $xmlTool->extractLocalXml($localXmlData, "Title");
$xmlTool->extractServerXmlAndSaveToLocal($urlArr);

//------------------------------------

//$postdata = http_build_query(
//    array(
//        'id' => '28938717,28938716',
//    )
//);
//
//$opts = array('http' =>
//    array(
//        'method'  => 'POST',
//        'header'  => 'Content-type: application/x-www-form-urlencoded',
//        'content' => $postdata
//    )
//);
//
//libxml_set_streams_context(stream_context_create($opts));
//$url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=xml";
//$xml = XMLReader::open($url);
//$arr = [];
//$index = -1;
//while ($xml->read()){
//    if($xml->name == "PubmedArticle" && $xml->nodeType==XMLReader::ELEMENT){
//        $index++;
//    }
//
//    if($xml->name == "PMID" && $xml->nodeType==XMLReader::ELEMENT ){
//        $arr[$index]["PMID"]=$xml->readString();
//        $xml->next();
//    }
//    if($xml->name == "ArticleTitle" && $xml->nodeType==XMLReader::ELEMENT){
//        $arr[$index]["ArticleTitle"]=$xml->readString();
//    }
//}
//print_r($arr);



//-------------------------------------

$elapsedTime =  time() - $startTime;
echo "Elapsed time: ". $elapsedTime ." s<br>";

?>