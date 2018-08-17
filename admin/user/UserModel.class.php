<?php
namespace admin\user;   //-- 注意 --//
use admin\transaction\TransactionModel;
use \Credit as Credit;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/key.php";

class UserModel extends Model {
    //秘钥
    private static $key = KEY;

    //是否开启注册邮箱验证
    public $enableEmailVerify = false;

    //user->|id|user_class_id|name|img|pwd|alias|gender|blocktime|blockreason|registertime|is_del|
    public $userId = null;
    public $userClassId = null;
    public $userHeadImg = null;
    public $userName = null;
    public $registerTime = null;
    public $aliasName = null;
    public $gender = null;
    public $isAdmin = null;
    public $authority = null;
    public $authorityTitle = null;
    public $blockTime = 0;
    public $blockReason = null;
    public $major = null;
    public $enrollYear = null;
    public $description = null;
    public $credit = null;
    public $activist = null;
    public $wechat = null;
    public $deviceToken = 0;
    public $deviceType = null;
    public $institutionId = null;


    public $row = null;

    public function __construct($userId = false) {
        parent::__construct();
        $this->setUserInfo($userId);

        if (@$_COOKIE['cc_id'] && !$this->isLogin()) {
            $this->logout();
            die("用户信息被非法篡改,终止继续访问");
        }
    }

    /**
     * 根据userId设置用户信息,如果没有userId则
     * @param int $userId 设置指定某个用户的
     *
     * id|user_class_id|name|img|pwd|alias|gender|blocktime
     * blockreason|registertime|is_del|title|is_admin|authority
     *
     */
    private function setUserInfo($userId = false) {

        if ($userId === false) {
            $this->userId = @$_COOKIE['cc_id'];
            $this->userClassId = @$_COOKIE['cc_uc'];
            $this->userHeadImg = @$_COOKIE['cc_im'];
            $this->userName = @$_COOKIE['cc_na'];
            $this->registerTime = @$_COOKIE['cc_rt'];
            $this->aliasName = @$_COOKIE['cc_al'];
            $this->gender = @$_COOKIE['cc_ge'];
            $this->isAdmin = @$_COOKIE['cc_ia'];
            $this->authority = @$_COOKIE['cc_au'];
            $this->authorityTitle = @$_COOKIE['cc_title'];
            $this->blockTime = @$_COOKIE['cc_bl'];
            $this->blockReason = @$_COOKIE['cc_br'];
            $this->institutionId = @$_COOKIE['cc_ii'];
        } else if ($userId >= 1) {
            $sql = "SELECT u.*,u_c.title,is_admin,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE u.id in ({$userId})";
            $arr = $this->sqltool->getRowBySql($sql);
            $this->userId = $arr['id'];
            $this->userClassId = $arr['user_class_id'];
            $this->userHeadImg = $arr['img'];
            $this->userName = $arr['name'];
            $this->registerTime = $arr['registertime'];
            $this->aliasName = $arr['alias'];
            $this->gender = $arr['gender'];
            $this->isAdmin = $arr['is_admin'];
            $this->authority = $arr['authority'];
            $this->authorityTitle = $arr['title'];
            $this->blockTime = $arr['blocktime'];
            $this->blockReason = $arr['blockreason'];
            $this->major = $arr['major'];
            $this->enrollYear = $arr['enroll_year'];;
            $this->description = $arr['description'];;
            $this->credit = $arr['credit'];;
            $this->activist = $arr['activist'];;
            $this->wechat = $arr['wechat'];
            $this->deviceToken = $arr['device'];
            $this->deviceType = $arr['device_type'];
            $this->institutionId = $arr['institution_id'];
        }
    }

