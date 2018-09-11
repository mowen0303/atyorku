<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$videoSectionModel = new \admin\videoSection\VideoSectionModel();
$videoModel = new \admin\video\VideoModel();
$videoAlbumModel = new \admin\videoAlbum\VideoAlbumModel();
$videoValidator = new \admin\video\VideoValidator();
$institutionModel = new \admin\institution\InstitutionModel();
$imageModel = new \admin\image\ImageModel();
$currentUser = new \admin\user\UserModel();
$aliyunModel = new \admin\aliyunPhpSdk\AliyunModel();
$transactionModel = new \admin\transaction\TransactionModel();
$msgModel = new \admin\msg\MsgModel();

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
    global $currentUser;
    try {
        $albumId = intval(BasicTool::get("album_id"));
        $sectionId = intval(BasicTool::get("section_id"));

        $result = $videoModel->getListOfVideoByConditions($albumId, $sectionId, 1);

        if ($result) {
            $purchasedTransactions = [];
            if ($currentUser->userId) {
                $vids = array_column($result, 'id');
                $purchasedTransactions = $videoModel->getListOfPurchasedVideoTransaction($vids, $currentUser->userId);
            }
            BasicTool::echoJson(1, "成功", $result, $purchasedTransactions);
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

/**
 * http://www.atyorku.ca/admin/video/videoController.php?action=getVideoPlayAuthWithJson&vid=VIDEO_ID
 * 获取一个视频播放权限码
 *
 * [GET]
 *
 * string vid 视频ID
 */
function getVideoPlayAuthWithJson() {
    global $aliyunModel;
    global $currentUser;
    global $videoModel;
    try {
        $vid = BasicTool::get('vid') or BasicTool::throwException('视频ID不能为空');
        $video = $videoModel->getVideoById($vid);
        if (!$video) { BasicTool::throwException('视频不存在'); }
        $currentUser->userId or BasicTool::throwException('请先登录');

        $price = floatval($video['price']);

        // check authorization
        if (!$currentUser->isUserHasAuthority("ADMIN") && $price > 0 && !$videoModel->checkAuthentication($vid, $currentUser->userId)) {
            BasicTool::throwException("请先购买");
        }

        $playAuth = $aliyunModel->getVideoPlayAuth($video['url']);
        if ($playAuth) {
            // update count_video_view if it's not free
            if ($price > 0) {
                $ptm = new \admin\productTransaction\ProductTransactionModel('video');
                $result = $ptm->incrementVideoViewCount($currentUser->userId, $vid);
                if (!$result) {
                    BasicTool::echoJson(0, $videoModel->errorMsg);
                }
            }
            BasicTool::echoJson(1, "成功", $playAuth);
        } else {
            BasicTool::echoJson(0, $aliyunModel->errorMsg);
        }
    } catch (Exception $e) {
        BasicTool::echoJson(0, $e->getMessage());
    }
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

            // 检测是否已购买
            if ($videoModel->checkAuthentication($id, $currentUser->userId)) {
                BasicTool::throwException("您已购买此视频");
            }

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

?>
