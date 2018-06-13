<?php
namespace admin\msg;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class MsgModel extends Model {
    private $enablePush = true; //测试阶段，禁用信息推送, 新版本删除原始推送

    /**
     * 给指定用户推送一条信息
     * @param $receiverId   消息接受者id
     * @param $msgType      产品数据库表名
     * @param $msgTypeId    产品数据库表的id字段
     * @param $content      消息内容
     * @return bool
     */
    public function pushMsgToUser($receiverId, $msgType, $msgTypeId, $content,$specifySenderId=0) {
        $receiverId or BasicTool::throwException("缺少 receiverId");
        $receiverUser = new UserModel($receiverId);
        if ((int)$specifySenderId>0){
            $senderUser = new UserModel($specifySenderId);
        }else{
            $senderUser = new UserModel();
            $senderUser->userId or BasicTool::throwException("请先登录");
        }

        if($senderUser->userId == $receiverUser->userId) {
            return false;
        }
        $receiverBadge = $receiverUser->addOnceCountInBadge();
        self::addMsg($senderUser->userId, $receiverUser->userId, $msgType, $msgTypeId, $content);
        //推送
        if ($this->enablePush == false) return false;
        //苹果
        if ($receiverUser->deviceToken == '0') return false;
        if ($receiverUser->deviceType == "ios") {
            //echo("ios");
            return self::applePushRN($receiverUser->deviceToken, $senderUser->aliasName, $msgType, $msgTypeId, $content, $receiverBadge);
        } else if ($receiverUser->deviceType == "android") {
            //echo("android");
            return self::androidPushRN($receiverUser->deviceToken, $senderUser->aliasName, $msgType, $msgTypeId, $content, $receiverBadge);
        }
    }

    /**
     * 群推一条信息给全体开启信息推送的用户
     * @param $msgType
     * @param $msgTypeId
     * @param $content
     * @param bool $silent
     * @return bool
     */
    public function pushMsgToAllUsers($msgType, $msgTypeId, $content, $silent = false) {
        //写入小纸条
        if (self::addMsg(28, 28, $msgType, $msgTypeId, $content, 1)) {
            echo "小纸条写入成功<br>";
        } else {
            echo "小纸条写入失败<br>";
        }

        //推送
        if ($this->enablePush == false) return false;
        $start = 0;
        $size = 200;
        $i = 0;
        $sql = "UPDATE user SET badge = badge+1";
        $this->sqltool->query($sql);

        while ($deviceArr = self::getListOfDevice($start, $size)) {
            foreach ($deviceArr as $row) {
                //if($row['id']!=1) return false;
                echo $i++ . "--UID:" . $row['id'] . "--" . $row['device_type']."--" . $row['device'];
                if($row['device_type']=="ios"){
                    $bool = self::applePush($row['device'], false, $msgType, $msgTypeId, $content, $row['badge'], $silent);
                }else{
                    $bool = self::androidPushRN($row['device'], false,$msgType, $msgTypeId, $content, $row['badge'],$silent);
                }

                if ($bool) {
                    echo "--成功<br>";
                } else {
                    echo "--失败--原因：{$this->errorMsg}<br>";
                }

            }
            $start += $size;
        }
        echo "END";
    }

    /**
     * 获取设备列表
     * @param $start
     * @param $size
     * @return array
     */
    private function getListOfDevice($start, $size) {
        $sql = "SELECT id,badge,device,device_type FROM user WHERE device <> '0' LIMIT $start,$size";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * 添加一行信息到msg表
     * @param $senderId
     * @param $userId
     * @param $type    forum/forumComment/course/courseComment/guide/msg/
     * @param $content
     * @param $alert 是否是全体推送 0 否 1是
     * @return bool
     */
    private function addMsg($senderId, $receiverId, $type, $typeId, $content, $alert = 0) {
        $arr['sender_id'] = $senderId;
        $arr['receiver_id'] = $receiverId;
        $arr['content'] = $content;
        $arr['type'] = $type;
        $arr['type_id'] = $typeId;
        $arr['time'] = time();
        $arr['alert'] = $alert;
        return $this->addRow('msg', $arr);
    }

    /**
     * 苹果推送
     * @param $deviceToken
     * @param bool $senderAlias
     * @param $msgType
     * @param $msgTypeId
     * @param $content
     * @param $badge
     * @param bool $silent
     * @return bool
     */
    private function applePushRN($deviceToken, $senderAlias = false, $msgType, $msgTypeId, $content, $badge, $silent = false) {
        if ($deviceToken == '0') return false;
        $senderAlias = $senderAlias ? $senderAlias . ": " : "";
        $passphrase = 'miss0226';
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
        // Create the payload body
        if ($silent) {
            $body['aps'] = array(
                'type' => $msgType,
                'typeId' => $msgTypeId,
                'badge' => (int)$badge
            );
        } else {
            $body['aps'] = array(
                'alert' => $senderAlias . $content,
                'type' => $msgType,
                'typeId' => $msgTypeId,
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
    }

    private function androidPushRN($deviceToken, $senderAlias = false, $msgType, $msgTypeId, $content, $badge, $silent = false) {
        if ($deviceToken == '0') return false;
        $senderAlias = $senderAlias ? $senderAlias . ": " : "";

        //config
        // API access key from Google API's Console
        define("API_ACCESS_KEY", "AIzaSyC2fX0_SQ20jY7Ov0HeS7P3xFuYqFCvX8E");
        $registrationIds = $deviceToken;
        // prep the bundle
        $msg = array
        (
            'message' => $senderAlias . $content,
            'title' => 'AtYorkU',
            'badge' => (int)$badge,
            'type' => $msgType,
            'typeId' => $msgTypeId,
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
        );
        $fields = array
        (
            'registration_ids' => array($registrationIds),
            'data' => $msg
        );

        $headers = array
        (
            'Authorization:key=' . API_ACCESS_KEY,
            'Content-Type:application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        //echo $result;
        curl_close($ch);
        $arr = json_decode($result);
        return $arr->success==1?true:false;
    }

    /**
     * @return array
     *
     * 返回字段
     * id
     * sender_id
     * receiver_id
     * content
     * type
     * type_id
     * time
     * alert
     * sender_alias
     * receiver_alias
     */
    public function getListOfMsg($receiverId = false) {
        $table = 'msg';
        $condition = "";
        if ($receiverId) {
            $condition = " WHERE receiver_id='{$receiverId}' ";
        }
        $sql = "SELECT msg.*,user.alias AS receiver_alias FROM (SELECT msg.*,user.alias AS sender_alias FROM (SELECT * FROM msg {$condition}) AS msg INNER JOIN user ON msg.sender_id = user.id) AS msg INNER JOIN user ON msg.receiver_id = user.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM msg {$condition}";
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

    public function getListOfAlert() {
        $table = 'msg';
        $sql = "SELECT M.*,user.img,user.alias,user.gender FROM (SELECT * FROM {$table} WHERE alert = 1) AS M INNER JOIN user ON sender_id = user.id ORDER BY id DESC";
        $countSql = "SELECT COUNT(*) FROM {$table} WHERE alert = 1 ORDER BY id DESC";
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


}


?>
