<?php

/**
 * User: Jerry
 * Date: 2017-09-26
 * Time: 10:56 AM
 */
class XMLUtil
{



    /**
     * Recursive algorithm for getting needed data from a specific tag in local xml
     * @param $xmlData : simpleXmlObject
     * @param $targetTag : string
     * @return array<string>
     */
    public function extractArticleTitle($xmlData)
    {
        $arr = [];
        $index = 0;
        $pubmedArticleArr = $xmlData->PubmedArticle;
        foreach($pubmedArticleArr as $pubmedArticle){
            $articleTitle = (string)$pubmedArticle->MedlineCitation->Article->ArticleTitle;
            $articleTitle = trim($articleTitle);
            $arr[$articleTitle]=$index++;
        }
        return array_flip($arr);
    }


    public function extractPMID($xmlData){

        $arr = [];
        $idArr = $xmlData->IdList->Id;
        foreach ($idArr as $id){
            $arr[] = (string)$id;
        }
        return $arr;
    }


    public function saveXML($xmlResultData){
        $dom = new DomDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xmlResultData->asXML());
        $dom->save('group3_result.xml');

    }




}

?>