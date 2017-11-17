<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/a2/page/frame/validateTop.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/a2/config/authority.php";
try {
    $userCategoryID = BasicTool::get("id");
    $userCategoryRow = $currentUser->getUserCategoryByID($userCategoryID);
} catch (Exception $e) {
    BasicTool::echoMessage("Error",$e->getMessage());
}
?>
<?php require_once $_SERVER["DOCUMENT_ROOT"]. "/a2/page/frame/top.php"; ?>
    <header class="cmsConHeader">
        <span>User Category</span>
    </header>
    <section class="cmsConSection cmsConSectionWhite">
        <h1><?php echo $userCategoryRow["user_category_title"]?></h1>
        <form action="userController.php?action=updateUserCategoryAuthority" method="post">
            <input type="hidden" name="id" value="<?php echo $userCategoryID?>"/>
        <?php foreach($RIGHT as $k=>$v){ ?>
            <P><label><input type="checkbox" name="authority[]" value="<?php echo $v?>" <?echo $userCategoryRow['user_category_authority']&$v?'checked':null?> ><?php echo $k?></label></P>
        <?php } ?>
            <input type="submit" value="submit"/>
        </form>
    </section>
<?php require_once $_SERVER["DOCUMENT_ROOT"]. "/a2/page/frame/bottom.php"; ?>