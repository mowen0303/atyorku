<?php

require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();

if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请登录AtYorkU账号");
    die();
}

$vid = BasicTool::get("vid") ?: "";
//$playAuth = BasicTool::get("playauth") ?: "";

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport"   content="width=device-width, height=300, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>测试视频播放器</title>
    <link rel="stylesheet" href="//g.alicdn.com/de/prismplayer/2.7.1/skins/default/aliplayer-min.css" />
    <script type="text/javascript" src="//g.alicdn.com/de/prismplayer/2.7.1/aliplayer-min.js"></script>
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="/admin/resource/js/main.js" type="text/javascript"></script>
</head>
<body style="margin: 0; background-color: #2b2d2f;">
<div class="prism-player" id="J_prismPlayer" style="position: absolute;"></div>
<script>
    const vid = '<?php echo $vid; ?>';
    // $.ajax({
    //     url: `/admin/video/videoController.php?action=getPlayCredentialsWithJson&vid=${vid}`,
    //     type: 'GET',
    //     success: function(json) {
    //         json = JSON.parse(json);
    //         if (json.code === 1) {
    //             const {AccessKeyId, AccessKeySecret, SecurityToken, Expiration} = json.result;
    //             const authInfo = json.secondResult;
    //
    //             let player = new Aliplayer({
    //                 id: 'J_prismPlayer',
    //                 width: '100%',
    //                 autoplay: false,
    //                 playsinline: true,
    //                 useFlashPrism: true,
    //                 format: 'm3u8',
    //                 vid: vid,
    //                 accId: AccessKeyId,
    //                 accSecret: AccessKeySecret,
    //                 stsToken: SecurityToken,
    //                 domainRegion: 'cn-hangzhou',
    //                 authInfo: authInfo,
    //             }, function(player){
    //                 console.log('播放器创建好了。');
    //             });
    //         }
    //     },
    //     error: function(jqXHR, status, err) {
    //         $('#J_prismPlayer').replaceWith(`<h2>${err}</h2>`);
    //     }
    // });

    $.ajax({
        url:"/admin/video/videoController.php?action=getVideoPlayAuthWithJson&vid=<?php echo $vid; ?>",
        type:"GET",
        success: function(json) {
            json = JSON.parse(json);
            if (json.code === 1) {
                const {VideoMeta:{CoverURL, VideoId}, PlayAuth} = json.result;
                console.log(json.result);
                let player = new Aliplayer({
                    id: 'J_prismPlayer',
                    width: '100%',
                    autoplay: false,
                    playsinline: true,
                    vid: VideoId,
                    playauth: PlayAuth,
                    cover: CoverURL,
                }, function(player){
                    console.log('播放器创建好了。');
                });
            } else {
                $('#J_prismPlayer').replaceWith(`<h2>${json.message}</h2>`);
            }
        },
        error: function(jqXHR, status, err) {
            $('#J_prismPlayer').replaceWith(`<h2>${err}</h2>`);
        }
    });

</script>
</body>
</html>