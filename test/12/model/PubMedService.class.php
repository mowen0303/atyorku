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
     *
     * @param $term
     * @return string || ""
     */
    static public function getXMLDataViaArticleTitle($term)
    {
        $term = @urlencode($term);
        $url = self::$host . "/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&field=title";
        $xmlData = @file_get_contents($url);
        if ($xmlData != false) {
            return simplexml_load_string($xmlData);
        } else {
            self::$errorMessage = "error: getXMLDataViaArticleTitle() in PubMedService.class.php";
            return false;
        }
    }
}


?>