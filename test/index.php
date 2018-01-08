<?php
require_once "../commonClass/config.php";
$currentUser = new \admin\user\UserModel();
//邮箱验证
try{
    $code = md5(rand(999,999999));
    $arr2['email'] = "mowen0303@gmail.com";
    $arr2['code'] = $code;
    $arr2['is_valid'] = "1";
    $currentUser->addRow('user_code',$arr2) or BasicTool::throwException("账号注册成功,但激活码配置出错,不能正常激活,请联系管理员");
    $id = $currentUser->idOfInsert;
    $mailBody = '<p>亲爱的用户:</p><p>您AtYorkU的账户已经注册成功,请点击下面链接进行激活:</p><p><a href="http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email='.$arr2['email'].'&code='.$code.'&id='.$id.'" target="_blank">http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email='.$arr2['email'].'&code='.$code.'&id='.$id.'</a></p>';
    if(BasicTool::mailTo($arr2['email'],"AtYorkU 账号激活邮件",$mailBody)){
        $msg = "注册成功，为了保证账号正常使用，请尽快到邮箱激活账号";
    } else {
        $msg = "注册成功，可以登录了! (当前邮件服务器压力过大，激活邮件发送失败，请稍登录账号后重新发送)";
    }
    BasicTool::echoJson(1, $msg, $userInfo);
}catch(Exception $e){
    echo "出错";
}

?>