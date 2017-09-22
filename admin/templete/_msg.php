<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login - AtYorkU</title>
    <link href="/admin/resource/css/style.css" rel="stylesheet" type="text/css">
    <script src="/resource/js/jquery-2.1.4.js" type="text/javascript"></script>
    <script src="/admin/resource/js/main.js" type="text/javascript"></script>
</head>

<body class="bodyMain">
    <!-- topBox s -->
    <header class="topBox">
        <h1>操作提示</h1>
    </header>
    <!-- topBox e -->
    <!-- mainBox s -->
    <article class="mainBox">
        <!-- 内容 s -->
        <section class="msgBox">
            <p><?php echo $msg; ?></p>

            <?php
            if($url === 0){
                echo "";
            } else if(is_integer($url)) {
                echo '<p><input class="btn" onclick="javascript:history.go('.$url.');" value="'.$urlTxt.'"> </p>';
            }
            else {
                echo '<p><a class="btn" href="'.$url.'">'.$urlTxt.'</a> </p>';
            }
            ?>
        </section>
        <!-- 内容 s -->
    </article>
    <!-- mainBox e -->
<?php
require_once "_mainFoot.php";
?>