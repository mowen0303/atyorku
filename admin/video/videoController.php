<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoSectionModel = new \admin\videoSection\VideoSectionModel();
$videoModel = new \admin\video\VideoModel();
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$videoValidator = new \admin\video\VideoValidator();
$institutionModel = new \admin\institution\InstitutionModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/aliyun-php-sdk/aliyun-php-sdk-core/Config.php';
use vod\Request\V20170321 as vod;
use Sts\Request\V20150401 as sts;
use Mts\Request\V20140618 as Mts;


call_user_func(BasicTool::get('action'));



/**=============**/
/**     API     **/
/**=============**/


/**
 * JSON -  获取一页视频列表 (GET)
 * http://www.atyorku.ca/admin/video/videoController.php?action=getListOfVideoWithJson
 */
function getListOfVideoWithJson() {
    global $videoModel;
    try {
        $reviewStatus = BasicTool::get("review_status");
        $albumId = intval(BasicTool::get("album_id"));
        $sectionId = intval(BasicTool::get("section_id"));

        $result = $videoModel->getListOfVideoByConditions($albumId, $sectionId, $reviewStatus);

        if ($result) {
            BasicTool::echoJson(1, "成功", $result);
        } else {
            BasicTool::echoJson(0, "获取视频专辑类别列表失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * JSON - 添加|修改一个视频 (POST)
 * http://www.atyorku.ca/admin/video/videoController.php?action=modifyVideoWithJson
 */
function modifyVideoWithJson() {
    modifyVideo("json");
}

/**
 * JSON - 删除一个或多个视频 (POST)
 * http://www.atyorku.ca/admin/video/videoController.php?action=deleteVideoWithJson
 */
function deleteVideoWithJson() {
    deleteVideo("json");
}


/**
 * http://www.atyorku.ca/admin/video/videoController.php?action=purchaseVideoWithJson
 * 购买一个视频
 *
 * [POST] int|string id 视频ID
 */
function purchaseVideoWithJson() {
    purchaseVideo("json");
}


/**
 * http://www.atyorku.ca/admin/video/videoController.php?action=updateReviewStatusWithJson
 * 审核一个视频
 *
 * [POST]
 *
 * int id 视频ID
 * int review_status 视频审核状态
 *
 */
function updateReviewStatusWithJson() {
    updateReviewStatus("json");
}

/**=============**/
/**   methods   **/
/**=============**/


/**
 * 添加|修改一个视频
 * @param string $echoType
 */
function modifyVideo($echoType='normal') {
    global $videoModel;
    global $videoAlbumModel;
    global $videoSectionModel;
    global $videoValidator;
    global $imageModel;
    global $currentUser;

    try{
        $flag = BasicTool::post('flag');
        $currentVideo = null;
        $sort = null;

        $videoAlbumId = BasicTool::post('album_id', '视频专辑ID不能为空');
        $videoSectionId = BasicTool::post('section_id', '视频章节ID不能为空');
        $videoSection = $videoSectionModel->getVideoSectionById($videoSectionId) or BasicTool::throwException("视频章节不存在");
        $videoAlbum = $videoAlbumModel->getRawAlbumById($videoAlbumId) or BasicTool::throwException("视频专辑不存在");
        intval($videoSection['album_id']) === intval($videoAlbum['id']) or BasicTool::throwException("视频专辑与章节不匹配");
        $videoUserId = $videoAlbum['user_id'];

        // validate video id if it's update
        if ($flag=='update') {
            $id = intval(BasicTool::post("id", "视频ID不能为空"));
            $currentVideo = $videoModel->getVideoById($id) or BasicTool::throwException("视频不存在");
            $sort = $currentVideo['sort'];
        }

        // 验证权限
        checkAuthority($flag, $videoUserId);

        // 验证fields
        $title = $videoValidator::validateTitle(BasicTool::post("title"));
        $description = $videoValidator::validateDescription(BasicTool::post("description"));
        $url = $videoValidator::validateUrl(BasicTool::post("url"));
        $size = BasicTool::post('size', '视频大小不能为空');
        $length = BasicTool::post('length', '视频时长不能为空');
        $instructorId = $videoValidator::validateInstructorId(BasicTool::post("instructor_id"));
        $price = $videoValidator::validatePrice(BasicTool::post("price"));

        $coverImgId = BasicTool::post("cover_img_id");
        $videoValidator::validateCoverImage($coverImgId, "imgFile");

        // analyze images
        $imgArr = array_filter(array($coverImgId));
        $currImgArr = ($currentVideo!=null) ? array_filter(array($currentVideo['cover_img_id'])) : false;
        $imgArr = $imageModel->uploadImagesWithExistingImages($imgArr, $currImgArr,1,"imgFile", $currentUser->userId,"video");

        // 执行
        if ($flag=='update') {
            $videoModel->updateVideo(
                $currentVideo['id'],
                $url,
                $size,
                $length,
                $videoAlbumId,
                $videoSectionId,
                $instructorId,
                $price,
                $description,
                $title,
                $imgArr[0]
            ) or BasicTool::throwException($videoModel->errorMsg);

            if ($echoType == "normal") {
                BasicTool::echoMessage("修改成功","/admin/video/index.php?s=listVideo&album_id={$currentVideo['album_id']}&id={$currentVideo['section_id']}");
            } else {
                BasicTool::echoJson(1, "修改成功");
            }
        } else if ($flag=='add') {
            $videoModel->addVideo(
                $url,
                $size,
                $length,
                $videoAlbumId,
                $videoSectionId,
                $instructorId,
                $price,
                $description,
                $title,
                $imgArr[0]
            ) or BasicTool::throwException($videoModel->errorMsg);
            if ($echoType == "normal") {
                BasicTool::echoMessage("添加成功","/admin/video/index.php?s=listVideo&album_id={$currentVideo['album_id']}&id={$currentVideo['section_id']}");
            } else {
                BasicTool::echoJson(1, "添加成功");
            }
        }
    }
    catch (Exception $e){
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}

/**
 * 删除1个或多个视频
 * @param string $echoType
 *
 * [POST] int|string|array id 被指定删除的单个或多个视频id
 */
function deleteVideo($echoType="normal") {
    global $videoModel;
    global $imageModel;
    try {
        $id = intval(BasicTool::post('id')) or BasicTool::throwException("请指定被删除视频ID");
        $i = 0;
        if (is_array($id)) {
            // multiple ids
            foreach ($id as $v) {
                $i++;
                $video = $videoModel->getVideoById($v) or BasicTool::throwException("没找到视频");
                checkAuthority('delete', $video['user_id']);
                !$video['cover_img_id'] or $imageModel->deleteImageById($video['cover_img_id']);
                $videoModel->deleteVideoById($v);
            }
        } else {
            // single id
            $i++;
            $video = $videoModel->getVideoById($id) or BasicTool::throwException("没找到视频");
            checkAuthority('delete', $video['user_id']);
            !$video['cover_img_id'] or $imageModel->deleteImageById($video['cover_img_id']);
            $videoModel->deleteVideoById($id);
        }
        if ($echoType == "normal") {
            BasicTool::echoMessage("成功删除{$i}个视频", $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(1, "成功删除{$i}个视频");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


function purchaseVideo($echoType="normal") {
    global $videoModel;
    global $currentUser;
    global $transactionModel;
    global $institutionModel;
    global $msgModel;
    try {
        $productTransactionModel = new \admin\productTransaction\ProductTransactionModel('video');
        $videoId = intval(BasicTool::post('id','请指定视频ID'));
        $buyerId = $currentUser->userId;
        $buyerId or BasicTool::throwException("请先登录");

        $result = $videoModel->getVideoById($videoId);

        if ($result) {
            if (intval($result['is_deleted']) || intval($result['review_status']) !== 1) {
                BasicTool::throwException("购买失败: 视频不存在");
            }
            $id = $result['id'];
            $title = $result['title'];

            // 检测购买者ID
            $sellerId = intval($result["user_id"]);
            $sellerId !== intval($buyerId) or BasicTool::throwException("无法购买自己的视频");

            // 检测购买者积分
            $price = floatval($result["price"]);
            $transactionModel->isCreditDeductible($buyerId, $price) || BasicTool::throwException($transactionModel->errorMsg);

            // 获取过期时间
            $expirationDate = $institutionModel->getCurrentTermEndingDate($result['term_end_dates']);

            // 创建交易
            $buyerDescription = "购买视频: " . $title . " ID: " . $id;
            $sellerDescription = "售出视频: " . $title . " ID: " . $id;
            $result = $productTransactionModel->buy($buyerId, $sellerId, $price, $buyerDescription, $sellerDescription, $id, $expirationDate) or BasicTool::throwException($productTransactionModel->errorMsg);
            $msgModel->pushMsgToUser($buyerId, 'video', $id, $title.": 购买成功");
            $msgModel->pushMsgToUser($sellerId, 'video', $id, "我花了[{$price}]点积分,购买了你的视频[{$title}]",$buyerId);
            BasicTool::echoJson(1, "购买成功", $result);
        } else {
            BasicTool::throwException("购买失败: 视频不存在");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 更新一个视频的审核状态
 * @param string $echoType
 *
 * [POST] int id
 * [POST] int review_status
 */
function updateReviewStatus($echoType="normal") {
    global $videoModel;
    global $currentUser;
    try {
        $id = intval(BasicTool::post("id", "视频ID不能为空"));
        $status = intval(BasicTool::post("review_status", "审核状态不能为空"));

        $currentVideo = $videoModel->getVideoById($id) or BasicTool::throwException("视频不存在");
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");

        $videoModel->updateReviewStatusById($id, $status) or BasicTool::throwException($videoModel->errorMsg);

        if ($echoType == "normal") {
            BasicTool::echoMessage("审核修改成功","/admin/video/index.php?s=listVideo&album_id={$currentVideo['album_id']}&id={$currentVideo['section_id']}");
        } else {
            BasicTool::echoJson(1, "审核修改成功");
        }
    } catch (Exception $e) {
        if ($echoType == "normal") {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        } else {
            BasicTool::echoJson(0, $e->getMessage());
        }
    }
}


/**
 * 检测权限
 * @param string flag 'add' | 'update' | 'delete'
 * @param int $id current video user id / for 'add_many' it's the section_id
 * @param int $op 'add_many' 的 review_status
 * @throws Exception
 */
function checkAuthority($flag, $id, $op=null) {
    // TODO:
    global $currentUser;
    if ($flag === 'get') {
        // TODO: 购买权限验证
    } else if ($flag === 'get_many') {
        // TODO: 获取列表权限验证
    } else if ($flag === 'add' || $flag === 'update' || $flag === 'delete') {
        $currentUser->isUserHasAuthority("ADMIN") or BasicTool::throwException("权限不足");
    }
//    else if ($flag === 'update' || $flag === 'delete') {
//        $id or BasicTool::throwException("Modified video album user id is required.");
//        $currentUser->userId or BasicTool::throwException("权限不足，请先登录");
//        if (!($currentUser->isUserHasAuthority('ADMIN') && $currentUser->isUserHasAuthority('VIDEO'))) {
//            $currentUser->userId == $id or BasicTool::throwException("无权修改其他人的视频");
//        }
//    }
    else {
        BasicTool::throwException("权限不足");
    }
}

/**=============**/
/**   Ali VOD   **/
/**=============**/



function getVideoPlayAuthWithJson() {
    try {
        getVideoPlayAuth('json');
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

/**
 * Retrieve one video play information
 * @param string $echoType
 */
function getVideoPlayAuth($echoType='normal') {
    global $videoModel;
    global $currentUser;
    try {
        $vid = BasicTool::get('vid') or BasicTool::throwException('视频ID不能为空');
        $currentUser->userId or BasicTool::throwException('请先登录');

        // check authorization
        $currentUser->isUserHasAuthority("ADMIN") or $videoModel->checkAuthentication($vid, $currentUser->userId);

        $client = _initVodClient();
        $playAuth = _getPlayAuth($client, $vid);
        if ($echoType == 'json') {
            if ($playAuth) {
                BasicTool::echoJson(1, "成功", $playAuth);
            } else {
                BasicTool::echoJson(0, "获取 playAuth 失败");
            }
        } else {
            if ($playAuth) {
                BasicTool::echoMessage($playAuth);
            } else {
                BasicTool::echoMessage("获取 playAuth 失败");
            }
        }
    } catch (Exception $e) {
        if ($echoType == 'json') {
            BasicTool::echoJson(0, $e->getMessage());
        } else {
            BasicTool::echoMessage($e->getMessage(), $_SERVER['HTTP_REFERER']);
        }
    }
}

/**
 * init VOD client
 * @return DefaultAcsClient
 */
function _initVodClient() {
    $accessKeyId = "LTAIh2nVinWvn1T5";
    $accessKeySecret = "2OmNCMI2M7CnsUvD8lYb7MkJ5rpMWf";
    $regionId = 'cn-shanghai';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}

function _initMtsClient() {
    $accessKeyId = "LTAIIwQ9XuZ4abJq";
    $accessKeySecret = "u9RUQTZWkXZg603NUgQTDBt4yuLG8s";
    $regionId = 'cn-hangzhou';  // 点播服务所在的Region，国内请填cn-shanghai，不要填写别的区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}

function _getPlayAuth($client, $vid) {
    $request = new vod\GetVideoPlayAuthRequest();
    $request->setAcceptFormat('JSON');
    $request->setVideoId($vid);
    try {
        $response = $client->getAcsResponse($request);
        return $response;
    } catch(ServerException $e) {
        print "Error: " . $e->getCode() . " Message: " . $e->getMessage() . "\n";
    } catch(ClientException $e) {
        print "Error: " . $e->getCode() . " Message: " . $e->getMessage() . "\n";
    }
}

function getPlayCredentialsWithJson() {
    $vid = BasicTool::get('vid', '媒体ID不能为空');
    try {
        $response = _getMtsPlayCredentials($vid);
        if ($response) {
            $authInfo = _getAuthInfo($vid);
            BasicTool::echoJson(1, "成功", $response->Credentials, $authInfo);
        } else {
            BasicTool::echoJson(0, "播放权限获取失败");
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
}

function _getMtsPlayCredentials($vid) {
    $client = _initMtsClient();
    $arn = "acs:ram::1489533743474643:role/mtsplay";
    $response = _assumeRole($client, $arn);
    return $response;
}

function _getAuthInfo($vid) {
    date_default_timezone_set('UTC');
    $key = "atyorkutest";
    $expiration = str_replace('+00:00', 'Z', gmdate('c', strtotime('+ 1 hour')));
    $encodedExpiration = urlencode($expiration);
    $encodedVid = urlencode($vid);
    $str = "ExpireTime={$encodedExpiration}&MediaId={$encodedVid}";
    $signature = base64_encode(hash_hmac('sha1', $str, $key, true));
    $authInfo = [
        "ExpireTime" => $expiration,
        "MediaId" => $vid,
        "Signature" => $signature
    ];
    return json_encode($authInfo);
}

function _assumeRole($client, $roleArn) {
    $request = new sts\AssumeRoleRequest();
    $request->setVersion("2015-04-01");
    $request->setProtocol("https");
    $request->setMethod("POST");
    $request->setDurationSeconds(900);
    $request->setRoleArn($roleArn);
    $request->setRoleSessionName("test-token");
    return $client->getAcsResponse($request);
}
?>
