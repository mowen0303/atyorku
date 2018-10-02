<?php
$userModel = new \admin\user\UserModel();
$employeeKPIModel = new \admin\employeeKPI\EmployeeKPIModel();
try{
    //-----------------------------------------------时间
    $start_time_html = BasicTool::get("start_time");
    $start_time = $start_time_html ? BasicTool::translateHTMLTimeToPHPStaple($start_time_html) : time() - 604800;
    $end_time_html = BasicTool::get("end_time");
    $end_time = $end_time_html ? BasicTool::translateHTMLTimeToPHPStaple($end_time_html) : time();
    $start_time < $end_time or BasicTool::throwException("查询失败:初始时间>截止时间");
    //------------------------------------------------------

}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 管理员列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=addEmployeeKPIProfile">添加</a>
</nav>

<article class="mainBox">
    <header><h2>统计周期</h2></header>
    <form action="index.php" method="get">
        <input name="s" value="getEmployeeKPIProfiles" hidden/>
        <section class="formBox">
            <div style="display:flex;flex-direction:row">
                <div>
                    <label>开始时间<i>*</i></label>
                    <input class="input" type="datetime-local" name="start_time" value="<?php echo date("Y-m-d",$start_time)."T".date("H:i",$start_time)?>" style="margin-right:3rem"/>
                </div>
                <div style="margin-left: 20px">
                    <label>截止时间<i>*</i></label>
                    <input class="input" type="datetime-local" name="end_time" value="<?php echo date("Y-m-d",$end_time)."T".date("H:i",$end_time)?>" style="margin-right:3rem"/>
                </div>
            </div>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="提交" class="btn">
        </footer>
    </form>
</article>

<article class="mainBox">
    <form class="" action="index.php" method="get">
        <section class="formRow">
            <input name="start_time" value="<?php echo $start_time_html?>" hidden/>
            <input name="end_time" value="<?php echo $end_time_html ?>" hidden/>
            <input type="hidden" name="s" value="getEmployeeKPIDetail">
            <input class="input" placeholder="用户名邮箱" type="text" name="username" value="">
            <input class="btn btn-center" type="submit" value="查询用户">
        </section>
    </form>
</article>

<article class="mainBox">
    <form action="employeeKPIController.php?action=deleteEmployeeKPIProfileById" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                    <th>ID</th>
                    <th>头像</th>
                    <th>用户名</th>
                    <th>昵称</th>
                    <th>账号数</th>
                    <th>性别</th>
                    <th>创建时间</th>
                    <th>上次更改时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $employeeKPIModel->getEmployeeKPIProfiles();
                foreach ($arr as $row) {
                    ?>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                        <td><a href="index.php?s=getEmployeeKPIDetail&id=<?php echo $row['id']?>&start_time=<?php echo $start_time_html?>&end_time=<?php echo $end_time_html?>"><?php echo $row['name'] ?></a></td>
                        <td><?php echo $row['alias']?></td>
                        <td><?php echo $row['count_accounts']?></td>
                        <td><?php echo $userModel->translateGender($row['gender'])?></td>
                        <td><?php echo BasicTool::translateTime($row['created_time'])?></td>
                        <td><?php echo BasicTool::translateTime($row['last_updated_time'])?></td>
                        <td><a href="index.php?s=addEmployeeKPIProfile&id=<?php echo $row['id']?>">更改</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php $employeeKPIModel->echoPageList(); ?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>

