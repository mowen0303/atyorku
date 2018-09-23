<?php
try{
    $userModel = new \admin\user\UserModel();
    $employeeKPIModel = new \admin\employeeKPI\EmployeeKPIModel();
    if (BasicTool::get("id")){
        $id = BasicTool::get("id");
        $profile = $employeeKPIModel->getEmployeeKPIProfiles($id)[0] or BasicTool::throwException("查询失败");
    }else{
        $searchedUsername = BasicTool::get("username","用户名邮箱不能为空");
        $main_uid = $userModel->getUserIdByName($searchedUsername) or BasicTool::throwException("用户不存在");
        $profile = $employeeKPIModel->getEmployeeKPIProfileByMainUserId($main_uid) or BasicTool::throwException("查询失败");
    }
    //-----------------------------------------------时间
    $start_time = BasicTool::get("start_time");
    $start_time = $start_time ? BasicTool::translateHTMLTimeToPHPStaple($start_time) : time() - 604800;
    $end_time = BasicTool::get("end_time");
    $end_time = $end_time ? BasicTool::translateHTMLTimeToPHPStaple($end_time) : time();
    $start_time < $end_time or BasicTool::throwException("查询失败:初始时间>截止时间");
    //------------------------------------------------------

}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}

$user_ids = $profile["user_ids"];
sort($user_ids);
$user_ids = array_merge([$profile["main_user_id"]],$user_ids);

?>

<header class="topBox">
    <h1><?php echo $pageTitle?> - <?php echo $profile["alias"]?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=getEmployeeKPIProfiles">返回</a>
</nav>

<article class="mainBox">
    <header><h2>账户列表</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>用户ID</th>
                <th>头像</th>
                <th>用户名</th>
                <th>昵称</th>
                <th>文章数</th>
                <th>同学圈数</th>
                <th>评论数</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $employeeKPIModel->getPostCountByUserId($user_ids,$start_time,$end_time);
            foreach ($arr as $row) {
                $user_profile = $userModel->getProfileOfUserById($row["user_id"]);
                ?>
                <tr>
                    <td><?php echo $user_profile["id"]?></td>
                    <td><img width="36" height="36" src="<?php echo $user_profile['img']?>"></td>
                    <td><?php echo $user_profile['name']?></td>
                    <td><?php echo $user_profile['alias']?></td>
                    <td><?php echo $row['count_guides']?></td>
                    <td><?php echo $row['count_forums']?></td>
                    <td><?php echo $row['count_comments']?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header><h2>文章</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>封面</th>
                <th>标题</th>
                <th>简介</th>
                <th>发布时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $employeeKPIModel->getGuidesByUserId($user_ids,$start_time,$end_time);
            foreach ($arr as $temp) {
                $user_profile = $userModel->getProfileOfUserById($temp["user_id"]);
                foreach ($temp["data"] as $row) {
                    ?>
                    <tr>
                        <td><img width="36" height="36" src="<?php echo $user_profile['img'] ?>"></td>
                        <td><?php echo $user_profile["alias"] ?></td>
                        <td><div style="width:200px;height:125px;background-image:url('<?php echo $row['img_url']?>');background-size:auto 100%;background-repeat:no-repeat;background-position:center"></div></td>
                        <td><?php echo $row['title'] ?></td>
                        <td><?php echo $row['introduction'] ?></td>
                        <td><?php echo BasicTool::translateTime($row['time']) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header><h2>同学圈评论</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>内容</th>
                <th>发布时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $employeeKPIModel->getCommentsByUserId($user_ids,$start_time,$end_time);
            foreach ($arr as $temp) {
                $user_profile = $userModel->getProfileOfUserById($temp["user_id"]);
                foreach ($temp["data"] as $row) {
                    ?>
                    <tr>
                        <td><img width="36" height="36" src="<?php echo $user_profile['img'] ?>"></td>
                        <td><?php echo $user_profile["alias"] ?></td>
                        <td><?php echo $row['comment'] ?></td>
                        <td><?php echo BasicTool::translateTime($row['time']) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </section>
</article>

<article class="mainBox">
    <header><h2>同学圈</h2></header>
    <section>
        <table class="tab">
            <thead>
            <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>图片</th>
                <th>内容</th>
                <th>发布时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $arr = $employeeKPIModel->getForumsByUserId($user_ids,$start_time,$end_time);
            foreach ($arr as $temp) {
                $user_profile = $userModel->getProfileOfUserById($temp["user_id"]);
                foreach ($temp["data"] as $row) {
                    ?>
                    <tr>
                        <td><img width="36" height="36" src="<?php echo $user_profile['img'] ?>"></td>
                        <td><?php echo $user_profile["alias"] ?></td>
                        <td><img width="60px" height="auto" src="<?php echo $row['img_url'] ?>"></td>
                        <td><?php echo $row['content'] ?></td>
                        <td><?php echo BasicTool::translateTime($row['time']) ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </section>
</article>
