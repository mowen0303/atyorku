<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonClass/simple_html_dom.php";
$timetableModel = new \admin\timetable\TimetableModel();
$currentUser = new \admin\user\UserModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$cookie = dirname(__file__).'/cookie.txt';
call_user_func(BasicTool::get('action'));

function getTimetableTermsWithJson(){
    $json = file_get_contents('./terms.json');
    $res = json_decode($json,true);
    BasicTool::echoJson(1,"",$res);
}

function updateTimetable($echoType="normal"){
    global $timetableModel,$currentUser;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录");
        $username = BasicTool::post("username")?:'okcxl3';
        $password = BasicTool::post("password")?:'0745778';
        $term = BasicTool::post("term")?:'FALL/WINTER 2017-2018 UNDERGRADUATE STUDENTS';
        $term_year = $timetableModel->extractTermYear($term);
        $term_semester = $timetableModel->extractTermSemester($term);
        $courses = getTimetableFromYorkWithHtml($username,$password,$term,$term_year);
        $result = $timetableModel->updateTimetable($courses,$currentUser->userId,$term_year,$term_semester) or BasicTool::throwException($timetableModel->errorMsg);
        if ($echoType == "normal") {
            BasicTool::echoMessage("更新课程表成功");
        } else {
            BasicTool::echoJson(1, "更新课程表成功",$result);
        }

    }catch (Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

function updateTimetableWithJson(){
    updateTimetable('json');
}

function getTimetableFromYorkWithHtml($username,$password,$term,$term_year){
    global $cookie,$timetableModel;
    //登陆
    $login_url = 'https://passportyork.yorku.ca/ppylogin/ppylogin';
    $post = [
        "dologin"=>"Login",
        "mli"=>$username,
        "password"=>$password,
        "__albform__"=>"eJyzuvX7dgmnvu60jqZDcWe23VPWVilsYKotZNQI5ShILC4uzy9KKWTyZghlzs3JLGQGMthT8nPy0zPzClm8mUIFQGqSMxLz0lPLE/NKUlMKWb0ZSvUAoV4eug==",
        "__albstate__"=>"eJzzFXW9qF7y7W6dC9dhpnfbcycJG+s2MNUWMmqEcsTHFySmp8bHFzKFsubkp2fmFTKHckIEi4GiLLGFrKV6ACFPFs0=",
        "__pybpp__"=>"tYsV1IneTfYnS8LTQtdJjCdIjT81"
    ];
    $ch = curl_init($login_url);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Encoding: gzip, deflate, br',
        'Accept-Language: en-US,en;q=0.5',
        'Cache-Control:no-cache',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded',
        'Cookie: pybpp=tYsV1IneTfYnS8LTQtdJjCdIjT81; mayatarget=~aHR0cHM6Ly9teS55b3JrdS5jYS9jL3BvcnRhbC9leHBpcmVfc2Vzc2lvbg==~Student%20Portal%20; _ga=GA1.2.313460713.1527110627; _gid=GA1.2.288515123.1527110627; cuvon=1527110629179; cusid=1527110629176; cuvid=c90dedeff6784c6dbcc601a2fb7f0df4; __insp_wid=1748088910; __insp_slim=1527110629325; __insp_nv=true; __insp_targlpu=aHR0cDovL2N1cnJlbnRzdHVkZW50cy55b3JrdS5jYS8%3D; __insp_targlpt=SG9tZSB8IEN1cnJlbnQgU3R1ZGVudHMgfCBZb3JrIFVuaXZlcnNpdHk%3D; __insp_norec_sess=true',
        'Host: passportyork.yorku.ca',
        'Pragma:no-cache',
        'Origin: https://passportyork.yorku.ca',
        'Referer: https://passportyork.yorku.ca/ppylogin/ppylogin',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36'
    ));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);   //CURL收到的 HTTP Response 中的 Set-Cookie 存放的位置
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);  //CURL发送的 HTTP Request 中的 Cookie 存放的位置
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $result = curl_exec($ch);
    curl_close($ch);
    if (strpos($result,"Logged in as") === false){
        BasicTool::throwException("登录失败：用户名或密码错误");
    }

    /**
     * 获取课程表连接
     */
    $login_url = 'https://w2prod.sis.yorku.ca/Apps/WebObjects/cdm.woa/wa/DirectAction/cds';
    $ch = curl_init($login_url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //CURL发送的 HTTP Request 中的 Cookie 存放的位置
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $html = new simple_html_dom();
    $html->load($result);
    $timeTableLinkArr = [];
    foreach($html->find('table table table a') as $a){
        $timeTableLinkArr[$a->innertext] = 'https://w2prod.sis.yorku.ca'.$a->href;
    }
    /**
     * 获取课程表
     */
    $result = getHtml($timeTableLinkArr[$term]);
    if (strpos($result,"You do not appear to be enrolled in any courses for this academic session") !== false){
        BasicTool::throwException("获取课程表失败:Not enrolled");
    }
    //print_r(getHtml($timeTableLinkArr[$term]));
    $html = new simple_html_dom();
    $html->load($result);
    $courses = array();
    $conflicted_courses = array();
    foreach($html->find('table[class=timetable] tbody') as $index =>$table){
        if ($index ==0)
            $parentContainer = $table->parent();
        foreach ($table->find('tr') as $i => $row){
            if ($i == 0)
                continue;
            foreach($row->find('td') as $j => $col){
                if ($j == 0)
                    continue;
                //---------------------------------------------------------------------
                if ($col->bgcolor && $col->bgcolor == '#FFF0F0'){
                    //-----------------------------------------------------------抓coursecode，section,学期,教学楼
                    $temp = $col->find('font b a')[0]->innertext;
                    $temp = preg_replace("/\s{2,}/", " ", $temp);
                    $temp = trim($temp);
                    $temp = explode(' ',$temp);
                    $course_code = $temp[1].' '.$temp[2];
                    //echo $course_code;
                    $temp = $col->find('span')[0]->plaintext;
                    $temp = preg_replace("/\s{2,}/", " ", $temp);
                    $temp = preg_replace("/[\[\]]/","",$temp);
                    $temp = trim($temp);
                    $temp = explode(' ',$temp);
                    //var_dump($temp);
                    $section = $temp[1];
                    $term_semester = $timetableModel->parseTermSemester($temp[3]);
                    $type = $temp[4];
                    $location = $temp[count($temp)-2]." ".$temp[count($temp)-1];
                    //-----------------------------------------------------------------------------------判断时间
                    $day = $j;
                    $start_time = 8+($i-1)*0.5;
                    $end_time = $start_time + $col->rowspan * 0.5;
                    //-----------------------------------------------------------------------------------------
                    $key = $course_code.$term_semester.$section;
                    if (!array_key_exists($key,$courses)){
                        $courses[$key] = array("course_code"=>$course_code,"section"=>$section,"term_semester"=>$term_semester,"term_year"=>$term_year);
                    }
                    if(!array_key_exists('schedule',$courses[$key])) {
                        $courses[$key]["schedule"] = array();
                    }
                    $courses[$key]["schedule"][] = array("day"=>$day,"start_time"=>$start_time,"end_time"=>$end_time,"type"=>$type,"location"=>$location);
                }
                //---------------------------------------------------------------------
            }
        }
    }
    //--------------------------------------------------------------------------------------extract and merge conflicted courses
    foreach ($parentContainer->find('p') as $p){
        if (strpos($p->plaintext,"Schedule Conflicts") !== false){
            $ul = $p->nextSibling()->nextSibling();
            foreach($ul->children() as $li){
                $day = $timetableModel->parseDay($li->find('b')[0]->plaintext);
                preg_match_all("/[0-9]+:[0-9]+/",$li->find('b')[0]->plaintext,$matches);
                $temp = explode(":",$matches[0][0]);
                $start_time = (int)$temp[0];
                if (strpos($temp[1],"30") !== false)
                    $start_time += 0.5;
                $temp = explode(":",$matches[0][1]);
                $end_time = (int)$temp[0];
                if (strpos($temp[1],"30") !== false)
                    $end_time += 0.5;
                foreach ($li->find("li") as $_li){
                    $temp = explode("|",$_li->plaintext);
                    $_temp = preg_replace("/\s{2,}/", " ", $temp[0]);
                    $_temp = trim($_temp);
                    $_temp = explode(" ",$_temp);
                    $course_code = $_temp[1]." ".$_temp[2];
                    $_temp = preg_replace("/\s{2,}/", " ", $temp[1]);
                    $_temp = trim($_temp);
                    $_temp = explode(" ",$_temp);
                    $section = $_temp[1];
                    $term_semester = $timetableModel->parseTermSemester($_temp[3]);
                    $_temp = preg_replace("/\s{2,}/", " ", $temp[2]);
                    $location = trim($_temp);
                    $_temp = preg_replace("/\s{2,}/", " ", $temp[3]);
                    $type = trim($_temp);
                    $type = explode(" ",$type)[0];
                    $key = $key = $course_code.$term_semester.$section;
                    if (!array_key_exists($key,$courses)){
                        $courses[$key] = array("course_code"=>$course_code,"section"=>$section,"term_semester"=>$term_semester,"term_year"=>$term_year);
                        $courses[$key]["schedule"] = array(array("day"=>$day,"start_time"=>$start_time,"end_time"=>$end_time,"type"=>$type,"location"=>$location));
                    }else{
                        $isMerged = false;
                        foreach ($courses[$key]["schedule"] as $index => $schedule){
                            if ($schedule["day"] == $day && $schedule["type"] == $type && $schedule["location"] == $location){
                                if ($schedule["end_time"] == $start_time){
                                    $courses[$key]["schedule"][$index]["end_time"] = $end_time;
                                    $isMerged = true;
                                    break;
                                }else if ($schedule["start_time"] == $end_time){
                                    $courses[$key]["schedule"][$index]["start_time"] = $start_time;
                                    $isMerged = true;
                                    break;
                                }
                            }
                        }
                        if (!$isMerged){
                            $courses[$key]["schedule"][] = array("day"=>$day,"start_time"=>$start_time,"end_time"=>$end_time,"type"=>$type,"location"=>$location);
                        }
                    }
                }
            }
        }
    }
    //------------------------------------------------------------------------------------------------------------------
    foreach ($courses as $index => $course){
        if (array_key_exists("schedule",$course)){
            $courses[$index]["schedule"] = json_encode($courses[$index]["schedule"]);
        }
        if (array_key_exists("course_code",$course)){
            $temp = explode(" ",$course["course_code"]);
            $res = $timetableModel->getCourseCodeByString($temp[0],$temp[1]);
            if (!$res){
                unset($courses[$index]);
            }
            else{
                $courses[$index]["course_code_id"] = $res["course_child_id"];
            }
        }
    }
    //echo (json_encode($courses));
    //echo ("<br/>");
    return $courses;
}

function getHtml($url){
    global $cookie;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //CURL发送的 HTTP Request 中的 Cookie 存放的位置
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

//function parseTermSemester($term_semester){
//    if ($term_semester == "F")
//        return "Fall";
//    else if ($term_semester == "W")
//        return "Winter";
//    else if ($term_semester == "Y")
//        return "Year";
//    else if ($term_semester == "SU1")
//        return "Summer1";
//    else if ($term_semester == "SU2")
//        return "Summer2";
//    else
//        return "Summer";
//}
//
//function parseDay($str){
//    if (strpos($str,"Monda") !== false){
//        return 1;
//    }
//    else if (strpos($str,"uesda") !== false){
//        return 2;
//    }
//    else if (strpos($str,"ednesda") !== false){
//        return 3;
//    }
//    else if (strpos($str,"hursda") !== false){
//        return 4;
//    }else{
//        return 5;
//    }
//}

