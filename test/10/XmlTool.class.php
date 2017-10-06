<?php

/**
 * User: Jerry
 * Date: 2017-09-26
 * Time: 10:56 AM
 */
class XmlTool
{

    private $retmax = 10;


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
     * Through by a url, get the all of the data you need from a service,
     * and save the data to a local xml file named 'group3_result.xml'
     * @param $url : string
     * @param $itemContainerTag : string
     * @param $targetTagArr : Array<string>
     */
    public function extractESearchXml($terms)
    {
        $arr = [];
        foreach ($terms as $term) {
            $term = rawurlencode($term);
            $amountOfItem = $this->getAmountOfItems($term);
            $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&datetype=edat&retstart=0&retmax={$amountOfItem}";
            //$url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&datetype=edat&retstart=0&retmax=10";
            $fp = fopen($url, 'r');
            $data = stream_get_contents($fp);
            fclose($fp);
            $xmlData = simplexml_load_string($data);

            //extract id
            $child = $xmlData->children();
            foreach ($child[3]->children() as $child) {
                $arr[] = (string)$child;
            }
        }
        return $arr;
    }


    private function getAmountOfItems($term)
    {
        $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&datetype=edat&retstart=0&retmax=1";
        $fp = fopen($url, 'r');
        $data = stream_get_contents($fp);
        $xml = simplexml_load_string($data);
        fclose($fp);
        return (string)$xml->children();

    }

    //
    public function extractEFetchXml($pmidArr)
    {
        $groupMax = 100;
        $currentGroupIndex = 1;
        $groupString = "";
        $arr = [];
        $arrIndex = 0;

        foreach ($pmidArr as $index => $pmid) {
            $groupString .= $pmid . ",";
            if ($index > $currentGroupIndex * $groupMax) {
                $currentGroupIndex++;
                $groupString = rtrim($groupString, ",");
                $url = "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id={$groupString}&retmode=xml";
                $groupString = "";
                //echo $url."<br>";
                $fp = fopen($url, 'r');
                $data = stream_get_contents($fp);
                fclose($fp);
                $xmlData = simplexml_load_string($data);

                foreach($xmlData->children() as $children){
                    $children = $children->children()->children();
                    $arr[$arrIndex]['PMID']= (string)$children->PMID;
                    $children = $children->Article->children();
                    $arr[$arrIndex]['ArticleTitle']= (string)$children->ArticleTitle;
                    $arrIndex++;
                }
            }
        }
        return $arr;

    }


    private function extractFetchXml($xmlData, $targetTagArr)
    {
        static $index = -1;
        static $arr = [];

        foreach ($xmlData->children() as $child) {
            if ($child->getName() == "PubmedArticle") {
                $index++;
            }
            if (in_array($child->getName(), $targetTagArr)) {
                $arr[$index][$child->getName()] = (string)$child;
            } else {
                $this->extractFetchXml($child, $targetTagArr);
            }
        }
        return $arr;
    }


    /**Use array to create a xml object
     * @param $arr : array[<int>][<string>]
     * @param $itemContainerTag : string
     */
    private function saveXmlToFile($arr)
    {
        if ($xmlData = @simplexml_load_file("group3_result.xml")) {

        } else {
            $xmlData = simplexml_load_string("<PubmedArticleSet></PubmedArticleSet>");
        }

        foreach ($arr as $subArr) {
            $item = $xmlData->addChild("PubmedArticle");
            foreach ($subArr as $k => $v) {
                $itemSon = $item->addChild($k, $v);
            }
        }
        //reformat xml
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlData->asXML());
        //echo $dom->saveXML();
        $dom->save('group3_result.xml');
    }


}

?>