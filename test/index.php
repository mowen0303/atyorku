<?php
// require_once "../commonClass/config.php";
// $msgModel = new \admin\msg\MsgModel();
//
// //$msgModel->updateRowById('user',3,['device'=>'APA91bHiQG999TZOvWdoGu-E5Qfrpil2EGudgHpXHtkWOdW8Xp…LTH5xVJb6nvcp-ZMnojBVHvRbjSA3um_GdRAlmyZpPMBV_7Kw']);
//
//
// //var_dump($msgModel->pushMsgToUser(1,"good","1","good"));
//
// //$time = BasicTool::getTodayTimestamp();
// //print_r($time);
//
// $user = new \admin\user\UserModel();
//
// $user->getDailyCredit() or BasicTool::throwException($user->errorMsg);
//
// //邮箱验证




/**
* @access_path: http://127.0.0.1/4.php
* @author: vfhky 20140313 19:13
* @description: PHP模拟登录wordpress后台(http://127.0.0.1/wpupdate/wp-admin/)
* @reference: http://vfhky.sinaapp.com/mix/887.html
*/

$post = [
    "dologin"=>"Login",
    "mli"=>"jerry226",
    "password"=>"miss0227",
    "__albform__"=>"eJyzuvX7dgmnvu60jqZDcWe23VPWVilsYKotZNQI5ShILC4uzy9KKWTyZghlzs3JLGQGMthT8nPy0zPzClm8mUIFQGqSMxLz0lPLE/NKUlMKWb0ZSvUAoV4eug==",
    "__albstate__"=>"eJzzFXW9qF7y7W6dC9dhpnfbcycJG+s2MNUWMmqEcsTHFySmp8bHFzKFsubkp2fmFTKHckIEi4GiLLGFrKV6ACFPFs0=",
    "__pybpp__"=>"tYsV1IneTfYnS8LTQtdJjCdIjT81"
];

$cookie = dirname(__file__).'/cookie.txt';
$login_url = 'https://passportyork.yorku.ca/ppylogin/ppylogin';

$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_HEADER, true);

/* curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); */
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


/* CURL收到的 HTTP Response 中的 Set-Cookie 存放的位置 */
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
/* CURL发送的 HTTP Request 中的 Cookie 存放的位置 */
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$result = curl_exec($ch);
curl_close($ch);

/* 输出获取的资源$result,该资源是wordpress后台处理返回的Response Headers信息 */
print_r($result);

/* 清理cookie文件: unlink($cookie_file); */

// //模拟登录
// function login_post($url, $cookie, $post) {
//     $curl = curl_init();
//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
//     curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie);
//     curl_setopt($curl, CURLOPT_POST, 1);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
//     curl_exec($curl);
//     curl_close($curl);
// }
//
// //登录成功后获取数据
// function get_content($url, $cookie) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//     $rs = curl_exec($ch);
//     curl_close($ch);
//     return $rs;
// }
//
//
// $post = [
//     "dologin"=>"Login",
//     "mli"=>"jerry226",
//     "password"=>"miss0227"
// ];
// $url = "https://passportyork.yorku.ca/ppylogin/ppylogin";
// $url2 = "https://my.yorku.ca/group/home/home";
// $cookie = dirname(__FILE__) . '/cookie.txt';
//
// //模拟登录
// login_post($url, $cookie, $post);
// //获取登录页的信息
// $content = get_content($url2, $cookie);
// //删除cookie文件
// //@ unlink($cookie);
//
// var_dump($content);
?>
