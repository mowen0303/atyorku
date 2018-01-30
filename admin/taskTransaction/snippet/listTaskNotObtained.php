<?php
$taskTransactionModel = new \admin\taskTransaction\TaskTransactionModel();
$userModel = new \admin\user\UserModel();
$userId = BasicTool::get("user_id");
?>
<style>
    .isDisabled {
        color: currentColor;
        cursor: not-allowed;
        opacity: 0.5;
        text-decoration: none;
    }
</style>
<header class="topBox">
    <h1>未领取的成就奖励</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=summaryTaskTransaction&user_id=<?php echo $userId ?>">返回</a>
</nav>
<article class="mainBox">
    <header><h2>成就奖励列表</h2></header>
    <table class="tab">
        <thead>
        <tr>
            <th width="21px"><input id="cBoxAll" type="checkbox"></th>
            <th width="40px">顺序</th>
            <th width="60px">图标</th>
            <th>标题</th>
            <th width="60px">奖励</th>
            <th width="80px">学习资料</th>
            <th width="60px">课评</th>
            <th width="60px">问答</th>
            <th width="60px">同学圈</th>
            <th width="60px">knowledge</th>
            <th width="60px">操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        try{
            $arr = $taskTransactionModel->getListOfAvailableTaskDesignsByUserId($userId);
            foreach ($arr as $row) {
                ?>
                <tr id="task<? echo $row['id'] ?>">
                    <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id'] ?>"/></td>
                    <td><?php echo $row["id"] ?></td>
                    <td><img width="60px" height="auto" src="<?php echo $row['icon_url'] ?>"></td>
                    <td><?php echo $row["title"] ?></td>
                    <td><?php echo $row["bonus"] ?></td>
                    <td><?php echo $row["book"] ?></td>
                    <td><?php echo $row["course_rating"] ?></td>
                    <td><?php echo $row["course_question"] ?></td>
                    <td><?php echo $row["forum"] ?></td>
                    <td><?php echo $row["knowledge"] ?></td>

                    <td><a class="btn obtainBtn" href="#" data-id="<?php echo $row['id']?>">领取</a></td>
                </tr>
                <?php
            }
        }catch(Exception $e){
            BasicTool::echoMessage($e->getMessage(),$_SERVER['HTTP_REFERER']);
        }
        ?>
        </tbody>
    </table>
</article>

<script>
    $(document).ready(function() {
        // 注册领取按钮
        $(".obtainBtn").click(function (e) {
            $(this).addClass('isDisabled');
            var taskId = e.target.dataset.id;
            var userId = <?php echo $userId ?>;
            if (taskId && userId) {
                $.ajax({
                    url: "/admin/taskTransaction/taskTransactionController.php?action=obtainTaskDesignByIdWithJson",
                    type: "POST",
                    contentType: "application/x-www-form-urlencoded",
                    data: {"user_id":userId,"task_id":taskId},
                    dataType: "json",
                }).done(function (json) {
                    console.log(json);
                    if (json.code === 1) {
                        alert("获取成功");
                        var td = "#task" + taskId;
                        $(td).remove();
                    }else{
                        alert("获取失败");
                        $(that).removeClass('isDisabled');
                    }
                }).fail(function (data) {
                    console.log(data);
                    alert("获取失败");
                    $(that).removeClass('isDisabled');
                });
            } else {
                alert("缺失用户ID或成就ID");
                $(that).removeClass('isDisabled');
            }
        });
    });
</script>