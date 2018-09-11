<?php
namespace admin\aliyunPhpSdk;   //-- 注意 --//

// 取原有的加载方法
$oldFunctions = spl_autoload_functions();

// 逐个卸载
if ($oldFunctions){
    foreach ($oldFunctions as $f) {
        spl_autoload_unregister($f);
    }
}
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/aliyunPhpSdk/aliyun-php-sdk-core/Config.php';
// 如果引用本框架的其它框架已经定义了__autoload,要保持其使用
if (function_exists('__autoload')) {
    spl_autoload_register('__autoload');
}

// 再将原来的自动加载函数放回去
if ($oldFunctions){
    foreach ($oldFunctions as $f) {
        spl_autoload_register($f);
    }
}

use vod\Request\V20170321 as vod;
//use Sts\Request\V20150401 as sts;
//use Mts\Request\V20140618 as Mts;
use DefaultProfile;
use DefaultAcsClient;
//use ServerException;
//use ClientException;
use \Model as Model;
//use \BasicTool as BasicTool;
use \Exception as Exception;

class AliyunModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get a video playAuth by vid
     * @param $vid
     * @return mixed
     */
    function getVideoPlayAuth($vid) {
        $client = $this->_initVodClient();
        return $this->_getPlayAuth($client, $vid);
    }

//    function getPlayInfoWithJson() {
//        global $videoModel;
//        global $currentUser;
//        try {
//            $vid = BasicTool::get('vid') or BasicTool::throwException('视频ID不能为空');
//            $currentUser->userId or BasicTool::throwException('请先登录');
//
//            // check authorization
//                if (!$currentUser->isUserHasAuthority("ADMIN") && !$videoModel->checkAuthentication($vid, $currentUser->userId)) {
//                    BasicTool::throwException("请先购买");
//                }
//
//            $client = _initVodClient();
//            $playAuth = _getPlayAuth($client, $vid);
//            if ($playAuth) {
//                $request = new Mts\PlayInfoRequest();
//                $request->setMediaId($vid);
//                $request->setHlsUriToken($playAuth);
//                $response = $client->getAcsResponse($request);
//                if ($response) {
//                    BasicTool::echoJson(1, "成功", $response);
//                } else {
//                    BasicTool::echoJson(0, "获取 playInfo 失败");
//                }
//            } else {
//                BasicTool::echoJson(0, "获取 playAuth 失败");
//            }
//        } catch (Exception $e) {
//            BasicTool::echoJson(0, $e->getMessage());
//        }
//    }


    /**
     * init VOD client
     * @return DefaultAcsClient
     */
    private function _initVodClient() {
        $accessKeyId = "LTAIh2nVinWvn1T5";
        $accessKeySecret = "2OmNCMI2M7CnsUvD8lYb7MkJ5rpMWf";
        $regionId = 'cn-shanghai';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
        $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
        return new DefaultAcsClient($profile);
    }

//    function _initMtsClient() {
//        $accessKeyId = "LTAIIwQ9XuZ4abJq";
//        $accessKeySecret = "u9RUQTZWkXZg603NUgQTDBt4yuLG8s";
//        $regionId = 'cn-hangzhou';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
//        $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
//        return new DefaultAcsClient($profile);
//    }

    private function _getPlayAuth($client, $vid) {
        $request = new vod\GetVideoPlayAuthRequest();
        $request->setAcceptFormat('JSON');
        $request->setVideoId($vid);
        $request->setAuthInfoTimeout(3600);
        try {
            $response = $client->getAcsResponse($request);
            return $response;
        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }
//        } catch(ServerException $e) {
//            print "Error: " . $e->getCode() . " Message: " . $e->getMessage() . "\n";
//        } catch(ClientException $e) {
//            print "Error: " . $e->getCode() . " Message: " . $e->getMessage() . "\n";
//        }
    }

//    function getPlayCredentialsWithJson() {
//        $vid = BasicTool::get('vid', '媒体ID不能为空');
//        try {
//            $response = _getMtsPlayCredentials($vid);
//            if ($response) {
//                $authInfo = _getAuthInfo($vid);
//                BasicTool::echoJson(1, "成功", $response->Credentials, $authInfo);
//            } else {
//                BasicTool::echoJson(0, "播放权限获取失败");
//            }
//        } catch (Exception $e) {
//            BasicTool::echoJson(0, $e->getMessage());
//        }
//    }
//
//    function _getMtsPlayCredentials($vid) {
//        $client = _initVodClient();
//        $arn = "acs:ram::1489533743474643:role/mtsplay";
//        $response = _assumeRole($client, $arn);
//        return $response;
//    }
//
//    function _getAuthInfo($vid) {
//        date_default_timezone_set('UTC');
//        $key = "atyorkutest";
//        $expiration = str_replace('+00:00', 'Z', gmdate('c', strtotime('+ 1 hour')));
//        $encodedExpiration = urlencode($expiration);
//        $encodedVid = urlencode($vid);
//        $str = "ExpireTime={$encodedExpiration}&MediaId={$encodedVid}";
//        $signature = base64_encode(hash_hmac('sha1', $str, $key, true));
//        $authInfo = [
//            "ExpireTime" => $expiration,
//            "MediaId" => $vid,
//            "Signature" => $signature
//        ];
//        return json_encode($authInfo);
//    }

//    function _assumeRole($client, $roleArn) {
//        $request = new sts\AssumeRoleRequest();
//        $request->setVersion("2015-04-01");
//        $request->setProtocol("https");
//        $request->setMethod("POST");
//        $request->setDurationSeconds(900);
//        $request->setRoleArn($roleArn);
//        $request->setRoleSessionName("test-token");
//        return $client->getAcsResponse($request);
//    }

}
