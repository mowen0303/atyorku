<?php
header('Content-Type:text/html;Charset=utf-8');
require("class.phpmailer.php"); //下载的文件必须放在该文件所在目录
$mail = new PHPMailer(); //建立邮件发送类
$mail->CharSet = "UTF-8";                             // 设置邮件编码
$mail->setLanguage('zh_cn');                          // 设置错误中文提示

//$address ="nellie.zhang@nellieshair.com";
//$address ="zhangjiyu@foxmail.com";


//$address =$_GET['iAddress'];
//$iContent = $_GET['iContent'];
//$iName = $_GET['iName'];
//$iPhone = $_GET['iPhone'];
//$iEmail = $_GET['iEmail'];
//$iSubject = "[NelliesHair.com]---$iName";


$address ="mowen0303@gmail.com";


$iSubject = "邮件测试标题"; //标题
$iBody = "测试邮件";

$mail->IsSMTP(); // 使用SMTP方式发送
$mail->Host = "smtp.zoho.com"; // 您的企业邮局域名
$mail->SMTPAuth = true; // 启用SMTP验证功能
$mail->Username = "support@atyorku.ca"; // 邮局用户名(请填写完整的email地址)
$mail->Password = "jerry0226"; // 邮局密码
$mail->SMTPSecure = 'tls';
$mail->Port=587;
$mail->From = "support@atyorku.ca"; //邮件发送者email地址
$mail->FromName = "AtYorkU Support";


$mail->AddAddress("$address", "a");//收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")

$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式

$mail->Subject = $iSubject; //邮件标题
$mail->Body =  $iBody; //邮件内容

$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略

if(!$mail->Send())
{
echo "Sorry,The website system have some error.You can submit it again in a few minute.More over you can send Email to us." . $mail->ErrorInfo;
exit;
}
echo "<p>Your content have succeeded in submiting.</p><p>We will contact you in one business day.</p>";
?>