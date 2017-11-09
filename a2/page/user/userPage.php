<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/a2/page/frame/validateTop.php";
try {
    $userArr = $currentUser->getUsers();
} catch (Exception $e) {
    BasicTool::echoMessage("Error",$e->getMessage());
}
?>

<?php require_once $_SERVER["DOCUMENT_ROOT"]."/a2/page/frame/top.php"; ?>
    <header class="cmsConHeader">
        <span>User list</span>
    </header>
    <section class="cmsConSection">
        <div class="userContainer">
        <?php foreach($userArr as $row) { ?>
        <div class="userBox">
            <div class="userAvatarBox"></div>
            <div class="UserInfoBox">
                <div class="tooBox"><a href="#" class="icon iconfont icon-xiugai"></a><a href="#" class="icon iconfont icon-shanchu"></a>
                </div>
                <h1><?php echo $row['user_name']?></h1>
                <p><span>User ID:</span><?php echo $row['user_id']?></p>
                <p><span>User Group:</span><?php echo $row['user_category_title']?></p>
            </div>
        </div>
        <?php } ?>
        </div>
    </section>
<?php require_once $_SERVER["DOCUMENT_ROOT"]. "/a2/page/frame/bottom.php"; ?>
