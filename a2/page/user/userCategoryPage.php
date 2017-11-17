<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/a2/page/frame/validateTop.php";
try {
    $userCategoryArr = $currentUser->getUserCategories();
} catch (Exception $e) {
    BasicTool::echoMessage("Error",$e->getMessage());
}
?>
<?php require_once $_SERVER["DOCUMENT_ROOT"]. "/a2/page/frame/top.php"; ?>
    <header class="cmsConHeader">
        <span>User Category</span>
    </header>
    <section class="cmsConSection">
        <div class="cmsTableBox">
            <table>
                <tbody>
            <?php foreach($userCategoryArr as $row) { ?>
                <tr>
                    <td><?php echo $row['user_category_title']?></td>
                    <td class="textRightAlign"><a href="userCategoryEditPage.php?id=<?php echo $row['user_category_id']?>" class="icon iconfont icon-xiugai"></a></td>
                </tr>
            <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
<?php require_once $_SERVER["DOCUMENT_ROOT"]. "/a2/page/frame/bottom.php"; ?>