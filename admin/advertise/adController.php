<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$adModel = new \admin\advertise\adModel();
$userModel = new \admin\user\UserModel();
$currentUser = new \admin\user\UserModel();



call_user_func(BasicTool::get('action'));


/**
 * JSON -  获取论坛的一个分类列表
 * @param 无
 * http://localhost/admin/forum/forumController.php?action=getForumClassListWithJson
 */


//---------------------------------------------------------------------------------------------------------------------

function addAd()
{

    global $currentUser;
    global $adModel;
    $flag = BasicTool::post('flag');
    $id = BasicTool::get('aid');
    $temp = $adModel->getListByAdId($id);

        try {
            //if(!$currentUser->isblocked()){throw new Exception('您已经被禁言');};

            $arr = [];
            $arr['class_id'] = BasicTool::post('category');
            $arr['title'] = BasicTool::post('title', '内容不能为空');
            $arr['content'] = BasicTool::post('content');
            $arr['img'] = BasicTool::post('img');
            if($id==null){
                $arr['user_id'] = $currentUser->userId;
                $arr['datetopost'] = time();}
            $arr['url'] = BasicTool::post('url');
            $arr['startdate'] = strtotime(BasicTool::post('startdate'));
            $arr['expiredate'] = strtotime(BasicTool::post('datetotime'));
            if($flag=='add') {
            if ($adModel->addRow('advertisement', $arr)) {
                BasicTool::echoMessage("广告添加成功", "/admin/advertise/index.php");
            }
        }
            elseif($flag=='update') {
                $info = "$temp[img]";
                    if(unlink($info)){
                        echo "文件{$info}删除完毕...!";
                    }else{
                        if(chmod($info,0777)){
                            unlink($info);
                            echo "文件{$info}权限修改后删除完毕...";
                        }else{
                            echo "文件{$info}无法通过web方式删除，可能是ftp权限对此文件有所设置...";
                        }
                    }
                if($adModel->updateRowById('advertisement',$id,$arr)){
                    BasicTool::echoMessage("修改成功","index.php");
                }else{
                    throw new Exception("没有修改任何数据");
                }
            }
    }

    catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


//---------------------------------------------------------------------------------------------------------------------

function deleteAd()
{
    global $adModel;
    global $currentUser;
    $id = BasicTool::post('id');
    $temp = $adModel->getListByAdId($id);

    try {


            if (!($currentUser->isUserHasAuthority('ADMIN'))) {
                throw new Exception('无权限删除评论信息');
            }


        if ($adModel->logicalDeleteByFieldIn('advertisement', 'id', $id)) {
            $info = "$temp[img]";
            if(unlink($info)){
                echo "文件{$info}删除完毕...!";
            }else{
                if(chmod($info,0777)){
                    unlink($info);
                    echo "文件{$info}权限修改后删除完毕...";
                }else{
                    echo "文件{$info}无法删除，可能是tm的bug,……%%…………$%$";
                }
            }
            throw new Exception('操作成功');
        } else {
            throw new Exception('删除失败,数据未受影响');
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}

//---------------------------------------------------------------------------------------------------------------------
function realDeleteAd()
{
    global $adModel;
    global $currentUser;

    try {

        $id = BasicTool::post('id');

        if (!($currentUser->isUserHasAuthority('ADMIN'))) {
            throw new Exception('无权限删除评论信息');
        }


        if ($adModel->realDeleteByFieldIn('advertisement', 'id', $id)) {
            throw new Exception('操作成功');
        } else {
            throw new Exception('删除失败,数据未受影响');
        }
    } catch (Exception $e) {
        BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
    }
}


?>