    /**
     * 根据一个userId查询用户信息
     * @param $id
     * @return 一维关联数组
     *
     * id|user_class_id|name|img|pwd|alias|gender|blocktime
     * blockreason|registertime|is_del|title|is_admin|authority
     */
    public function getProfileOfUserById($id, $onlyShowBasic = false) {
        $condition = $onlyShowBasic == true ? "" : ",u.activist,u.credit,u.name,u_c.is_admin,u_c.authority";
        $sql = "SELECT u.id,u.degree,u.checkin_count,u.checkin_last_time,u.device,u.wechat,u.user_class_id,u.institution_id,u.registertime,u.major,u.enroll_year,u.description,u.img,u.alias,u.gender,u_c.title,u.blocktime,u.blockreason,i.title AS institution_title {$condition} FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id INNER JOIN institution i ON u.institution_id = i.id WHERE u.id in ({$id}) AND u.is_del = 0";
        $row = $this->sqltool->getRowBySql($sql);
        foreach ($row as $k => $v) {
            if ($k == "enroll_year") {
                $row['enrollYearTranslate'] = BasicTool::translateEnrollYear($v);
            }
            if ($k == "registertime") {
                $row[$k] = date('Y-m-d', $row[$k]);
            }
            if ($k == "blocktime") {

                if ($v - time() > 0) {
                    $row['blockState'] = "1";
                    $row['blockToTime'] = date('Y-m-d H:i:s', $v);
                } else {
                    $row['blockState'] = "0";
                }
                $row['blockToTime'] = date('Y-m-d H:i:s', $v);
                $row[$k] = date('Y-m-d', $row[$k]);
            }
        }
        return $row;
    }


    /*
     * user_class        | id | user_class_id | name | pwd | alias  | gender | blocktime  | blockreason |
     *
     */
    public function getListOfUser($isAdmin = false, $userClass = false,$orderBy = false, $pageSize = 40) {
        // user * user_class          | id | user_class_id | name | img | pwd |alias | gender | blocktime | blockreason |title | is_admin | authority |

        $condition = " true ";
        $orderCondition = "";

        if($isAdmin !== false){
            $condition .= "AND is_admin in ({$isAdmin}) ";
        }

        if ($userClass !== false) {
            $condition .= "AND u_c.id = $userClass ";
        }
        if ($orderBy) {
            $orderCondition .= "u.{$orderBy} DESC,";
        }
        $table = 'user';
        $sql = "SELECT u.*,u_c.title,is_admin,authority,i.title AS institution_title FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id INNER JOIN institution i ON u.institution_id = i.id WHERE  {$condition} AND is_del =0 ORDER BY {$orderCondition} u.id DESC";
        $countSql = "SELECT COUNT(*) FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE {$condition} AND is_del =0 ORDER BY {$orderCondition} u.id DESC";
        return parent::getListWithPage($table, $sql, $countSql, $pageSize);
    }

    /**
     * 获得用户的分类列表
     * @param int $pageCurrent
     * @param int $pageSize
     * @return 二维数组
     */
    public function getListOfUserClass($pageSize = 50) {
        $table = 'user_class';
        $sql = "SELECT * FROM {$table}";
        $countSql = null;
        return parent::getListWithPage($table, $sql, $countSql, $pageSize);   //-- 注意 --//
    }


    /**
     * 输出用户分类的option列表
     */
    public function echoUserClassOption() {
        $arr = $this->getListOfUserClass();

        //die();
        foreach ($arr as $row) {
            if ($this->isUserHasAuthority('GOD')) {
                echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
            } else if ($row['is_admin'] == 0) {
                echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
            }
        }
    }


    /**
     * 判断当前用户是否有权管理另一个用户
     * @param $targetUserId
     * @return bool
     */
    public function isAuthorityToManageUserByTargetUserId($targetUserId) {
        //安全验证 s---
        if ($this->isUserHasAuthority('GOD')) {
            //上帝,可以修改
            return true;
        } else if ($this->userId == $targetUserId && $this->isLogin()) {
            //自己,可以修改
            return true;
        } else {

            if ($this->isUserHasAuthority('USER_UPDATE')) {
                //有user权限的管理员
                $targetUser = new UserModel($targetUserId);
                if (!$targetUser->isAdmin) {
                    return true;
                } else {
                    $this->errorMsg = "不能修改其他管理员信息";
                    return false;
                }
            } else {
                $this->errorMsg = "无权修改用户信息";
                return false;
            }
        }
        //安全验证 e---
    }


    //------------------------------------------------------------------------------------------------------------------

