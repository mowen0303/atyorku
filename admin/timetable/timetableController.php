<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonClass/simple_html_dom.php";
$timetableModel = new \admin\timetable\TimetableModel();
$currentUser = new \admin\user\UserModel();
$courseCodeModel = new \admin\courseCode\CourseCodeModel();
$cookieFolder = dirname(__file__).'/cookieFolder';
call_user_func(BasicTool::get('action'));

function getTimetableCoursesWithJson(){
    global $timetableModel,$currentUser;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录");
        $term_year = BasicTool::get("term_year","Missing term year");
        $term_semester = BasicTool::get("term_semester","Missing term semester");
        $courses = $timetableModel->getTimetableCourses($currentUser->userId,$term_year,$term_semester) or BasicTool::throwException("空");
        foreach ($courses as $index => $course){
            if ($course["schedule"])
                $courses[$index]["schedule"] = json_decode($courses[$index]["schedule"]);
        }
        BasicTool::echoJson(1,"获取课程表成功",$courses);
    }catch (Exception $e){
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function getTermsWithJson(){
    global $timetableModel,$currentUser;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录");
        $terms = $timetableModel->getTerms($currentUser->userId) or BasicTool::throwException("空");
        BasicTool::echoJson(1,"成功",$terms);
    }catch (Exception $e){
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**POST传值
 * term_year, array of term years
 */
function deleteTimetable(){
    global $timetableModel,$currentUser;
    try{
        $currentUser->isUserHasAuthority("GOD") or BasicTool::throwException("删除失败:权限不足");
        $user_id = BasicTool::post("user_id","删除失败:Missing user id");
        $term_year = BasicTool::post("term_year","删除失败:Missing term year");
        $concat = "";
        foreach ($term_year as $year){
            $concat .= "'{$year}',";
        }
        $concat = substr($concat, 0, -1);
        $timetableModel->deleteTimetableByTermYear($user_id,$concat) or BasicTool::throwException($timetableModel->errorMsg);
        BasicTool::echoMessage("删除成功");

    }catch (Exception $e){
        BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
    }
}

function updateTimetable($echoType="normal"){
    global $cookieFolder,$timetableModel,$currentUser;
    try{
        $currentUser->userId or BasicTool::throwException("请先登录",3);
        $username = BasicTool::post("username")?:'okcxl3';
        $password = BasicTool::post("password")?:'0745778';
        $cookie = $cookieFolder . "/cookie{$currentUser->userId}.txt";
        $courses = getTimetableFromYorkWithHtml($username,$password,$cookie);
        $timetableModel->updateTimetable($courses,$currentUser->userId) or BasicTool::throwException($timetableModel->errorMsg);
        $result = $timetableModel->getUserTermsAndCourses($currentUser->userId) or BasicTool::throwException("空");
        if ($echoType == "normal") {
            BasicTool::echoMessage("更新课程表成功");
        } else {
            BasicTool::echoJson(1, "更新课程表成功",$result);
        }

    }catch (Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER["HTTP_REFERER"]);
        } else {
            BasicTool::echoJson($e->getCode(), $e->getMessage());
        }
    }
}

function updateTimetableWithJson(){
    updateTimetable('json');
}

function getTimetableFromYorkWithHtml($username,$password,$cookie){
    global $timetableModel;
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $result = curl_exec($ch);
    curl_close($ch);
    //echo $result;
    if (strpos($result,"Logged in as") === false){
        BasicTool::throwException("登录失败: 约克账号或密码有误,或约克大学网站接口异常.",2);
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
    $all_courses = [];
    foreach ($timeTableLinkArr as $term => $link){
        $result = getHtml($link,$cookie);
        $term_year = $timetableModel->parseTermYear($term);
        if (strpos($result,"You do not appear to be enrolled in any courses for this academic session") !== false)
            continue;
        //print_r(getHtml($timeTableLinkArr[$term]));
        $html = new simple_html_dom();
        $html->load($result);
        $courses = array();
        foreach($html->find('table[class=timetable] tbody') as $index =>$table){
            $occupied = [];
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
                        $start_time = 8+($i-1)*0.5;
                        $end_time = $start_time + $col->rowspan * 0.5;
                        $day = $timetableModel->awesomeFunction($occupied,$start_time,$col->rowspan,$j);
                        //-----------------------------------------------------------------------------------------
                        $key = $course_code.$term_semester.$section.$term_year;
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
                        $key = $course_code.$term_semester.$section.$term_year;
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
                if ($course["term_semester"] == "Year" || $course["term_semester"] == "Summer"){
                    $unsetIndex = [];
                    foreach ($courses[$index]["schedule"] as $i => $schedule){
                        if (in_array($i,$unsetIndex))
                            continue;
                        foreach ($courses[$index]["schedule"] as $j => $_schedule){
                            if ($i == $j)
                                continue;
                            if (($schedule["start_time"] == $_schedule["start_time"]) && ($schedule["end_time"] == $_schedule["end_time"]) && ($schedule["day"] == $_schedule["day"]) && ($schedule["location"] == $_schedule["location"]) && ($schedule["type"] == $_schedule["type"])){
                                unset($courses[$index]["schedule"][$j]);
                                $unsetIndex[] = $j;
                            }
                        }
                    }
                }
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
        //return $courses;
        $all_courses = array_merge($all_courses,$courses);
    }
    return $all_courses;
}

function getHtml($url,$cookie){
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



