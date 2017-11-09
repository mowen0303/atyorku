<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/a2/model/UserModel.php";
$currentUser = new UserModel();
try{
    $currentUser->validateUser();
}catch(Exception $e){
    $currentUser->logout();
}
?>