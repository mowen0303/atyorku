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
//https://idpz.utorauth.utoronto.ca/idp/profile/SAML2/Redirect/SSO;jsessionid=D6372E3CB12E5D08F5802F876E5ABAFF?execution=e1s1



$login_url = 'https://idpz.utorauth.utoronto.ca/idp/profile/SAML2/Redirect/SSO?execution=e1s1';
$post = [
    '_eventId_proceed'=>'',
    '$csrfToken.getParameterName()'=>'$csrfToken.getToken()',
    'j_password'=>'Fan19970220',
    'j_username'=>'chenru20'
];
$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: en-US,en;q=0.5',
    'Cache-Control:no-cache',
    'Connection:keep-alive',
    'Content-Length:124',
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: JSESSIONID=6F0937EBE0AEDFDFE5C7EF264C8C08FA; shib_idp_session_ss=AAdzZWNyZXQxwFhBwVSrTvdMDVjqpXom17x8RNZHCSeCBo9fTy6k7VWdd8%2B0kwu1IcMJvGHdddT%2FuD1Ycdn1d1x9aqkiC9HPBApb4YgpU3To7ZA%2FdoM6JNSPbFUwUrc5VmpUbwltCkxzrg6UjqRcvvizGxarESH03PtSQkIVUbdaoLigeL4UDu%2Fl1WW37Iw7kYyOtwpdFvi%2B0bwUy3cVnuEvV9Gq758oJtJ%2BfuQ6Zu86eYZ3Sl%2BG0Q1MUkrO4xh0206ciY4WfoUDsMnLlrNMeN%2BcEaOpdm3DC%2BkNwVMhTayIJ%2FSZ7HFHOEknd6OgEex82PTjsclVgQi37wYVREcXFTDOYQzdUieCMmDjDdR44BQqyGHlFwbu3DrevQC2DXB%2Bkd8TeOuzsAbfxbbnlRCtptRJr9zo9lUdsruLJEW8VI2hjxOuzbLyvNdfEf%2FOi2OrmobY2VTTiTvRrQZC%2Bwx5iIMSsfrAeetH2FN1Ob9ijVq9XTL%2FyDw%2F8cAIkwE4c%2FZHkDA61xfm9nC8vmz5kd7SdoK5TLpwnoRjzgVTcmgX7tjh9mwC10FR10kOHg7KrlAO0JvTWQ8Fq60qIiQUFidZFT4%3D; shib_idp_session=8caddb7fc92226d5a7a08a82b18ff9927bf38347f69bae87373e855c1cf161d9; _ga=GA1.2.1684170426.1535038050; _gid=GA1.2.480607578.1535038050',
    'Host: idpz.utorauth.utoronto.ca',
    'Pragma:no-cache',
    'Referer: https://idpz.utorauth.utoronto.ca/idp/profile/SAML2/Redirect/SSO?execution=e1s1',
    'Upgrade-Insecure-Requests: 1',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0'
));
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);   //CURL收到的 HTTP Response 中的 Set-Cookie 存放的位置
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);  //CURL发送的 HTTP Request 中的 Cookie 存放的位置
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
$result = curl_exec($ch);
curl_close($ch);
print_r($result);

?>
