<?php
namespace admin\aws;   //-- 注意 --//

//// 取原有的加载方法
//$oldFunctions = spl_autoload_functions();
//// 逐个卸载
//if ($oldFunctions){
//    foreach ($oldFunctions as $f) {
//        spl_autoload_unregister($f);
//    }
//}
require $_SERVER['DOCUMENT_ROOT'] . '/admin/aws/aws-autoloader.php';
//// 如果引用本框架的其它框架已经定义了__autoload,要保持其使用
//if (function_exists('__autoload')) {
//    spl_autoload_register('__autoload');
//}
//// 再将原来的自动加载函数放回去
//if ($oldFunctions){
//    foreach ($oldFunctions as $f) {
//        spl_autoload_register($f);
//    }
//}


use \Model as Model;
//use \BasicTool as BasicTool;
use \Exception as Exception;
use \Datetime;

use Aws\Credentials\CredentialProvider;
use Aws\S3\S3Client;
use Aws\Sdk as Sdk;

use admin\aws\Aws\CloudFront as CloudFront;

class S3Buckets {
    public static $TRANSCODED_BUCKET = "pocket.school.video.trans";
    public static $THUMBNAIL_BUCKET = "pocket.school.video.thumb";
    public static $VIDEO_BUCKET = "pocket.school.video";
};

class AwsModel extends Model
{

    public function __construct()
    {
        parent::__construct();
        $sharedConfig = [
            'profile' => 'default',
            'region'  => 'us-east-1',
            'version' => 'latest'
        ];
        // Create an SDK class used to share configuration across clients.
        $sdk = new Sdk($sharedConfig);
//        $this->s3Client = $sdk->createS3();
        $this->cloudFront = $sdk->createCloudFront();
    }

//    public function getListOfBuckets() {
//        //Listing all S3 Bucket
//        $CompleteSynchronously = $this->s3Client->listBucketsAsync();
//        // Block until the result is ready.
//        $CompleteSynchronously = $CompleteSynchronously->wait();
////        return
//    }


    /**
     * Initiate signed cookie from Cloud Front to be able to get S3 Objects
     * @param $resourceKey
     * @param int $expiresInSec
     * @param string $domain
     */
    public function initSignedCookieFromCloudFront($resourceKey, $expiresInSec=3600, $domain='.zaiyueke.ca') {
        $privateKeyPath = $_SERVER["DOCUMENT_ROOT"] . '/commonClass/pk-APKAJZODMBTGOXUSUZDQ.pem';
        $cookies = $this->cloudFront->getSignedCookie([
            'policy'      => $this->getCloudFrontCustomPolicy($resourceKey, $expiresInSec),
            'private_key' => $privateKeyPath,
            'key_pair_id' => 'APKAJZODMBTGOXUSUZDQ'
        ]);

        foreach ($cookies as $k => $v) {
            setcookie($k, $v, time() + $expiresInSec, '/', $domain);
        }
    }

    /**
     * Construct a CloudFront Custom Policy
     * @param $resourceKey
     * @param $expiresInSec
     * @return string
     */
    private function getCloudFrontCustomPolicy($resourceKey, $expiresInSec) {
        $expires = time() + $expiresInSec;
        $customPolicy = <<<POLICY
{
    "Statement": [
        {
            "Resource": "{$resourceKey}",
            "Condition": {
                "DateLessThan": {"AWS:EpochTime": {$expires}}
            }
        }
    ]
}
POLICY;
        return $customPolicy;
    }




//    /**
//     * Retrieve a signed URL to access S3 object(s)
//     * @param $resourceKey
//     * @param int $expiresInSec
//     * @return string
//     */
//    private function getSignedUrlFromCloudFront($resourceKey, $expiresInSec=3600) {
//        $privateKeyPath = $_SERVER["DOCUMENT_ROOT"] . '/commonClass/pk-APKAJZODMBTGOXUSUZDQ.pem';
//        return $this->cloudFront->getSignedUrl([
//            'policy'      => $this->getCloudFrontCustomPolicy($resourceKey, $expiresInSec),
//            'private_key' => $privateKeyPath,
//            'key_pair_id' => 'APKAJZODMBTGOXUSUZDQ'
//        ]);
//    }



//    public function getS3ObjectUrl($bucket, $key, $expiration="+10 minutes") {
//        //Creating a presigned URL
//        $cmd = $this->s3Client->getCommand('GetObject', [
//            'Bucket' => $bucket,
//            'Key'    => $key
//        ]);
//
//        $request = $this->s3Client->createPresignedRequest($cmd, $expiration);
//
//        // Get the actual presigned-url
//        $presignedUrl = (string) $request->getUri();
//
//        echo $presignedUrl;
//    }
//
//    public function downloadPlaylistByKey($key) {
//        $result = $this->s3Client->getObject(array(
//            'Bucket' => $this->transBucket,
//            'Key'    => $key
//        ));
//        echo $result['Body'] . "\n";
//    }
//
//    public function getTest() {
//        $result = $this->s3Client->getObject([
//            'Bucket' => 'pocket.school.video',
//            'Key'    => 'my-key'
//        ]);
//
//    }
//
//    function getClient() {
//        // Use the default credential provider
//        $provider = CredentialProvider::defaultProvider();
//
//        // Pass the provider to the client
//        $client = new S3Client([
//            'region'      => 'us-east-1',
//            'version'     => 'latest',
//            'credentials' => $provider
//        ]);
//    }


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
