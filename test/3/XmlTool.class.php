<?php

/**
 * User: Jerry
 * Date: 2017-09-26
 * Time: 10:56 AM
 */
class XmlTool
{

    private $retmax = 10000;
    //private $retmax = 5;

    /**
     * Recursive algorithm for getting needed data from a specific tag in local xml
     * @param $xmlData : simpleXmlObject
     * @param $targetTag : string
     * @return array<string>
     */
    public function extractLocalXml($xmlData, $targetTag)
    {
        static $arr = [];

        foreach ($xmlData->children() as $child) {
            if ($child->getName() == $targetTag) {
                $val = trim((string)$child);
                $val = str_replace('.', '', $val);
                if (!in_array($val, $arr)) {
                    $arr[] = $val;
                }
            } else {
                $this->extractLocalXml($child, $targetTag);
            }
        }
        return array_unique($arr);
    }


    /**
     * Retrieve PMID and ArticleTitle base on 'key words', then write result to a group3_result.xml file.
     * @param $terms
     */
    public function extractServerXmlAndSaveToLocal($terms)
    {
        $xmlData = simplexml_load_string("<PubmedArticleSet></PubmedArticleSet>");

        foreach ($terms as $term) {
            $term = rawurlencode($term);
            $totalPage = $this->getTotalPage($term);
            //$totalPage = 2;

            for ($currentPage = 0; $currentPage < $totalPage; $currentPage++) {
                $retstart = $currentPage * $this->retmax;
                echo "[Processing..." . ($currentPage + 1) . "/" . $totalPage . "...]" . rawurldecode($term) . "<br>";
                $pmidArr = $this->getPMIDsFromEsearchAPI($term, $retstart, $this->retmax);
                $articalArr = $this->getDataFromEfetchAPI($pmidArr);

                foreach ($articalArr as $subArr) {
                    $item = $xmlData->addChild("PubmedArticle");
                    foreach ($subArr as $k => $v) {
                        $v=htmlspecialchars($v);
                        $item->addChild($k, $v);
                    }
                }
                //$this->saveXmlToFile($articalArr);
                echo "Success<br><br>";
            }
        }
        $dom = new DomDocument('1.0','utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlData->asXML());
        //echo $dom->saveXML();
        $dom->save('group3_result.xml');
    }

    /**
     * Retrieve a amount number of PMID from server ESearch API and calculate out amount of group.
     * @param $term : string - searched key words
     * @return int - amount number of group
     */
    private function getTotalPage($term)
    {
        $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&datetype=edat&retstart=0&retmax=1&field=title";
        $fp = fopen($url, 'r') or die("URL can't open: ".$url."<br>");
        $data = stream_get_contents($fp);
        $xml = simplexml_load_string($data) or die("xml can't load from: ".$url);
        $totalItemAmount = (string)$xml->children();
        fclose($fp);
        //echo $totalItemAmount."<br>";
        return ceil($totalItemAmount / $this->retmax);
    }


    /**
     * @param $term : string - key word
     * @param $retstart : number - index of starting position
     * @param $retmax : number - retrieve how many data
     * @return array[] - PMIDs
     */
    private function getPMIDsFromEsearchAPI($term, $retstart, $retmax)
    {
        $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&datetype=edat&retstart={$retstart}&retmax={$retmax}&field=title";
        //echo $url."<br><br>";
        $fp = fopen($url, 'r');
        $data = stream_get_contents($fp);
        fclose($fp);
        $xmlData = simplexml_load_string($data);

        $arr = [];
        $child = $xmlData->children();
        foreach ($child->IdList->children() as $id) {
            $arr[] = (string)$id;
        }
        return $arr;
    }
    /**
     * Use PMIDs to retrieve each PMID's article title, and save into a two-dimensional array.
     * @param $idArr : array - an array contains PMIDs
     * @return array[int][key=>value]
     */
    private function getDataFromEfetchAPI($idArr)
    {
        $idStr = "";
        foreach ($idArr as $id) {
            $idStr .= $id . ",";
        }
        $idStr = rtrim($idStr, ",");
        //
        $postdata = http_build_query(
            array(
                'id' => $idStr,
            )
        );

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        libxml_set_streams_context(stream_context_create($opts));
        $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&retmode=xml";
        //echo $url."<br><br><br><br><br>";
        $xml = XMLReader::open($url);
        $arr = [];
        $index = 0;
        while ($xml->read()){
            if($xml->name == "PMID" && $xml->nodeType==XMLReader::ELEMENT){
                $arr[$index]['PMID']=$xml->readString();
            }else if($xml->name == "ArticleTitle" && $xml->nodeType==XMLReader::ELEMENT){
                $arr[$index]['ArticleTitle']=$xml->readString();
            }
            if($xml->name == "PubmedArticle" && $xml->nodeType==XMLReader::ELEMENT){
                $index++;
            }
        }
        $xml->close();
       // echo $index."<br><br><br><br>";
        return $arr;
    }

}

?>