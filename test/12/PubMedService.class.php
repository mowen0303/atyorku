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
    static public function getXMLDataViaArticleTitle($term) {
        $term = urlencode($term);
        $url = self::$host."/entrez/eutils/esearch.fcgi?db=pubmed&term={$term}&field=title";


        $urlinfo = parse_url($url);

        $host = $urlinfo['host'];
        $path = $urlinfo['path'];
        $query = isset($param)? http_build_query($param) : '';

        $port = 80;
        $errno = 0;
        $errstr = '';
        $timeout = 10;

        $fp = fsockopen($host, $port, $errno, $errstr, $timeout);

        $out = "GET ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n";
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;

        fwrite($fp, $out);

        while (!feof($fp)) {
            echo fgets($fp, 128);
        }

//        $xmlData = file_get_contents($url);
//        return simplexml_load_string($xmlData);


        fclose($fp);

    }
}


?>
