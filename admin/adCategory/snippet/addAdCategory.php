<?php
$userModel = new \admin\user\UserModel();
$adCategoryModel = new \admin\adCategory\AdCategoryModel();
unset($id);
$id = BasicTool::get('id');
$flag = $id == null ? 'add' : 'update';


if ($flag=='update') {
    $row = $adCategoryModel->getAdCategory($id);
    $form_action = "/admin/adCategory/AdCategoryController.php?action=updateAdCategory";
    }
else
    {$row = null;
    $form_action = "/admin/adCategory/AdCategoryController.php?action=addAdCategory";
    }
//$judgement = $currentUser->getUserAuthority('god','guide');
//!$judgement ? null : BasicTool::echoMessage('你无权修改信息',$_SERVER['HTTP_REFERER']);
?>
<header class="topBox">
    <h1><?php echo $pageTitle.'-';
        echo $flag=='add'?'添加广告类':'修改广告类';
        ?>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post">
        <section class="formBox">
            <input name="id" value="<?php echo $id?>" type="hidden">
            <div>
                <label>类型</label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>大小</label>
                <input class="input" type="text" name="size" value="<?php echo $row['size'] ?>">
            </div>
            <div>
                <label>描述</label>
                <input class="input" type="text" name="description" value="<?php echo $row['description'] ?>">
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
