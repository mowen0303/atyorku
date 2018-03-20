<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$userModel = new \admin\user\UserModel();
$guideModel = new \admin\guide\GuideModel();
$msgModel = new \admin\msg\MsgModel();
$statisticsModel = new \admin\statistics\StatisticsModel();

call_user_func(BasicTool::get('action'));
//---------------------------------------------------------------------------------------------------------------------
function modifyGuideClass()
{
    global $guideModel;
    global $userModel;
    global $title;

    try{
        $userModel->isUserHasAuthority('GOD') or BasicTool::throwException("只有GOD权限可以配置");

        $flag = BasicTool::post('flag');
        $id = BasicTool::post('guide_class_id');
        $arr = array();
        $arr['guide_class_order'] = BasicTool::post('order') ?  BasicTool::post('order') : 0;
        $arr['icon'] = BasicTool::post('icon',false,50);
        $arr['title'] = BasicTool::post('title','标题不能为空',100);
        $arr['description'] = BasicTool::post('description',false,120);
        $arr['visible'] = BasicTool::post('visible');
        if($flag=='add'){
            $userModel->isAdmin && $userModel->isUserHasAuthority('GUIDE_ADD') or BasicTool::throwException("无权限修改");
            if($guideModel->addRow('guide_class',$arr)){
                BasicTool::echoMessage("添加成功","index.php?s=listGuideClass");
            }
            else{
                throw new Exception("没有添加任何数据");
            }
        }
        elseif($flag=='update'){
        $userModel->isAdmin && $userModel->isUserHasAuthority('GUIDE_UPDATE') or BasicTool::throwException("无权限修改");
            if($guideModel->updateRowById('guide_class',$id,$arr)){
                BasicTool::echoMessage("修改成功","index.php?s=listGuideClass");
            }
            else{
                throw new Exception("没有修改任何数据");
            }
        }

    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }

}

//---------------------------------------------------------------------------------------------------------------------