    /**
     * 查看账户状态是否正常和权限
     * @param $key 如果不填参数,就只检查账号是否被禁言和删除,不检测权限
     * @return bool
     */
    public function isUserHasAuthority($key = false) {
        global $_AUT;
        if (!self::isLogin()) {
            BasicTool::echoJson(0, "账号验证失败，请重新登录");
            die();
        }

        if ($this->row == null) {
            $sql = "SELECT u.*,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE u.id = ({$this->userId})";
            $this->row = $this->sqltool->getRowBySql($sql);
        }

        if ($this->row['blocktime'] - time() > 0) {

            $time = date("Y-m-d H:i:s", $this->blockTime);
            $this->errorMsg = "此账号已因({$this->row[blockreason]})被禁言. 恢复日期({$time}). 如有任何问题,请与我们运维组联系.";
            return false;
        }

        if ($this->row['alias'] == "NewUser") {
            $this->errorMsg = "请先在 '个人信息设置' 里设置一个 '昵称'";
            return false;
        }

        if ($this->row['is_del'] == 1) {
            $this->errorMsg = "你的账号已经被冻结";
            return false;
        }


        if ($key != false) {
            if (!($this->row['authority'] & $_AUT[$key])) {
                $this->errorMsg = "你所在用户组无权做此操作. (如果你是一位新注册用户,请到邮箱激活账号)";
                return false;
            }
        }


        return true;

    }


    /**
     * 解释性别, 将性别的数字解释成文字
     * @param $int
     * @return string
     */
    public function translateGender($int) {
        switch ($int) {
            case 0:
                return "女";
                break;
            case 1:
                return "男";
                break;
            case 2:
                return "保密";
                break;
        }
    }

    /**
     * 判断用户是否登录以及cookie是否合法
     * @return bool
     */
    public function isLogin() {
        $encodeKey = md5($_COOKIE['cc_id'] . $_COOKIE['cc_uc'] . $_COOKIE['cc_ii'] . $_COOKIE['cc_na'] . $_COOKIE['cc_ia'] . $_COOKIE['cc_au'] . $_COOKIE['cc_bl'] . self::$key);
        //return $encodeKey == $_COOKIE['cc_cc'] ? true : false;
        if ($encodeKey !== $_COOKIE['cc_cc']) {
            return false;
        }
        return true;

    }

    /**
     * 判断管理员是否登录以及cookie是否合法
     * @return bool
     */
    public function isAdminLogin() {
        if (self::isLogin()) {
            if ($this->isAdmin) {
                return true;
            }
        }
        return false;
    }

    /**
     * 验证登录,设置cookie
     * @param  string $userType 用户类型
     * @param  string $username
     * @param  string $password
     * @return user<json> | false
     */
    public function login($name, $pwd, $usertype = 'user') {
        //user->|id|user_class_id|name|img|pwd|alias|gender|blocktime|blockreason|registertime|is_del|
        $sql = "SELECT u.*,u_c.title,is_admin,authority,device FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE name in ('{$name}')";
        if ($usertype == 'admin') {
            $sql .= "AND is_admin in (1)";
        }

        if ($row = $this->sqltool->getRowBySql($sql)) {
            if ($row['pwd'] == md5($pwd)) {

                //编码秘钥
                $encodeKey = md5($row['id'] . $row['user_class_id'] . $row['institution_id'] . $row['name'] . $row['is_admin'] . $row['authority'] . $row['blocktime'] . self::$key);

                $time = $usertype == "user" ? time() + 3600 * 24 * 365 * 10 : 0;

                $arr = [];
                $arr['cc_al'] = $row['alias'];
                $arr['cc_im'] = $row['img'];
                $arr['cc_ge'] = $row['gender'];
                $arr['cc_id'] = $row['id'];//保护
                $arr['cc_uc'] = $row['user_class_id'];//保护
                $arr['cc_ii'] = $row['institution_id'];//保护
                $arr['cc_na'] = $row['name'];//保护
                $arr['cc_ia'] = $row['is_admin'];//保护
                $arr['cc_au'] = $row['authority'];//保护
                $arr['cc_title'] = $row['title'];
                $arr['cc_bl'] = $row['blocktime'];//保护
                $arr['cc_br'] = $row['blockreason'];
                $arr['cc_rt'] = $row['registertime'];
                $arr['cc_ye'] = BasicTool::translateEnrollYear($row['enroll_year']);
                $arr['cc_cc'] = $encodeKey;//验证码
                $arr['device'] = $row['device'];

                foreach ($arr as $k => $v) {
                    setcookie($k, $v, $time, '/');
                }
                return $this->getProfileOfUserById($arr['cc_id']);

            }
        }
        $this->errorMsg = '用户名或密码错误';
        return false;
    }


