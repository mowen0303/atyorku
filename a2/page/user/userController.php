<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2017-11-06
 * Time: 7:53 PM
 */
require_once $_SERVER["DOCUMENT_ROOT"].'/a2/model/UserModel.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/a2/model/BasicTool.php';
$currentUser = new UserModel();

call_user_func($_GET['action']);

function loginWithJson(){
    global $currentUser;
    try{
        $username = BasicTool::post('username',"please enter your user name");
        $password = BasicTool::post('password',"please enter your password");
        $currentUser->login($username,$password);
        BasicTool::echoJson(1,"success");
    }catch(Exception $e){
        BasicTool::echoJson(0,$e->getMessage());
    }
}

function updateUserCategoryAuthority(){
    global $currentUser;
    try{
        $id = BasicTool::post("id","id can't be empty");
        $val = BasicTool::post("authority");
        if($val == null){
            $val = 0;
        }else{
            $val = array_sum($val);
        }
        $currentUser->updateUserCategoryAuthority($id,$val);
        BasicTool::echoMessage("Success","Authority has been updated");
    }catch(Exception $e){
        BasicTool::echoMessage("Error",$e->getMessage());
    }

}



?>

