<?php
$userModel = new \admin\user\UserModel();

$user_class_id = BasicTool::get('id');
$flag = $user_class_id == null ? 'add' : 'update';
$row = flag=='add' ? null : $userModel->getRowById('user_class',$user_class_id);
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 分类管理</h1>
</header>

<article class="mainBox">
    <form action="userController.php?action=modifyUserClass" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="id" value="<?php echo $user_class_id ?>" type="hidden">
            <div>
                <label>类别描述<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>是否为管理员组</label>
                <select name="is_admin" defvalue="<?php echo $row['is_admin']?>" class="input input-select input-size50 selectDefault">
                    <option value="0">非管理员</option>
                    <option value="1">管理员</option>
                </select>
            </div>
            <div class="authorityBox">
            <label>操作权限</label>
            <?php
            global $_AUT;
            foreach($_AUT as $k=>$v){
            ?>
            <P><input name="authority[]" value="<?php echo $v?>" <?php echo $row['authority']&$v?'checked':null?> type="checkbox"><?php echo $k?></P>
            <?php
            }
            ?>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
