<?php
$userModel = new \admin\user\UserModel();
$knowledgeCategoryModel = new \admin\knowledgeCategory\KnowledgeCategoryModel();
$id = BasicTool::get('id');


if ($id) {
    //更改
    $row = $knowledgeCategoryModel->getKnowledgeCategoryById($id);
    $form_action = "/admin/knowledgeCategory/knowledgeCategoryController.php?action=updateKnowledgeCategory";
}
else {
    //添加
    $row = null;
    $form_action = "/admin/knowledgeCategory/knowledgeCategoryController.php?action=addKnowledgeCategory";
}

?>
<header class="topBox">
    <h1><?php echo $pageTitle.'-';
        echo !$id=='add'?'添加活动类':'修改活动类';
        ?>
</header>

<article class="mainBox">
    <form action="<?php echo $form_action ?>" method="post">
        <section class="formBox">
            <input name="id" value="<?php echo $id?>" type="hidden">
            <div>
                <label>类别<i>*</i></label>
                <input class="input" type="text" name="name" value="<?php echo $row['name'] ?>">
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
