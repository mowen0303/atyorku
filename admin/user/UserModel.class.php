<?php
namespace admin\user;   //-- 注意 --//
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/key.php";

class UserModel extends Model
{
    //秘钥
    private static $key = KEY;

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
    public $deviceToke = 0;


    public $row = null;

    public function __construct($userId = false)
    {
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
    private function setUserInfo($userId = false)
    {

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
            $this->major = $arr['major'];;
            $this->enrollYear = $arr['enroll_year'];;
            $this->description = $arr['description'];;
            $this->credit = $arr['credit'];;
            $this->activist = $arr['activist'];;
            $this->wechat = $arr['wechat'];
            $this->deviceToke = $arr['device'];

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
    public function getProfileOfUserById($id, $onlyShowBasic = false)
    {

        $condition = $onlyShowBasic == true ? "" : ",u.activist,u.credit,u.name,u.registertime,u_c.is_admin,u_c.authority";

        $sql = "SELECT u.id,u.degree,u.wechat,u.user_class_id,u.major,u.enroll_year,u.description,u.img,u.alias,u.gender,u_c.title,u.blocktime,u.blockreason {$condition} FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE u.id in ({$id}) AND u.is_del = 0";

        $arr = $this->sqltool->getRowBySql($sql);

        foreach ($arr as $k => $v) {

            if ($k == "enroll_year") {
                $arr['enrollYearTranslate'] = BasicTool::translateEnrollYear($v);
            }
            if ($k == "registertime") {
                $arr[$k] = date('Y-m-d', $arr[$k]);
            }
            if ($k == "blocktime") {

                if ($v - time() > 0) {
                    $arr['blockState'] = "1";
                    $arr['blockToTime'] = date('Y-m-d H:i:s', $v);
                } else {
                    $arr['blockState'] = "0";
                }
                $arr['blockToTime'] = date('Y-m-d H:i:s', $v);
                $arr[$k] = date('Y-m-d', $arr[$k]);
            }

        }
        return $arr;
    }


    /*
     * user_class        | id | user_class_id | name | pwd | alias  | gender | blocktime  | blockreason |
     *
     */
    public function getListOfUser($isAdmin, $userClass = false, $pageSize = 40)
    {
        // user * user_class          | id | user_class_id | name | img | pwd |alias | gender | blocktime | blockreason |title | is_admin | authority |

        $condition = "";
        if ($userClass !== false) {
            $condition .= "AND u_c.id = $userClass";
        }
        $table = 'user';
        $sql = "SELECT u.*,u_c.title,is_admin,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE is_admin in ({$isAdmin}) {$condition} AND is_del =0 ORDER BY u.id DESC";
        $countSql = "SELECT COUNT(*) FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE is_admin in ({$isAdmin}) {$condition} AND is_del =0 ORDER BY u.id DESC";
        return parent::getListWithPage($table, $sql, $countSql, $pageSize);
    }

    /**
     * 获得用户的分类列表
     * @param int $pageCurrent
     * @param int $pageSize
     * @return 二维数组
     */
    public function getListOfUserClass($pageSize = 50)
    {
        $table = 'user_class';
        $sql = "SELECT * FROM {$table}";
        $countSql = null;
        return parent::getListWithPage($table, $sql, $countSql, $pageSize);   //-- 注意 --//
    }


    /**
     * 输出用户分类的option列表
     */
    public function echoUserClassOption()
    {
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
    public function isAuthorityToManageUserByTargetUserId($targetUserId)
    {
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
    public function isUserHasAuthority($key = false)
    {
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
    public function translateGender($int)
    {
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
    public function isLogin()
    {
        $encodeKey = md5($_COOKIE['cc_id'] . $_COOKIE['cc_uc'] . $_COOKIE['cc_na'] . $_COOKIE['cc_ia'] . $_COOKIE['cc_au'] . $_COOKIE['cc_bl'] . self::$key);
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
    public function isAdminLogin()
    {
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
    public function login($name, $pwd, $usertype = 'user')
    {
        //user->|id|user_class_id|name|img|pwd|alias|gender|blocktime|blockreason|registertime|is_del|
        $sql = "SELECT u.*,u_c.title,is_admin,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE name in ('{$name}')";
        if ($usertype == 'admin') {
            $sql .= "AND is_admin in (1)";
        }

        if ($row = $this->sqltool->getRowBySql($sql)) {
            if ($row['pwd'] == md5($pwd)) {

                //编码秘钥
                $encodeKey = md5($row['id'] . $row['user_class_id'] . $row['name'] . $row['is_admin'] . $row['authority'] . $row['blocktime'] . self::$key);

                $time = $usertype == "user" ? time() + 3600 * 24 * 365 * 10 : 0;

                $arr = [];
                $arr['cc_al'] = $row['alias'];
                $arr['cc_im'] = $row['img'];
                $arr['cc_ge'] = $row['gender'];
                $arr['cc_id'] = $row['id'];//保护
                $arr['cc_uc'] = $row['user_class_id'];//保护
                $arr['cc_na'] = $row['name'];//保护
                $arr['cc_ia'] = $row['is_admin'];//保护
                $arr['cc_au'] = $row['authority'];//保护
                $arr['cc_title'] = $row['title'];
                $arr['cc_bl'] = $row['blocktime'];//保护
                $arr['cc_br'] = $row['blockreason'];
                $arr['cc_rt'] = $row['registertime'];
                $arr['cc_ye'] = BasicTool::translateEnrollYear($row['enroll_year']);
                $arr['cc_cc'] = $encodeKey;//验证码

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
    public function logout()
    {
        foreach ($_COOKIE as $k => $v) {
            setcookie($k, $v, time() - 1, '/');
        }
        return true;
    }

    //增加积分
    public function addCredit($value, $userId)
    {

        if (!$this->isUserHasAuthority('GOD')) {
            $this->errorMsg = "没有权限";
            return false;
        }

        if (!is_numeric($value)) {
            $this->errorMsg = "请输入正确的数字";
            return false;
        }

        $sql = "UPDATE user SET credit = credit + {$value} WHERE id in ({$userId})";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }


    //更改用户为普通用户
    public function changeUserClassToNormal($username)
    {
        $sql = "UPDATE user SET user_class_id = 7 WHERE name in ('{$username}')";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受影响";
        return false;
    }

    public function blockUser($userId, $days, $reason)
    {
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
    public function updateCookie()
    {

        if ($this->isLogin() == false) {
            $this->errorMsg = "当前没有用户登录";
            return false;
        }

        $userId = $this->userId;
        $sql = "SELECT u.*,u_c.title,is_admin,authority FROM user AS u INNER JOIN user_class AS u_c ON u.user_class_id = u_c.id WHERE u.id in ('{$userId}')";

        if ($row = $this->sqltool->getRowBySql($sql)) {
            //编码秘钥
            $encodeKey = md5($row['id'] . $row['user_class_id'] . $row['name'] . $row['is_admin'] . $row['authority'] . $row['blocktime'] . self::$key);

            $time = time() + 3600 * 24 * 365 * 10;

            $arr = [];
            $arr['cc_al'] = $row['alias'];
            $arr['cc_im'] = $row['img'];
            $arr['cc_ge'] = $row['gender'];
            $arr['cc_id'] = $row['id'];//保护
            $arr['cc_uc'] = $row['user_class_id'];//保护
            $arr['cc_na'] = $row['name'];//保护
            $arr['cc_ia'] = $row['is_admin'];//保护
            $arr['cc_au'] = $row['authority'];//保护
            $arr['cc_title'] = $row['title'];
            $arr['cc_bl'] = $row['blocktime'];//保护
            $arr['cc_br'] = $row['blockreason'];
            $arr['cc_rt'] = $row['registertime'];
            $arr['cc_ye'] = BasicTool::translateEnrollYear($row['enroll_year']);
            $arr['cc_cc'] = $encodeKey;//验证码

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
    public function addActivity()
    {

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
     * 向特定用户推送
     * @param $diviceToken
     * @param $msg
     * @param $typ
     * @return bool
     */
    public function pushMsg($senderId, $senderAlias, $type, $typeId, $content)
    {
        // Put your device token here (without spaces):
        //5dbd8b1a99e2c07c42914f1e726d0388801ddadf876462416a90c77bb98c94d1
        //a26c5e114dea2564018531ef4b2ae350d11569ca2970f6c4ae28d5f022da5d17
        //78166675a9f9c452f68a7c300c3f8516c44254ff9dca5953a38553a3e6f36189
        self::sendMsg($senderId, $this->userId, $type, $typeId, $content, 0);
        self::applePush($this->deviceToke, $senderAlias . ": ", $type, $typeId, $content);
    }

    /**
     * 向所有用户推送
     * @param $deviceArr
     * @param $senderId
     * @param $senderAlias
     * @param $type
     * @param $typeId
     * @param $content
     * @param int $alert
     */
    public function pushMsgToAllUser($type, $typeId, $content, $silent = false)
    {
        if (self::sendMsg("28", "28", $type, $typeId, $content, 1)) {
            echo "小纸条写入成功";
        } else {
            echo "小纸条写入失败";
        }
        echo "<br>";
        $start = 0;
        $size = 10;
        $i = 0;
        while ($deviceArr = self::getListOfDevice($start, $size)) {
            foreach ($deviceArr as $row) {
                echo $i++ . "------UID:" . $row['id'] . "------" . $row['device'] . "<br>";
                if (self::applePush($row['device'], "", $type, $typeId, $content, 1, $silent)) {
                    echo "------成功<br>";
                } else {
                    echo "------失败<br>";
                }
            }
            $start += $size;
        }
        echo "END";
        return;
    }

    /**
     * 获取设备列表
     * @param $page
     * @param $pageSize
     * @return array
     */
    private function getListOfDevice($page, $pageSize)
    {
        $sql = "SELECT id,device FROM user WHERE device <> '0' LIMIT $page,$pageSize";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * 统计已经注册设备的数量
     * @return mixed
     */
    public function getCountOfDevice()
    {
        $sql = "SELECT count(device) FROM user WHERE device <> '0'";
        return $this->sqltool->getCountBySql($sql);
    }

    /**
     * 统计有效用户
     * @return mixed
     */
    public function getCountOfUserForValid()
    {
        $sql = "SELECT count(activist) FROM user WHERE activist > '0'";
        return $this->sqltool->getCountBySql($sql);
    }


    /**
     * 手机信息推送
     * @param $senderAlias
     * @param $type
     * @param $typeId
     * @param $content
     * @return bool
     */
    public function applePush($deviceToken, $senderAlias, $type, $typeId, $content, $alert = 0, $silent = false)
    {
        if ($deviceToken != "0") {
            //$deviceToken = $this->deviceToke;
            //$deviceToken = "e1e4f6a7f01ec5146829718c2730195e1f1110c6952a5b61db0e1a5b5649c725"; //jerry
            //$deviceToken = "78166675a9f9c452f68a7c300c3f8516c44254ff9dca5953a38553a3e6f36189";  //wendy
            //1d3e0f65535853b8b91fa055e0199651e2dea55cf479c53a1aeb9c92b5485adc  jerry online

            // Put your private key's passphrase here:
            $passphrase = 'miss0226';


            ////////////////////////////////////////////////////////////////////////////////

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER["DOCUMENT_ROOT"] . '/commonClass/ck2.pem');
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client(
                'ssl://gateway.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp) {
                $this->errorMsg = "Failed to connect: $err $errstr" . PHP_EOL;
                return false;
            }

            if ($alert == 0) {
                $badge = $this->getBadge();
            } else {
                $badge = 1;
            }


            // Create the payload body
            if ($silent == true) {
                $body['aps'] = array(
                    'type' => $type,
                    'typeId' => $typeId,
                    'badge' => (int)$badge
                );
            } else {
                $body['aps'] = array(
                    'alert' => $senderAlias . $content,
                    'type' => $type,
                    'typeId' => $typeId,
                    'badge' => (int)$badge,
                    'sound' => 'default'
                );
            }


            // Encode the payload as JSON
            $payload = json_encode($body);


            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            fclose($fp);

            if (!$result) {
                $this->errorMsg = 'Message not delivered' . PHP_EOL;
                return false;
            } else {
                $this->errorMsg = 'Message successfully delivered' . $deviceToken . PHP_EOL;

                return true;
            }
            return false;
        } else {
            return false;
        }
    }


    /**
     * 退出登录清空设备
     */
    public function logoutDevice()
    {
        $uid = $this->userId;
        $sql = "UPDATE user SET device = '0' WHERE id = {$uid}";
        $this->sqltool->query($sql);
    }

    /**
     * @param $senderId
     * @param $userId
     * @param $type    forum/forumComment/course/courseComment/guide/msg/
     * @param $content
     * @param $alert 是否是全体推送 0 否 1是
     * @return bool
     */
    private function sendMsg($senderId, $receiverId, $type, $typeId, $content, $alert = 0)
    {
        $time = time();
        if ($senderId == null) {
            $senderId = 0;
        }
        if ($receiverId == null) {
            return false;
        }
        if ($type == null) {
            return false;
        }
        if ($content == null) {
            return false;
        }
        if ($typeId == null) {
            return false;
        }
        $sql = "INSERT INTO `msg`(`sender_id`, `receiver_id`, `content`, `type`,`type_id`,`time`,`alert`) VALUES ('{$senderId}','{$receiverId}','{$content}','{$type}','{$typeId}',{$time},{$alert})";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "数据未受影响";
        return false;
    }

    public function getListOfMsgReceive()
    {
        $uid = $this->userId;
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

    public function getListOfMsgSend()
    {
        $uid = $this->userId;
        $sql = "SELECT * FROM msg WHERE sender_id = {$uid}";
        return $this->sqltool->getListBySql($sql);
    }


    public function getBadge()
    {
        $uid = $this->userId;
        $sql = "UPDATE user SET badge = badge + 1 WHERE id = {$uid}";
        $this->sqltool->query($sql);
        $sql = "SELECT badge FROM user WHERE id = {$uid}";
        $row = $this->sqltool->getRowBySql($sql);
        return $row['badge'];
    }

    public function clearBadge()
    {
        $uid = $this->userId;
        $sql = "UPDATE user SET badge = 0 WHERE id = {$uid}";
        return $this->sqltool->query($sql);
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
    public function register($user_class_id,$name,$pwd,$degree,$alias,$major,$wechat,$description)
    {
        $arr['name'] = $name;
        $arr['pwd'] = md5($pwd);
        $arr['user_class_id'] = $user_class_id ? $user_class_id : 7;
        $arr['degree'] = $degree?$degree:"";
        $arr['alias'] = $alias?$alias:"";
        $arr['major'] = $major?$major:"";
        $arr['wechat'] = $wechat?$wechat:"";
        $arr['description'] = $description?$description:"";
        $arr['registertime']=time();
        return $this->addRow('user',$arr);

//邮箱验证
//        $code = md5(rand(999,999999));
//        $arr2 = [];
//        $arr2['email'] = $username;
//        $arr2['code'] = $code;
//        $arr2['is_valid'] = "1";
//        $currentUser->addRow('user_code',$arr2) or BasicTool::throwException("账号注册成功,但激活码配置出错,不能正常激活,请联系管理员");
//        $id = $currentUser->idOfInsert;
//        $mailBody = '<p>AtYorkU账号注册成功,请点击下面链接进行激活:</p><p><a href="http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email='.$username.'&code='.$code.'&id='.$id.'" target="_blank">http://www.atyorku.ca/admin/user/userController.php?action=activateAccount&email='.$username.'&code='.$code.'&id='.$id.'</a></p>';
//        if(BasicTool::mailTo($username,"AtYorkU账号激活邮件",$mailBody)){
//            $msg = "注册成功，可以登录了! (为了保证账号正常使用，请尽快到邮箱激活账号)";
//        } else {
//            $msg = "注册成功，可以登录了! (当前邮件服务器压力过大，激活邮件发送失败，请稍登录账号后重新发送)";
//        }
    }

    public function updateUserByAdmin($targetUserId,$alias,$user_class_id,$gender,$blocktime,$blockreason,$major,$enroll_year,$description,$wechat){
        $sql = "UPDATE user SET alias='{$alias}',user_class_id='{$user_class_id}',gender='{$gender}',blocktime='{$blocktime}',blockreason='{$blockreason}',major='{$major}',enroll_year='{$enroll_year}',description='{$description}',wechat='{$wechat}' WHERE id in ({$targetUserId})";
        return $this->sqltool->query($sql);
    }

    /**
     * 更新别名
     * @param $val
     * @return bool
     */
    public function updateAlias($val)
    {
        return self::updateRowById('user', $this->userId, ['alias' => $val]);
    }

    /**
     * 更新性别
     * @param $val
     * @return bool
     */
    public function updateGender($val)
    {
        return self::updateRowById('user', $this->userId, ['gender' => $val]);
    }

    /**
     * 更新专业
     * @param $val
     * @return bool
     */
    public function updateMajor($val)
    {
        return self::updateRowById('user', $this->userId, ['major' => $val]);
    }

    /**
     * 更新微信
     * @param $val
     * @return bool
     */
    public function updateWechat($val)
    {
        return self::updateRowById('user', $this->userId, ['wechat' => $val]);
    }

    /**
     * 更新个人签名
     * @param $val
     * @return bool
     */
    public function updateDescription($val)
    {
        return self::updateRowById('user', $this->userId, ['description' => $val]);
    }

    /**
     * 更新入学年
     * @param $val
     * @return bool
     */
    public function updateEnrollYear($val)
    {
        return self::updateRowById('user', $this->userId, ['enroll_year' => $val]);
    }

    /**
     * 更新学位等级
     * @param $val
     * @return bool
     */
    public function updateDegree($val)
    {
        return self::updateRowById('user', $this->userId, ['degree' => $val]);
    }

    /**
     * 更新密码
     * @param $val
     * @return bool
     */
    public function updatePassword($val)
    {
        return self::updateRowById('user', $this->userId, ['pwd' => $val]);
    }

}


?>