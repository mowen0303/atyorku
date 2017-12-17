<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$guideModel = new \admin\guide\GuideModel();
$guide_id = BasicTool::get('guide_id');
$guideModel-> increaseCountNumber($guide_id);
$arr = $guideModel ->getRowOfGuideById($guide_id);
//wechat component
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/wechat/jssdk.php";
$jssdk = new JSSDK();
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta charset="UTF-8">
    <title><?php echo $arr['title'] ?></title>
    <link href="/apps/templete/article/css.css?1215" rel="stylesheet" type="text/css">
    <style type="text/css">
        #coverImgBox { position: absolute; width:1px; height: 1px; top: -200px; left: -200px; }
        #container {padding-bottom: 6em}
        #downloadBox { background: #eee;text-align: center; padding: 1em 0.5em}
        #downloadBox { margin: 25px; border-radius: 6px}
        #downloadBox .lo { width: 70px; height: 70px}
        #downloadBox a { }
        .dbox img { border-radius: 6px}
        /*appbox*/
        #appBox { width: 100%; height: 60px; background: rgba(0,0,0,0.92); color: #ccc}
        #appBox table { padding: 0; margin:0; width: 100%}
        #appBox .t1 { width: 50px; padding: 10px 0 0 10px}
        #appBox .t1 img { width: 43px; height: 43px; border-radius: 6px;}
        #appBox .t2 {line-height: 1.5em; font-size: 12px}
        #appBox .t2 span {font-weight: bold;}
        #appBox .t3 { text-align: right; padding-right: 10px; padding-top: 5px}
        #appBox .t3 a { font-size: 14px; border:1px solid #ccc; border-radius: 4px; padding: 6px 5px;  color: #ccc; text-decoration: none}
        #appBox.fix {  position: fixed; bottom: 0; left: 0;}
    </style>
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript">
    $(function(){

        //----------------fold 折叠------------------------------[start]-------------------
        function fold(obj){
            if(obj.parent(".foldBox").hasClass("foldBoxShow")){
                obj.parent(".foldBox").removeClass("foldBoxShow");
            }else{
                obj.parent(".foldBox").addClass('foldBoxShow');
            }
        }

        $(".foldBox").each(function(){
            if($(this).height() >= parseInt($(this).css("height"))){
                var $foldBtn = $('<div class="foldBtn"><div class="show">显示全部<svg viewBox="0 0 10 6""><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div><div class="hide">收起<svg viewBox="0 0 10 6"><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div></div>');
                $foldBtn.click(function(){fold($foldBtn)});
                $(this).append($foldBtn);
            }
        })
        //----------------fold 折叠------------------------------[end]start----------------

        //----------------WeChat Share------------------------------[start]-------------------
        var shareData = {
            title:'<?php echo $arr['title'];?>',
            link:window.location.href,
            imgUrl:'http://www.atyorku.ca<?php echo $arr['cover'];?>',
            desc:'<?php echo $arr['introduction'];?>'
        }

        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: <?php echo $signPackage["timestamp"];?>,
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                // 所有要调用的 API 都要加到这个列表中
                'checkJsApi',
                'openLocation',
                'getLocation',
                'onMenuShareTimeline',
                'onMenuShareAppMessage'
            ]
        });
        wx.ready(function() {
            timeLine();
            message();
        });

        function timeLine(){
            wx.onMenuShareTimeline({
                title: shareData.title,
                link: shareData.link,
                imgUrl: shareData.imgUrl,
                trigger: function (res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    // alert('用户点击分享到朋友圈');
                },
                success: function (res) {
                    // alert('已分享');
                },
                cancel: function (res) {
                    // alert('已取消');
                },
                fail: function (res) {
                    // alert(JSON.stringify(res));
                }
            });
        }
        function message(){
            wx.onMenuShareAppMessage({
                title: shareData.title,
                desc: shareData.desc,
                link: shareData.link,
                imgUrl: shareData.imgUrl,
                trigger: function (res) {
                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                    //alert('用户点击发送给朋友');
                },
                success: function (res) {
                    //alert('已分享');
                },
                cancel: function (res) {
                    // alert('已取消');
                },
                fail: function (res) {
                    // alert(JSON.stringify(res));
                }
            });
        }

        //----------------WeChat Share------------------------------[end]-------------------
    })


    </script>
</head>
<body>
<div style="display:none"><img src="http://www.atyorku.ca/resource/img/logo.jpg"></div>
<article id="container">
    <h1><?php echo $arr['title'] ?></h1>
    <hr>
    <div class="authorBox">
        <div id="authorHead" class="authorHead clickAuthor" style="background-image: url(<?php echo $arr['img'] ?>)"></div>
        <address>文章作者：<span class="author clickAuthor"><?php echo $arr['alias']; ?></span><br>最后更新：<data><?php echo BasicTool::translateTime($arr['time'])?></data></address>
    </div>
    <hr>
    <section class="context">
        <?php echo $arr['content'];?>
    </section>
    <hr>
    <p class="viewBox"><span>浏览量（<?php echo $arr['view_no']; ?>）</span></p>
</article>
<div id="appBox" class="fix">
    <table>
        <tr>
            <td class="t1"><img src="/resource/img/icon.png"></td>
            <td class="t2"><span>AtYorkU (约克大学专属APP)</span><br>选课攻略·课程评价·校内论坛</td>
            <td class="t3"><a href="http://www.atyorku.ca/download.html">下载 App</a> </td>
            <!--https://itunes.apple.com/us/app/atyorku/id1137850622?l=zh&ls=1&mt=8-->
        </tr>
    </table>
</div>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_footer.html";
?>
