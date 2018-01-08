<?php
namespace admin\userCode; //-- 注意 --//
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

/**
 * 评论Model使用指南：
 * 1. 添加一个字段 count_comments 到需要使用评论功能的数据库表中
 * 2. 使用表名作为 $section_name 的值
 * 3. 使用表中的ID 作为 $section_id 的值
 */

class UserCodeModel extends Model
{
    /**
     * @param $userName
     * @param $controllerName - 验证连接的controller文件
     * @param $actionName - 验证连接的controller文件中的方法名
     * @return string - 验证码连接
     * @throws Exception
     */
    function generateUserCode($userName,$actionName){
        $arr['email'] = $userName;
        $arr['code'] = md5(rand(999,999999));;
        $arr['is_valid'] = "1";
        $this->addRow('user_code',$arr) or BasicTool::throwException("邮件验证码写入失败");
        $id = $this->idOfInsert;
        $activeUrl = '<a href="http://www.atyorku.ca/admin/user/userController.php?action='.$actionName.'&email='.$arr['email'].'&code='.$arr['code'].'&id='.$id.'" target="_blank">http://www.atyorku.ca/admin/user/userController.php?action='.$actionName.'&email='.$arr['email'].'&code='.$arr['code'].'&id='.$id.'</a>';
        return $activeUrl;
    }

    /**
     * 验证邮件码
     * @param $id
     * @param $email
     * @param $code
     * @throws Exception
     */
    function validateUserCode($id,$email,$code){
        $row = $this->getRowById('user_code', $id);
        ($email == $row['email'] && $code == $row['code'] && $row['is_valid'] == 1) or BasicTool::throwException("连接已过期,请重新请求服务");
        $arr['is_valid'] = 0;
        $this->updateRowById('user_code', $id, $arr);
    }
}



?>