<?php

/**
 * User: Jerry
 * Date: 2017-09-26
 * Time: 10:56 AM
 */
class XMLUtil
{
    /**
     * Extract article title from local xml file and remove the duplicate titles then saving the titles into an array.
     * @param $xmlData : simpleXMLObject
     * @return array : array[string]
     * @throws Exception
     */
    public function extractArticleTitle($xmlData)
    {
        $arr = [];
        $index = 0;
        $pubmedArticleArr = $xmlData->PubmedArticle;
        foreach ($pubmedArticleArr as $pubmedArticle) {
            $articleTitle = (string)$pubmedArticle->MedlineCitation->Article->ArticleTitle;
            $articleTitle = trim($articleTitle);
            $arr[$articleTitle] = $index++;
        }
        if (count($arr) > 0) {
            return array_values(array_flip($arr));
        } else {
            throw new Exception("Error: " . __FUNCTION__ . " in  " . basename(__FILE__));
        }
    }

    /**
     * Extract PMIDs from Esearch API.
     * @param $xmlData
     * @return array
     * @throws Exception
     */
    public function extractPMID($xmlData)
    {

        $arr = [];
        $idArr = $xmlData->IdList->Id;
        foreach ($idArr as $id) {
            $arr[] = (string)$id;
        }
        if (count($arr) > 0) {
            return $arr;
        } else {
            throw new Exception("Error: " . __FUNCTION__ . " in  " . basename(__FILE__));
        }

    }

    public function saveResultToSession($pubmedArticle, $sessionIndex)
    {
        session_start();
        $_SESSION["a" . $sessionIndex] = $pubmedArticle;
    }


    public function saveXML()
    {
        session_start();
        if (count(@$_SESSION) > 0) {
            $xmlResultData = simplexml_load_string("<PubmedArticleSet></PubmedArticleSet>");
            foreach (@$_SESSION as $pubmedArticle) {
                $pubmedArticleNode = $xmlResultData->addChild('PubmedArticle');
                foreach ($pubmedArticle['PMID'] as $pmid) {
                    $pubmedArticleNode->addChild("PMID", $pmid);
                }
                $pubmedArticleNode->addChild("ArticleTitle", $pubmedArticle['ArticleTitle']);
            }
            @$_SESSION = [];
            $dom = new DomDocument('1.0', 'utf-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xmlResultData->asXML());
            $dom->save('../group3_result.xml');
            return "group3_result.xml file is generated successfully!";
        } else {
            throw new Exception("Session is empty");
        }

    }


}

?>