<?php

class BasicTool
{


    /**
     * 重新封装$_POST
     * @param string $name
     * @param int $notNullMessage 必填项未填时,throw一个提示
     * @return array|string / NULL
     */
    public static function post($name, $notNullMessage = false, $rangeRestrict = false)
    {
        //如果有值则进行字符串处理
        if (isset($_POST[$name])) {
            $v = $_POST[$name];
            if ($v === "" && $notNullMessage) {
                throw new Exception($notNullMessage);
            }
            if (is_string($v)) {
                //--
                $r = strlen($v);
                if ($rangeRestrict && $r > $rangeRestrict) {
                    throw new Exception("输入字节超出限制 : {$r}/{$rangeRestrict}  ( " . $_POST[$name] . " )");
                }
                //--
                $v = addslashes(trim($v));
            } elseif (is_array($v)) {
                if (!function_exists('__addslashes')) {
                    function __addslashes(&$v, $k)
                    {
                        global $rangeRestrict;
                        global $name;
                        $r = strlen($v);
                        if ($rangeRestrict && $r > $rangeRestrict) {
                            throw new Exception("输入字节超出限制:{$r}/{$rangeRestrict}");
                        }
                        $v = addslashes(trim($v));
                    }
                }
                array_walk_recursive($v, '__addslashes');
            }
            return $v;
        } else {
            if ($notNullMessage != false) {
                throw new Exception($notNullMessage);   //如果没填必填项抛出exception
            } else {
                return null;    //如果无值返回null
            }
        }
    }

    /**
     * 重新封装 $_GET
     * @param string $name
     * @param int $notNullMessage 必填项未填时,throw一个提示
     * @return array|string / NULL
     */
    public static function get($name, $notNullMessage = false, $rangeRestrict = false, $rangeError = false)
    {
        //如果有值则进行字符串处理
        if (isset($_GET[$name])) {
            $v = $_GET[$name];
            if ($v === "" && $notNullMessage) {
                throw new Exception($notNullMessage);
            }
            if (is_string($v)) {
                $r = strlen($v);
                if ($rangeRestrict && $r > $rangeRestrict) {
                    throw new Exception("输入字节超出限制:{$r}/{$rangeRestrict}");
                }
                $v = addslashes(trim($v));
            } elseif (is_array($v)) {
                if (!function_exists('__addslashes')) {
                    function __addslashes(&$v, $k)
                    {
                        global $rangeRestrict;
                        global $name;
                        $r = strlen($v);
                        if ($rangeRestrict && $r > $rangeRestrict) {
                            throw new Exception("输入字节超出限制:{$r}/{$rangeRestrict}");
                        }
                        $v = addslashes(trim($v));
                    }
                }
                array_walk_recursive($v, '__addslashes');
            }
            return $v;
        } else {
            if ($notNullMessage != false) {
                throw new Exception($notNullMessage);   //如果没填必填项抛出exception
            } else {
                return null;    //如果无值返回null
            }
        }
    }


    /**
     * 加载指定的代码片段
     * @param $snippetName  根据地址栏传值,地址栏变量s
     * @param $pageTitle 页面标题
     * @param $defaultSnippet  设置不传值默认显示的代码片段
     */
    public static function loadSnippet($snippetName, $pageTitle, $defaultSnippet)
    {
        if ($snippetName == null) {
            $snippetName = $defaultSnippet;
        }
        include_once "snippet/{$snippetName}.php";
    }

    /**
     * 输出一个提示页
     * @param string $msg 提示文本
     * @param string $url 跳转地址
     * @param string $urlTxt 跳转按钮标题
     */
    public static function echoMessage($msg, $url = null, $urlTxt = "返回")
    {
        if ($url === null) {
            $url = $_SERVER['HTTP_REFERER'];
        }
        include_once $_SERVER['DOCUMENT_ROOT'] . "/admin/templete/_msg.php";
        die();
    }

    /**
     * 输出一个提示页
     * @param string $msg 提示文本
     * @param string $url 跳转地址
     * @param string $urlTxt 跳转按钮标题
     */
    public static function echoWapMessage($msg,$title=false)
    {
        include_once $_SERVER['DOCUMENT_ROOT'] . "/apps/wap/msg.php";
        die();
    }


