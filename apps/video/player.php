<?php

require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();

if(!$currentUser->isLogin()){
    BasicTool::echoWapMessage("请登录AtYorkU账号");
    die();
}

$vid = BasicTool::get("vid") ?: "";

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport"   content="width=device-width, height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>测试视频播放器</title>
    <link rel="stylesheet" href="//g.alicdn.com/de/prismplayer/2.7.1/skins/default/aliplayer-min.css" />
    <script type="text/javascript" src="//g.alicdn.com/de/prismplayer/2.7.1/aliplayer-min.js"></script>
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="/admin/resource/js/main.js" type="text/javascript"></script>
</head>
<body style="margin: 0;">
<div class="prism-player" id="J_prismPlayer" style="position: absolute; background-color: #2b2d2f;"></div>
<script>
    const vid = '<?php echo $vid; ?>';
    <?php
    // $.ajax({
    //     url: `/admin/video/videoController.php?action=getPlayCredentialsWithJson&vid=${vid}`,
    //     type: 'GET',
    //     success: function(json) {
    //         json = JSON.parse(json);
    //         if (json.code === 1) {
    //             const {AccessKeyId, AccessKeySecret, SecurityToken, Expiration} = json.result;
    //             // const authInfo = json.secondResult;
    //
    //             let player = new Aliplayer({
    //                 id: 'J_prismPlayer',
    //                 width: '100%',
    //                 autoplay: false,
    //                 playsinline: true,
    //                 useFlashPrism: true,
    //                 // format: 'm3u8',
    //                 vid: vid,
    //                 accId: AccessKeyId,
    //                 accSecret: AccessKeySecret,
    //                 stsToken: SecurityToken,
    //                 // domainRegion: 'cn-hangzhou',
    //                 // authInfo: authInfo,
    //             }, function(player){
    //                 console.log('播放器创建好了。');
    //             });
    //         }
    //     },
    //     error: function(jqXHR, status, err) {
    //         $('#J_prismPlayer').replaceWith(`<h2>${err}</h2>`);
    //     }
    // });
    ?>

    $.ajax({
        url:`/admin/video/videoController.php?action=getVideoPlayAuthWithJson&vid=${vid}`,
        type:"GET",
        success: function(json) {
            try {
                json = JSON.parse(json);
                if (json.code === 1) {
                    const {VideoMeta:{CoverURL, VideoId}, PlayAuth} = json.result;
                    let player = new Aliplayer({
                        id: 'J_prismPlayer',
                        width: '100%',
                        autoplay: false,
                        playsinline: true,
                        vid: VideoId,
                        playauth: PlayAuth,
                        cover: CoverURL,
                        format: 'm3u8',
                        // controlBarVisibility:'hover',
                        // useFlashPrism: true,
                    }, function(player) {
                        const data = {event: "player_built", vid: vid};
                        window.postMessage(JSON.stringify(data));
                    });
                    player.on('ready', (e) => {
                        const data = {event: "player_ready"};
                        window.postMessage(JSON.stringify(data));
                    });
                    player.on('play', (e) => {
                        const data = {event: "player_play"};
                        window.postMessage(JSON.stringify(data));
                    });
                    player.on('pause', (e) => {
                        const data = {event: "player_pause"};
                        window.postMessage(JSON.stringify(data));
                    });
                    player.on('ended', (e) => {
                        const data = {event: "player_ended"};
                        window.postMessage(JSON.stringify(data));
                    });
                } else {
                    const data = {event: "error", message: json.message};
                    window.postMessage(JSON.stringify(data));
                    // $('#J_prismPlayer').replaceWith(`<div />`);
                }
            } catch (error) {
                console.log(error);
            }
        },
        error: function(jqXHR, status, err) {
            const data = {event: "error", message: err};
            window.postMessage(JSON.stringify(data));
            // $('#J_prismPlayer').replaceWith(`<h2>${err}</h2>`);
        }
    });

</script>
</body>
</html>