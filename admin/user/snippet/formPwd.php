<?php
$currentUser = new \admin\user\UserModel();
$userId = BasicTool::get('uid');
$inquiredUser = new \admin\user\UserModel($userId);
?>
<script>
//当页面document加载完成之后,执行
$(function(){
    $("#btn").click(function(){
        $.ajax({
            url:"/admin/user/userController.php?action=updatePwd",
            type:"POST",
            contentType:"application/x-www-form-urlencoded",
            dataType:"json",
            data:$("#formNode").serialize(),
            success:function(data){
                if(data.code == 1){
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            }
        });
    })
})
</script>
<header class="topBox">
    <h1><?php echo $pageTitle?> - 修改密码</h1>
</header>
<article class="mainBox">
<form id="formNode">
    <section>
        <div class="formBox">
            <div>
                <input type="hidden" name="uid" value="<?php echo $userId?>" id="uid">
            </div>
            <div>
                <label>原密码(管理员可忽略此项)</label>
                <input class="input" type="password" name="pwdOfOld" value="">
            </div>
            <div>
                <label>新密码</label>
                <input class="input" type="password" name="pwd" value="">
            </div>
            <div>
                <label>确认新密码</label>
                <input class="input" type="password" name="pwd2" value="">
            </div>
        </div>
    </section>
    <footer class="submitBox">
        <input type="button" value="修改" class="btn" id="btn">
    </footer>
</form>
</article>