    /**
     * [jump description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function jumpTo($url, $frame = 'self')
    {
        echo "<script>{$frame}.location='{$url}'</script>";
        exit();
    }


    public static function translateTime($time)
    {

        $second = time() - $time;

        if ($second < 120) {

            return 刚刚;

        } elseif ($second < 3600) {

            return floor($second / 60) . 分钟前;

        } elseif ($second < 86400) {

            return floor($second / 3600) . 小时前;

        } elseif ($second < 172800) {

            return "昨天";

        } elseif ($second < 259200) {

            return "前天";

        } elseif ($second < 86400 * 3) {

            return floor($second / 86400) . 天前;

        } elseif ($second < 31556926) {

            return date('m-d', $time);

        } else {
            return date('Y-m-d', $time);
        }
    }

    /**
     * 输出一个json格式的结果
     * @param $code
     * @param $message
     * @param $result
     */
    public static function echoJson($code, $message, $result = 0, $secondResult = 0, $thirdResult = 0)
    {
        //print_r($result);

        if (is_array($result)) {
            function _encodeArray(&$v, $k)
            {
                if ($v == null) {
                    $v = "";
                }

            }

            array_walk_recursive($result, "_encodeArray");

        }
        echo json_encode(array('code' => $code, 'message' => $message, 'result' => $result, 'secondResult' => $secondResult, 'thirdResult' => $thirdResult));
    }


    /**
     * 检验email格式是否合法
     * @param $email
     * @return bool
     */
    public static function checkFormatOfEmail($email)
    {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            return false;
        }
        return true;
    }


    public static function throwException($message, $code = 0)
    {
        throw new Exception($message, $code);
    }

    public static function mailTo($mailAddress, $mailTitle, $mailBody)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/resource/tools/email/class.phpmailer.php";
        $mail = new PHPMailer(); //建立邮件发送类
        $mail->CharSet = "UTF-8";                             // 设置邮件编码
        $mail->setLanguage('zh_cn');                          // 设置错误中文提示

        //配置邮局GODADDY
        $mail->IsSMTP();                        // 使用SMTP方式发送
        $mail->Host = "smtp.office365.com";          // 您的企业邮局域名
        $mail->SMTPAuth = true;                 // 启用SMTP验证功能
        $mail->Username = "admin@atyorku.ca"; // 邮局用户名(请填写完整的email地址)
        $mail->Password = "Mowen9373!";          // 邮局密码
        //$mail->SMTPSecure = 'SSL';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;                        //发送端口
        $mail->From = "admin@atyorku.ca";     //邮件发送者email地址
        $mail->FromName = "AtYorkU App";

        //配置邮件内容
        //$mail->AddAddress($mailAddress, "AtYorkU User");
        $mail->AddAddress($mailAddress, "");    //收件人email,收件人姓名
        $mail->IsHTML(true);                    //是否使用HTML格式
        $mail->Subject = $mailTitle;            //邮件标题
        $mail->Body = $mailBody;               //邮件内容

        //$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
        return $mail->Send();
    }

    /**
     * 如果第一个值是真就返回第一个值,否则返回第二个
     * @param $firstVal
     * @param $secondVal
     * @return mixed
     */
    public static function getFirstOrSecond($firstVal, $secondVal)
    {
        if ($firstVal == true) {
            return $firstVal;
        } else {
            return $secondVal;
        }
    }

    public static function translateEnrollYear($timeStaple)
    {
        if ($timeStaple == 0) {
            return " ";
        }
        return date('Y', $timeStaple) . "级";

    }

    /**
     * 假设需要删除一个名叫"upload"的同级目录即此目录下的所有文件，你可以通过以下代码完成： delFile($_SERVER["DOCUMENT_ROOT"].'/uploads',true);
     * 假设需要删除一个名叫"upload"目录下的所有文件（但无需删除目录文件夹），你可以通过以下代码完成：    delFile($_SERVER["DOCUMENT_ROOT"].'/uploads');
     * @param $dirName
     * @param bool $delSelf
     * @return bool
     */
    public static function delFile($dirName, $delSelf = false)
    {
        if (file_exists($dirName) && $handle = opendir($dirName)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (file_exists($dirName . '/' . $item) && is_dir($dirName . '/' . $item)) {
                        delFile($dirName . '/' . $item);
                    } else {
                        if (!unlink($dirName . '/' . $item)) {
                            return false;
                        }
                    }
                }
            }
            closedir($handle);
            if ($delSelf) {
                if (!rmdir($dirName)) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }

    //将html时间转成php时间戳
    //2018-01-29T01:01:00 (HTML时间戳)
    static function translateHTMLTimeToPHPStaple($htmlTime){
        //strtotime("2018-08-21 12:00:00");
        if($htmlTime){
            $htmlTime = str_replace("T"," ",$htmlTime);
            return strtotime($htmlTime);
        }else{
            return 0;
        }

    }

    /**
     * 获取今日凌晨与午夜两个节点的时间戳
     * @return array ["startTime"=>timeStamp,"endTime"=>timeStamp]
     */
    static function getTodayTimestamp(){
        $startTime = strtotime(date("Y-m-d")." 00:00:01");
        $endTime = $startTime+3600*24;
        return ["startTime"=>$startTime,"endTime"=>$endTime];
    }

}


?>
