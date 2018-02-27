<?php
try{
    $searchedUsername = BasicTool::get("username","用户名邮箱不能为空");
    $userModel = new \admin\user\UserModel();
    $searchedUID = $userModel->getUserIdByName($searchedUsername) or BasicTool::throwException("用户不存在");
    $row = $userModel->getProfileOfUserById($searchedUID);
}catch(Exception $e){
    BasicTool::echoMessage($e->getMessage());
    die();
}
?>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 用户列表</h1>
</header>
<nav class="mainNav">
    <a class="btn" href="javascript:history.go(-1);">返回</a>
</nav>
<article class="mainBox">
        <section>
            <table class="tab">
                <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>ID</th>
                        <th>头像</th>
                        <th>用户名</th>
                        <th>昵称</th>
                        <th>点券</th>
                        <th>活跃度</th>
                        <th>性别</th>
                        <th>身份</th>
                        <th>状态</th>
                        <th>注册时间</th>
                        <th>设备</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['id']?>"><input type="hidden" name="isAdmin[]" value="<?php echo $row['is_admin']?>"></td>
                        <td><?php echo $row['id']?></td>
                        <td><img width="36" height="36" src="<?php echo $row['img']?>"></td>
                        <td><a href="index.php?s=formUser&uid=<?php echo  $row['id']?>"><?php echo $row['name'] ?></a></td>
                        <td><?php echo $row['alias']?></td>
                        <td><a href="/admin/transaction/index.php?s=getTransactions&uid=<?php echo $row['id']?>"><?php echo $row['credit']?></a> </td>
                        <td><?php echo $row['activist']?></td>
                        <td><?php echo $userModel->translateGender($row['gender'])?></td>
                        <td><?php echo $row['title'] ?></td>
                        <td><?php echo $row['blocktime']-time()>0 ? "禁言中..." : "正常" ?></td>
                        <td><?php echo BasicTool::translateTime($row['registertime']); ?></td>
                        <td><?php echo $row['device'] == false ? "0" : "绑" ?></td>
                        <td><a class="btn" href="index.php?s=formCredit&uid=<?php echo $row['id']?>">点券管理</a></td>
                    </tr>
                </tbody>
            </table>
        </section>
</article>