    /**
     * 用户登出
     */
    public function logout() {

        foreach ($_COOKIE as $k => $v) {
            setcookie($k, "", time() - 10000000, '/');
        }
        return true;
    }



    //更改用户为普通用户
    public function changeUserClassToNormal($username) {
        $sql = "UPDATE user SET user_class_id = 7 WHERE name in ('{$username}')";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;
    }

    public function blockUser($userId, $days, $reason) {
        $time = time() + (3600 * 24) * $days;
        $sql = "UPDATE user SET blocktime = {$time},blockreason = '{$reason}' WHERE id IN ($userId)";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        } else {
            $this->errorMsg = "没有数据受影响";
            return false;
        }
    }

    /**
     * 更新用户cookie
     * @param $userId
     * @return bool
     */
    public function updateCookie() {

        if ($this->isLogin() == false) {
            $this->errorMsg = "当前没有用户登录";
            return false;
        }

        $userId = $this->userId;
        $sql = "SELECT u.*,u_c.title,is_admin,device,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE u.id in ('{$userId}')";

        if ($row = $this->sqltool->getRowBySql($sql)) {
            //编码秘钥
            $encodeKey = md5($row['id'] . $row['user_class_id'] . $row['institution_id'] . $row['name'] . $row['is_admin'] . $row['authority'] . $row['blocktime'] . self::$key);

            $time = time() + 3600 * 24 * 365 * 10;

            $arr = [];
            $arr['cc_al'] = $row['alias'];
            $arr['cc_im'] = $row['img'];
            $arr['cc_ge'] = $row['gender'];
            $arr['cc_id'] = $row['id'];//保护
            $arr['cc_uc'] = $row['user_class_id'];//保护
            $arr['cc_ii'] = $row['institution_id'];//保护
            $arr['cc_na'] = $row['name'];//保护
            $arr['cc_ia'] = $row['is_admin'];//保护
            $arr['cc_au'] = $row['authority'];//保护
            $arr['cc_title'] = $row['title'];
            $arr['cc_bl'] = $row['blocktime'];//保护
            $arr['cc_br'] = $row['blockreason'];
            $arr['cc_rt'] = $row['registertime'];
            $arr['cc_ye'] = BasicTool::translateEnrollYear($row['enroll_year']);
            $arr['cc_cc'] = $encodeKey;//验证码
            $arr['device'] = $row['device'];

            foreach ($arr as $k => $v) {
                setcookie($k, $v, $time, '/');
            }

            return $this->getProfileOfUserById($arr['cc_id']);
        }
        $this->errorMsg = $row;
        return false;

    }

    /**
     * 增加用户活跃度
     * @return bool
     */
    public function addActivity() {

        $userId = $this->userId;
        $sql = "UPDATE user SET activist = activist + 1 WHERE id = '{$userId}'";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }


    /**
     * 统计已经注册设备的数量
     * @return mixed
     */
    public function getCountOfDevice() {
        $sql = "SELECT count(device) FROM user WHERE device <> '0'";
        return $this->sqltool->getCountBySql($sql);
    }

    /**
     * 统计有效用户
     * @return mixed
     */
    public function getCountOfUserForValid() {
        $sql = "SELECT count(activist) FROM user WHERE activist > '0'";
        return $this->sqltool->getCountBySql($sql);
    }

    /**
     * 退出登录清空设备
     */
    public function logoutDevice() {
        $uid = $this->userId;
        $sql = "UPDATE user SET device = '0' WHERE id = {$uid}";
        $this->sqltool->query($sql);
    }

    /**
     * @return array
     */
    public function getListOfMsgReceive() {
        $uid = $this->userId or BasicTool::throwException("未登录");
        $table = 'msg';
        $sql = "SELECT M.*,user.img,user.alias,user.gender FROM (SELECT * FROM {$table} WHERE receiver_id = {$uid} OR alert = 1) AS M INNER JOIN user ON sender_id = user.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM {$table} WHERE receiver_id = {$uid} ORDER BY id DESC";
        $result = parent::getListWithPage($table, $sql, $countSql, 40);
        foreach ($result as $k1 => $v1) {
            foreach ($v1 as $k2 => $v2) {
                if ($k2 == "time") {
                    $result[$k1][$k2] = BasicTool::translateTime($v2);
                }
            }
        }
        return $result;
    }

    public function getListOfMsgSend() {
        $uid = $this->userId or BasicTool::throwException("未登录");
        $sql = "SELECT * FROM msg WHERE sender_id = {$uid}";
        return $this->sqltool->getListBySql($sql);
    }




    /**
     * --------------------------------------------------------
     * --------------------------------------------------------
     *  -------------------- 2.0 已审查 -----------------------
     * --------------------------------------------------------
     * --------------------------------------------------------
     */

    /**
     * @param $username
     * @param $password
     * @return user|bool
     */
    public function register($user_class_id, $institutionId, $name, $pwd, $degree, $alias, $major, $wechat, $description) {
        $arr['name'] = $name;
        $arr['pwd'] = md5($pwd);
        $arr['user_class_id'] = $user_class_id ? $user_class_id : 7;
        $arr['institution_id'] = $institutionId ? $institutionId : 1;
        $arr['degree'] = $degree ? $degree : "";
        $arr['alias'] = $alias ? $alias : "";
        $arr['major'] = $major ? $major : "";
        $arr['wechat'] = $wechat ? $wechat : "";
        $arr['description'] = $description ? $description : "";
        $arr['registertime'] = time();
        return $this->addRow('user', $arr);
    }

    public function updateUserByAdmin($targetUserId, $institutionId, $alias, $user_class_id, $gender, $blocktime, $blockreason, $major, $enroll_year, $description, $wechat) {
        $sql = "UPDATE user SET alias='{$alias}',user_class_id='{$user_class_id}',institution_id='{$institutionId}',gender='{$gender}',blocktime='{$blocktime}',blockreason='{$blockreason}',major='{$major}',enroll_year='{$enroll_year}',description='{$description}',wechat='{$wechat}' WHERE id in ({$targetUserId})";
        return $this->sqltool->query($sql);
    }

    /**
     * 更新别名
     * @param $val
     * @return bool
     */
    public function updateAlias($val) {
        return self::updateRowById('user', $this->userId, ['alias' => $val]);
    }

    /**
     * 更新性别
     * @param $val
     * @return bool
     */
    public function updateGender($val) {
        return self::updateRowById('user', $this->userId, ['gender' => $val]);
    }

    /**
     * 更新专业
     * @param $val
     * @return bool
     */
    public function updateMajor($val) {
        return self::updateRowById('user', $this->userId, ['major' => $val]);
    }

    /**
     * 更新微信
     * @param $val
     * @return bool
     */
    public function updateWechat($val) {
        return self::updateRowById('user', $this->userId, ['wechat' => $val]);
    }

    /**
     * 更新个人签名
     * @param $val
     * @return bool
     */
    public function updateDescription($val) {
        return self::updateRowById('user', $this->userId, ['description' => $val]);
    }

    /**
     * 更新入学年
     * @param $val
     * @return bool
     */
    public function updateEnrollYear($val) {
        return self::updateRowById('user', $this->userId, ['enroll_year' => $val]);
    }

    /**
     * 更新学位等级
     * @param $val
     * @return bool
     */
    public function updateDegree($val) {
        return self::updateRowById('user', $this->userId, ['degree' => $val]);
    }

    /**
     * 更新密码
     * @param $val
     * @return bool
     */
    public function updatePassword($val) {
        return self::updateRowById('user', $this->userId, ['pwd' => $val]);
    }

    /**
     * 随机修改密码,并将新密码返回
     * @param $username
     * @return int
     */
    public function changePasswordRandomly($username) {
        $pwd = rand(100000, 999999);
        $md5pwd = md5($pwd);
        $sql = "UPDATE user SET pwd = '{$md5pwd}' WHERE name in ('{$username}')";
        $this->sqltool->query($sql);
        return $pwd;
    }

    /**
     * 获取用户badge
     * @return mixed
     * @throws Exception
     */
    public function getBadge() {
        $uid = $this->userId or BasicTool::throwException("UID获取失败:#1");
        $sql = "SELECT badge FROM user WHERE id = {$uid}";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['badge'];
    }

    /**
     * badge + 1 且 返回新的badge结果
     * @return mixed
     * @throws Exception
     */
    public function addOnceCountInBadge() {
        $uid = $this->userId or BasicTool::throwException("UID获取失败:#2");
        $sql = "UPDATE user SET badge = badge + 1 WHERE id = {$uid}";
        $this->sqltool->query($sql);
        $sql = "SELECT badge FROM user WHERE id = {$uid}";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['badge'];
    }

    /**
     * 清楚badge
     * @return bool|\mysqli_result
     * @throws Exception
     */
    public function clearBadge() {
        $uid = $this->userId or BasicTool::throwException("UID获取失败:#3");
        $sql = "UPDATE user SET badge = 0 WHERE id = {$uid}";
        return $this->sqltool->query($sql);
    }

    public function getCredit() {
        $uid = $this->userId or BasicTool::throwException("UID获取失败:#4");
        $sql = "SELECT credit from user WHERE id IN ({$uid})";
        $row = $this->sqltool->query($sql);
        return $row['credit'];
    }

    public function getUserIdByName($name){
        $sql = "SELECT id FROM user WHERE name in ('$name')";
        return $this->sqltool->getRowBySql($sql)['id'];
    }

    public function getUserListBySearch($fieldName,$value){
        $sql = "SELECT * FROM user WHERE {$fieldName} LIKE '%{$value}%'";
        return $this->sqltool->getListBySql($sql);
    }

     public function updateDevice($uid,$deviceType,$deviceToken){
        if(!$this->userId) return false;
        $sql = "UPDATE user SET device = '0' WHERE device in ('{$deviceToken}')";
        $this->sqltool->query($sql) or BasicTool::throwException("清理设备失败");
        $sql = "UPDATE user SET device_type = '{$deviceType}',device='{$deviceToken}' WHERE id in ($uid)";
        $this->sqltool->query($sql) or BasicTool::throwException("更新设备Token失败");
    }

    /**
     * 获取用户每日积分
     * @return bool|string          成功返回array["积分描述",积分值];,失败返回false
     * @throws Exception
     */
    public function getDailyCredit(){
        $uid = $this->userId or BasicTool::throwException("请先登录账号");
        $today = strtotime(date("Y-m-d")." 00:00:01");
        $sql = "SELECT checkin_last_time,checkin_count,credit FROM user WHERE id IN ({$uid})";
        $row = $this->sqltool->getRowBySql($sql);
        $checkinTime = $row["checkin_last_time"];
        $checkinCount = $row["checkin_count"];
        $userCredit = $row["credit"];

        $timeGap = $today-$checkinTime;

        if($timeGap == 0){
            $this->errorMsg = "今日已领取过积分了哦 ^_^";
            return false;
        }else if ($timeGap == 86400) {
            $sql = "UPDATE user SET checkin_last_time = '{$today}', checkin_count = checkin_count+1 WHERE id IN ({$uid})";
            $checkinCount++;
        }else{
            $sql = "UPDATE user SET checkin_last_time = '{$today}', checkin_count = 1 WHERE id IN ({$uid})";
            $checkinCount =1;
        }


        if($this->sqltool->query($sql)){
            $transactionModel = new TransactionModel();
            $creditAwardCount = count(Credit::$dailyCredit);
            $creditAward = [];
            if($checkinCount<$creditAwardCount){
                $creditAward = Credit::$dailyCredit[$checkinCount-1];
            }else{
                $creditAward = end(Credit::$dailyCredit);
            }
            $credit = (float)$creditAward['credit'];
            $userCredit+=$credit;
            $description = "恭喜获得{$credit}点积分！今天是你连续登录的【第{$checkinCount}天】！连续天数越多, 积分越多哦！";
            if($transactionModel->addCredit($uid,$credit,"连续登陆第{$checkinCount}天","dailyCredit",$uid)){
                return [$description,$credit];
            }else{
                return false;
            }
        }else{
            $this->errorMsg = "更新用户领取状态出错";
            return false;
        }


    }
}


?>
