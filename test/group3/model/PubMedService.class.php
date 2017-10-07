<?php

/**
 * User: Jerry
 * Date: 2017-09-26
 * Time: 10:56 AM
 */
class PubMedService
{
    static public $host = "https://eutils.ncbi.nlm.nih.gov";

    /**
     * use article title as a key work to retrieve XML result through by PubMed Esearch API
     * @param $term : string - article title extracted from data xml file
     * @return SimpleXMLElement
     * @throws Exception
     */
    static public function getXMLDataViaArticleTitle($term)
    {
        $term = @urlencode($term);
        $url = self::$host . "/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&field=title";
        $xmlData = @file_get_contents($url);
        if ($xmlData != false) {
            return simplexml_load_string($xmlData);
        } else {
            throw new Exception("Error: " . __FUNCTION__ . " in  " . basename(__FILE__));
        }
    }
}


?>