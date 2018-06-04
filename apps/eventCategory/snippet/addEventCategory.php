<?php
$userModel = new \admin\user\UserModel();
$eventCategoryModel = new \apps\eventCategory\EventCategoryModel();
$id = BasicTool::get('id');
$flag = $id == null ? 'add' : 'update';


if ($flag=='update') {
    $row = $eventCategoryModel->getEventCategory($id);
    $form_action = "./eventCategoryController.php?action=updateEventCategory";
    }
else
    {$row = null;
    $form_action = "./eventCategoryController.php?action=addEventCategory";
    }

?>
<header class="topBox">
    <h1><?php echo $pageTitle.'-';
        echo $flag=='add'?'添加活动类':'修改活动类';
        ?>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post">
        <section class="formBox">
            <input name="id" value="<?php echo $id?>" type="hidden">
            <div>
                <label>活动类别<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>描述<i>*</i></label>
                <input class="input" type="text" name="description" value="<?php echo $row['description'] ?>">
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
