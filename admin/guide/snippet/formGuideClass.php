<?php
$userModel = new \admin\user\UserModel();
$guideModel = new \admin\guide\GuideModel();
$guideClassId = BasicTool::get('guide_class_id');
$flag = $guideClassId == null ? 'add' : 'update';

//$row = flag=='add' ? null : $guideModel->getRowOfGuideClassById($guideClassId);
if ($flag=='update')
$row = $guideModel->getRowOfGuideClassById($guideClassId);
else
$row = null;
//$judgement = $currentUser->getUserAuthority('god','guide');
//!$judgement ? null : BasicTool::echoMessage('你无权修改信息',$_SERVER['HTTP_REFERER']);
?>
<header class="topBox">
    <h1><?php echo $pageTitle.'-';
        echo $flag=='add'?'添加新指南':'修改指南';
        ?>
</header>

<article class="mainBox">
    <form action="guideController.php?action=modifyGuideClass" method="post">
        <section class="formBox">
            <input name="flag" value="<?php echo $flag?>" type="hidden">
            <input name="guide_class_id" value="<?php echo $guideClassId?>" type="hidden">
            <div>
                <label>指南类别顺序</label>
                <input class="input" type="text" name="order" value="<?php echo $row['guide_class_order'] ?>">
            </div>
            <div>
                <label>指南类别图标</label>
                <input class="input" type="text" name="icon" value="<?php echo $row['icon'] ?>">
            </div>
            <div>
                <label>指南类别名称<i>*</i></label>
                <input class="input" type="text" name="title" value="<?php echo $row['title'] ?>">
            </div>
            <div>
                <label>指南类别描述</label>
                <input class="input" type="text" name="description" value="<?php echo $row['description'] ?>">
            </div>
            <div>
                <?php if ($row['visible']==0) {?>

                <input type="radio" checked ="true" class="cBox" name="visible" value="0">显示
                <input type="radio" class="cBox" name="visible" value="1">隐藏
                <?php
                }
                elseif ($row['visible']==1) {?>
                <input type="radio" class="cBox" name="visible" value="0">显示
                <input type="radio" checked ="true" class="cBox" name="visible" value="1">隐藏   
                <?php   
                }?>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>
