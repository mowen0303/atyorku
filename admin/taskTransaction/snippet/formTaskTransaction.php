<?php
$taskTransactionModel = new \admin\taskTransaction\TaskTransactionModel();
?>

<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listTaskTransaction">返回</a>
</nav>
<article class="mainBox">
    <form action="taskTransactionController.php?action=addTaskTransaction" method="post">
        <header>
            <h2>添加成就交易</h2>
        </header>
        <section class="formBox">
            <div>
                <label>类别<i>*</i></label>
                <select class="input input-select selectDefault" name="task_type">
                    <option value="">选择类别</option>
                    <option value="book">学习资料</option>
                    <option value="course_rating">课评</option>
                    <option value="course_question">问答</option>
                    <option value="forum">朋友圈</option>
<!--                    <option value="knowledge">Knowledge</option>-->
                </select>
            </div>
            <div>
                <label>用户ID<i>*</i></label>
                <input class="input" type="text" name="user_id" value="">
            </div>
            <div>
                <label>产品ID<i>*</i></label>
                <input class="input" type="text" name="item_id" value="">
            </div>
            <div>
                <label>操作类别<i>*</i></label>
                <select class="input input-select selectDefault" name="op">
                    <option value="">选择操作类别</option>
                    <option value="add">添加</option>
                    <option value="delete">删除</option>
                </select>
            </div>
        </section>
        <footer class="submitBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>

</article>
