<?php
require_once $_SERVER['DOCUMENT_ROOT']."/commonClass/simple_html_dom.php";
$cookie = dirname(__file__).'/cookie.txt';
function getData($url){
    global $cookie;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //CURL发送的 HTTP Request 中的 Cookie 存放的位置
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    return curl_exec($ch);
}


//登陆
$login_url = 'https://passportyork.yorku.ca/ppylogin/ppylogin';
$post = [
    "dologin"=>"Login",
    "mli"=>"jerry226",
    "password"=>"miss0227",
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
if($result == false){
    echo curl_error($ch);
    echo "<br>";
    echo curl_errno($ch);
}
curl_close($ch);
print_r($result);
var_dump($result);
echo "123123";
///**
// * 获取课程表连接
// */
//$login_url = 'https://w2prod.sis.yorku.ca/Apps/WebObjects/cdm.woa/wa/DirectAction/cds';
//$ch = curl_init($login_url);
//curl_setopt($ch, CURLOPT_HEADER, false);
//curl_setopt($ch, CURLOPT_HEADER, 0);
//curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //CURL发送的 HTTP Request 中的 Cookie 存放的位置
//curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//$result = curl_exec($ch);
//curl_close($ch);
////print_r($result);
//$html = new simple_html_dom();
//$html->load($result);
//$timeTableLinkArr = [];
//foreach($html->find('table table table a') as $a){
//    $timeTableLinkArr[$a->innertext] = 'https://w2prod.sis.yorku.ca'.$a->href;
//}
////print_r($timeTableLinkArr);
///**
// * 获取课程表
// */
//next($timeTableLinkArr);
//print_r(getData(next($timeTableLinkArr)));
//////获取成绩单
////$login_url = 'https://wrem.sis.yorku.ca/Apps/WebObjects/ydml.woa/wa/DirectAction/document?name=CourseListv1';
////$ch = curl_init($login_url);
////curl_setopt($ch, CURLOPT_HEADER, false);
////curl_setopt($ch, CURLOPT_HEADER, 0);
////curl_setopt($ch,CURLOPT_RETURNTRANSFER,0);
////curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //CURL发送的 HTTP Request 中的 Cookie 存放的位置
////curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
////$result = curl_exec($ch);
////curl_close($ch);
////print_r($result);
?>