<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$parser = new \admin\xmlParser\XMLParser();
$json = file_get_contents('professor.json');

$json_data = json_decode($json,true);
$profs_check = [];
$profs = [];
foreach ($json_data["response"]["docs"] as $prof_profile)
{
    $prof_full_name = $prof_profile["teacherlastname_t"]." ".$prof_profile["teacherfirstname_t"];
    if (!in_array("$prof_full_name",$profs_check)){
        array_push($profs_check,$prof_full_name);
        $prof = [];
        $prof["view_count"] = 0;
        $prof["firstname"] =$prof_profile["teacherfirstname_t"]?$prof_profile["teacherfirstname_t"]:"";
        $prof["lastname"] = $prof_profile["teacherlastname_t"]?$prof_profile["teacherlastname_t"]:"";
        array_push($profs,$prof);
    }
    else{

    }

}
foreach($profs as $prof){
        $parser->insert("professors",$prof);
}




