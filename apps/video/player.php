<?php

require_once $_SERVER["DOCUMENT_ROOT"]."/commonClass/config.php";
$currentUser = new \admin\user\UserModel();
$vid = BasicTool::get("vid") ?: "";
header("Access-Control-Allow-Origin: *");

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, height=300px, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"/>
    <title>测试视频播放器</title>
    <link href="https://vjs.zencdn.net/7.1.0/video-js.css" rel="stylesheet">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
<!--    <script src="/admin/resource/js/main.js" type="text/javascript"></script>-->
</head>
<body style="margin: 0; padding: 0; background-color: #00a0e9;">
<script src="https://vjs.zencdn.net/7.1.0/video.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.14.1/videojs-contrib-hls.min.js"></script>
<video id="video-player" crossOrigin="anonymous" class="video-js vjs-default-skin vjs-big-play-centered" style="position: relative; width: 100%; height: 300px; background-color: #333;" controls data-setup="{}" playsinline>
</video>
<script>
    const vid = '<?php echo $vid; ?>';
    const player = videojs('video-player',
        {
            autoHeight: false,
            // html5: {
            //     nativeAudioTracks: false,
            //     nativeVideoTracks: false,
            //     hls: {
            //         debug: true,
            //         overrideNative: true
            //     }
            // }
        }
    );
    function sendMsgToRN(message) {
        if (document.hasOwnProperty('postMessage')) {
            document.postMessage(message, '*');
        } else if (window.hasOwnProperty('postMessage')) {
            window.postMessage(message, '*');
        } else {
            console.log(message);
        }
    }

    sendMsgToRN("hello");
    $.ajax({
        url:`/admin/video/videoController.php?action=getVideoPlayAuthWithJson&vid=${vid}`,
        type:"GET",
        success: function(response) {
            const json = JSON.parse(response);
            sendMsgToRN(JSON.stringify(response));
            if (json.code === 1) {
                player.src({
                    src: `http://video.zaiyueke.ca/${vid}/main.m3u8`,
                    type: 'application/x-mpegURL',
                    // type: 'application/octet-stream',
                    withCredentials: true
                });
                sendMsgToRN("Play info: ", `http://video.zaiyueke.ca/${vid}/main.m3u8`);
                player.on('error', (e) => {
                    var error = player.error();
                    console.log('Player error: ', error);
                    sendMsgToRN(JSON.stringify({eventKey: 'playerError', message: error.message}));
                });
                // player.play();
            } else {
                console.log(json.message);
                sendMsgToRN(JSON.stringify({eventKey: 'error', message: json.message}));
            }
        },
        error: function(jqXHR, status, err) {
            console.log(err);
            sendMsgToRN(JSON.stringify({eventKey: 'error', message: err}))
        }
    });
</script>
</body>
</html>