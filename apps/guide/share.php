<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
$guideModel = new \admin\guide\GuideModel();
$guide_id = BasicTool::get('guide_id');
$guideModel->increaseCountNumber($guide_id);
$arr = $guideModel->getRowOfGuideById($guide_id);
//wechat component
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/wechat/jssdk.php";
$jssdk = new JSSDK();
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<head>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport"/>
    <meta charset="UTF-8">
    <title><?php echo $arr['title'] ?></title>
    <link href="/apps/guide/css.css?1215138" rel="stylesheet" type="text/css">
    <style type="text/css">
        #coverImgBox {
            position: absolute;
            width: 1px;
            height: 1px;
            top: -200px;
            left: -200px;
        }

        #container {
            padding-bottom: 3em
        }

        #downloadBox {
            background: #eee;
            text-align: center;
            padding: 1em 0.5em
        }

        #downloadBox {
            margin: 25px;
            border-radius: 6px
        }

        #downloadBox .lo {
            width: 70px;
            height: 70px
        }

        #downloadBox a {
        }

        .dbox img {
            border-radius: 6px
        }

        /*appbox*/
        #appBox {
            width: 100%;
            height: 60px;
            background: rgba(0, 0, 0, 0.92);
            color: #ccc;
            display:flex;
            justify-content: space-between;
            align-items: center;
        }
        #appBox img {
            width: 46px;
            height: 46px;
            border-radius: 6px;
            margin:0 6px
        }

        #appBox .t2 {
            flex:1;
            line-height: 1.5em;
            font-size: 12px
        }

        #appBox .t2 p {
            padding: 0;
            margin: 0;
            white-space:nowrap;
            text-overflow:ellipsis;
            overflow:hidden;
            line-height: 1.25em;
        }
        #appBox .t3 a {
            display: inline-block;
            margin:0 6px;
            width: 50px;
            text-align: center;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            color: #ccc;
            line-height: 1.25em;
            padding:4px;
            text-decoration: none
        }

        #appBox.fix {
            position: fixed;
            bottom: 0;
            left: 0;
        }
    </style>
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
    <script type="text/javascript">
        $(function () {

            //----------------fold 折叠------------------------------[start]-------------------
            function fold(obj) {
                if (obj.parent(".foldBox").hasClass("foldBoxShow")) {
                    obj.parent(".foldBox").removeClass("foldBoxShow");
                } else {
                    obj.parent(".foldBox").addClass('foldBoxShow');
                }
            }

            $(".foldBox").each(function () {
                if ($(this).height() >= parseInt($(this).css("height"))) {
                    var $foldBtn = $('<div class="foldBtn"><div class="show">显示全部<svg viewBox="0 0 10 6""><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div><div class="hide">收起<svg viewBox="0 0 10 6"><path d="M8.716.217L5.002 4 1.285.218C.99-.072.514-.072.22.218c-.294.29-.294.76 0 1.052l4.25 4.512c.292.29.77.29 1.063 0L9.78 1.27c.293-.29.293-.76 0-1.052-.295-.29-.77-.29-1.063 0z"></path></svg></div></div>');
                    $foldBtn.click(function () {
                        fold($foldBtn)
                    });
                    $(this).append($foldBtn);
                }
            })
            //----------------fold 折叠------------------------------[end]start----------------

            //----------------WeChat Share------------------------------[start]-------------------
            var shareData = {
                title: '<?php echo $arr['title'];?>',
                link: window.location.href,
                imgUrl: 'http://www.atyorku.ca<?php echo $arr['cover'];?>',
                desc: '<?php echo $arr['introduction'];?>'
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
            wx.ready(function () {
                timeLine();
                message();
            });

            function timeLine() {
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

            function message() {
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
    <div class="articleContainer">
        <h1><?php echo $arr['title'] ?></h1>
        <div class="authorBox">
            <div class="left">
                <div id="authorHead" class="authorHead clickAuthor" style="background-image: url(<?php echo $arr['img'] ?>)"></div>
                <i>作者：<span class="author clickAuthor"><?php echo $arr['alias']; ?></span></i>
                <i><?php echo BasicTool::translateTime($arr['time']) ?></i>
            </div>
        </div>
        <section class="context">
            <?php echo $arr['content']; ?>
        </section>
        <div class="readCount"><em></em><span>浏览量：<?php echo $arr['view_no']; ?></span><em></em></div>
    </div>
    <!--评论组件 S-->
    <!--
    data-category 产品数据库表名
    data-production-id 产品ID（文章、二手书、分享、同学圈）
    data-receiver-id 产品作者ID
    -->
    <script type="text/javascript" src="/admin/resource/js/component.js"></script>
    <div id="commentComponent"
         data-category="guide"
         data-production-id="<?php echo $arr['id']; ?>"
         data-receiver-id="<?php echo $arr['uid']; ?>">
        <header><span>用户评论（<?php echo $arr['count_comments']; ?>）</span></header>
        <section id="commentListContainer"></section>
        <section id="loadMoreButton">点击加载更多</section>
        <section class="textAreaContainer">
            <textarea name="comment" placeholder="说两句吧..."></textarea>
            <div id="commentButton">评论</div>
        </section>
    </div>
    <!--评论组件 E-->
</article>
<div id="appBox" class="fix">
    <div><img src="/resource/img/icon.png"></div>
    <div class="t2"><p>学习资料 · 考点回忆  ·  作业问答</p><p>课程点评 · 找教学楼 · 同学圈</p><p><b>AtYorkU -约克大学专属APP</b></p></div>
    <div class="t3"><a href="http://www.atyorku.ca/download_v2.html">下载<br>App</a></div>
</div>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/guide/_footer.html";
?>
