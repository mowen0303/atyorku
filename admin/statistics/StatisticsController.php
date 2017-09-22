<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$action = BasicTool::get('action');
$forumModel = new \admin\forum\ForumModel();
$currentUser = new \admin\user\UserModel();



call_user_func(BasicTool::get('action'));


/**
 * JSON -  获取论坛的一个分类列表
 * @param 无
 * http://localhost/admin/forum/forumController.php?action=getForumClassListWithJson
 */
function getForumClassListWithJson(){

    global $forumModel;

    //执行逻辑处理
    $result = $forumModel->getListOfForumClass(false);

    //
    $countTodayOfAll = 0;

    foreach($result as $k1 => $v1) {

        foreach($v1 as $k2 => $v2){

            //计算发帖总量
            if($k2 == "count_today"){
                $countTodayOfAll += $v2;
            }
        }
    }

    $all = [];
    $all['id'] = '0';
    $all['title'] = '最新';
    $all['count_today'] = "{$countTodayOfAll}";
    $all['description'] = '';
    $all['type'] = 'normal';
    $all['icon'] = '/admin/resource/img/icon/f1.png';
    $all['sort'] = '1';
    $all['display'] = '1';

    array_unshift($result,$all);

    if($result){
        //输出json结果
        BasicTool::echoJson(1,"成功",$result);
    }else{
        BasicTool::echoJson(0,"没有分类列表");
    }


}




?>
