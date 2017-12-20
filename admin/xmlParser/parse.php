<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$parserModel = new admin\xmlParser\XMLParser();
$sqltool = SqlTool::getSqlTool();
$xml = simplexml_load_file("course.xml");
$json = json_encode($xml);
$array = json_decode($json,TRUE);
//var_dump(array_key_exists("courseCode",$array["courseSubject"][0]["course"]))


//foreach ($array["courseSubject"] as $subject){
  //  echo $subject["courseSubjectCode"]." : ".$subject["courseSubjectTitle"]."<br>";
   // if (array_key_exists("courseCode",$subject["course"]))
     //   echo $subject["course"]["courseCode"]." : ". $subject["course"]["courseTitle"]."/".$subject["course"]["credit"]."<br>";
    //else {
      //  foreach ($subject["course"] as $course) {
        //    echo $course["courseCode"] . " : " . $course["courseTitle"] . "/" . $course["credit"] . "<br>";
        //}
    //}
//}
//插入学科
foreach ($array["courseSubject"] as $subject){
    $arr = [];
    $arr["title"] = $subject["courseSubjectCode"];
    $arr["full_title"] = $subject["courseSubjectTitle"];
    $arr["description"] = "";
    $arr["parent_id"] = 0;
    $arr["credits"] = 0;
    $parserModel->insertCourseCode("coursecode",$arr);
    }
$sql = "SELECT id FROM coursecode";
$course_subjects = $sqltool->getListBySql($sql);

foreach($course_subjects as $subject){
    $id = $subject["id"] + 0;
    $index = $id - 1;
    $childNodes = [];
    if (array_key_exists("courseCode",$array["courseSubject"][$index]["course"])){
        $course = $array["courseSubject"][$index]["course"];
        echo $course["courseCode"]." : ". $course["courseTitle"]."/".intval($course["credit"])."<br>";
        $arr=[];
        $arr["parent_id"] = $id;
        $title = explode(" ",$course["courseCode"])[1];
        $arr["title"] = $title;
        $arr["full_title"] = $course["courseTitle"];
        $arr["credits"] = intval($course["credit"]);
        $arr["description"] = $course["description"];
        $parserModel->insertCourseCode("coursecode",$arr);
    }
    else{
        foreach ($array["courseSubject"][$index]["course"] as $course){
            echo $course["courseCode"] . " : " . $course["courseTitle"] . "/" . intval($course["credit"]) . "<br>";
            if (!in_array($course["courseCode"],$childNodes)){
            $arr=[];
            $arr["parent_id"] = $id;
            $title = explode(" ",$course["courseCode"])[1];
            $arr["title"] = $title;
            $arr["full_title"] = $course["courseTitle"];
            $arr["credits"] = intval($course["credit"]);
            $arr["description"] = $course["description"];
            $parserModel->insertCourseCode("coursecode",$arr);
            array_push($childNodes,$course["courseCode"]);
            }
            else{

            }
        }
    }
}



?>