function deleteGuideClass()
{
    global $userModel;
    global $guideModel;

    try {

        $userModel->isUserHasAuthority('GOD') or BasicTool::throwException("只有GOD权限可以配置");

        $id = BasicTool::post('id');

        $guideModel->logicalDeleteByFieldIn('guide_class', 'id', $id) or BasicTool::throwException($guideModel->errorMsg);

        BasicTool::echoMessage("成功");


    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

function updateGuide(){
    global $guideModel;
    global $userModel;
    try {
        $userModel->isUserHasAuthority('ADMIN') or BasicTool::throwException('无权操作');

        $guideID = BasicTool::post('guide_id') or BasicTool::throwException("缺少Guide ID");
        $guideClassID = BasicTool::post('guide_class_id', '所属指南组不能为空');
        $title = BasicTool::post('title', '标题不能为空',255);
        $content = $_POST['content'];
        $contentLength = strlen($content);
        $contentLength < 16777215 or BasicTool::throwException("内容超出字符限制{$contentLength}/65500");
        $introduction = BasicTool::post('introduction',false,65535);
        $cover = BasicTool::post('cover',false,60);
        $order = BasicTool::post('guide_order',false,4);
        $oldClassId = BasicTool::post('guide_class_old_id');
        $userID = BasicTool::post('userID');

        $guideModel->updateGuide($guideID, $guideClassID, $title, $content, $introduction, $userID, $cover, $order) or BasicTool::throwException($guideModel->errorMsg);
        if( $oldClassId != null && $oldClassId != $guideClassID){
            $guideModel->updateAmountOfArticleByClassId($guideClassID);
            $guideModel->updateAmountOfArticleByClassId($oldClassId);
        }

        BasicTool::echoMessage("修改成功", "index.php?s=listGuide&guide_class_id=" . $guideClassID);

    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(),-1);
    }
}


function deleteGuide()
{
    global $guideModel;
    global $userModel;

    try {
        $userModel->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权删除");
        $ids = BasicTool::post('id');
        $classId = BasicTool::post('classId');
        $guideModel->deleteGuideByIDs($ids) or BasicTool::throwException($guideModel->errorMsg);
        $guideModel->updateAmountOfArticleByClassId($classId);
        BasicTool::echoMessage("删除成功", "index.php?s=listGuide&guide_class_id=" . $classId);
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}
//=======

/*
*JSON - 获取指南分类的一个分类列表
http://www.atyorku.ca/admin/guide/guideController.php?action=getGuideClassListVisibleWithJson
*/
function getGuideClassListVisibleWithJson(){


    global $guideModel;

    $arr2 = $guideModel->getListOfGuideClassVisible();

    $arr1 = [];
    $arr1['id'] = "0";
    $arr1['title'] = "最新";
    $arr1['is_del'] = "0";
    $arr1['visible'] = "0";
    $arr1['icon'] = "/admin/resource/img/icon/h68.png";
    $arr1['guide_class_order'] = "1";
    $arr1['description'] = "全部";
    $amount = 0;
    foreach($arr2 as $row){
        foreach($row as $k => $v){
            if($k == 'amount'){
                $amount += $v;
            }
        }
    }
    $arr1['amount'] = "{$amount}";
    array_unshift($arr2,$arr1);
    BasicTool::echoJson(1,"获取成功",$arr2);

}
/**
 * JSON -  获取某一分类下的文章列表
 * @param guide_class_id 分类id
 * @param page
 * http://www.atyorku.ca/admin/guide/guideController.php?action=getGuideListWithJson&guide_class_id=4&page=1
 */
function getGuideListWithJson(){

    global $guideModel;
    $pageSize = BasicTool::get("pageSize")?:20;
    $arr = $guideModel->getListOfGuideByGuideClassId(BasicTool::get("guide_class_id"),$pageSize);
    if($arr){
        setcookie("guideRequest", 1, time() + 3600,'/');
        BasicTool::echoJson(1,"获取成功",$arr);
    }else{
        BasicTool::echoJson(0,"没有更多数据",$arr);
    }

}

/**
 * http://www.atyorku.ca/admin/guide/guideController.php?action=uploadImgWithJson
 * 上传图片,成功返回图片路径
 * @return bool|path
 */
function uploadImgWithJson(){

    global $guideModel;
    try {
        session_start();
        $oldImg = BasicTool::post('oldImg');
        $uploadDir =  $guideModel->uploadImg("imgFile",$_SESSION["ueditor_upload_location"]) or BasicTool::throwException($guideModel->errorMsg);

        if($oldImg && file_exists($_SERVER['DOCUMENT_ROOT'].$oldImg)){
            unlink($_SERVER['DOCUMENT_ROOT'].$oldImg);
        }

        BasicTool::echoJson(1, "上传成功", $uploadDir);

    } catch (Exception $e) {

        BasicTool::echoJson(0, $e->getMessage());

    }
}

function test(){
    global $guideModel;
    echo $guideModel->getClassIdByGuideId(2);
}

function pushToAll(){
    global $userModel;
    global $msgModel;
    try {
        $userModel->isUserHasAuthority('ADMIN') or BasicTool::throwException("无权限");
        $silent = BasicTool::get('silent');
        $silent = $silent == "silent" ? true : false;
        $title = BasicTool::get('title',"标题不能为空");
        $id =  BasicTool::get('guide_id',"ID不能为空");
        $msgModel->pushMsgToAllUsers("guide",$id,$title,$silent);

    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function renewTime(){
    global $guideModel;
    global $userModel;
    try {
        $userModel->isAdmin && $userModel->isUserHasAuthority('GUIDE_ADD') or BasicTool::throwException("无权限修改");
        $id =  BasicTool::get('guide_id',"ID不能为空");
        $arr = ["time"=>time()];
        $guideModel->updateRowById("guide",$id,$arr) or BasicTool::throwException($guideModel->errorMsg);
        BasicTool::echoMessage("成功");
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage().$guideModel->errorMsg);
    }
}



?>
