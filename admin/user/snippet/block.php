<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_header.html";

$currentUser = new \admin\user\UserModel();
if(!$userModel->isAdminLogin()){
    $userModel->logOut();
    BasicTool::jumpTo('/admin/login/');
}
$currentUser->isUserHasAuthority('ADMIN') or BasicTool::echoMessage("权限不足");
$uid = BasicTool::get('userId','UID不能为空');
$row = $currentUser->getProfileOfUserById($uid);
//print_r($row);
?>


<script>
    $(function(){

        $blockBox1 = $("#blockBox1");
        $blockBox2 = $("#blockBox2");
        $blockBox1.change(function(){
            if($blockBox1.val()>=0){
                if($blockBox1.val()>0){
                    $blockBox2.show();
                }
                $("#btn").removeClass("disable");
            }else{
                $blockBox2.hide();
                $("#btn").addClass("disable");
            }
        })

        $("#btn").click(function(){

            if($blockBox1.val()<0){
                return
            } else {
                $("#btn").text("数据提交中....");
            }


            //ajax请求接口
            $.post("/admin/user/userController.php?action=blockUserByUserId",
                $("#formNode").serialize(),
                function(data) {
                    //从接口获取的json数据,会赋值给data这个临时变量
                    $("#btn").text("提交");
                    if(data.code == 1){
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }},"json")
        })

    })
</script>
<style>


    #container { text-align: center}
    .btn { width: 40%; padding:0.7em 3em; line-height: 1em; display: inline-block; text-align: center; color: #fff; text-decoration: none; background:#c83c46; margin: 1em; border: 0; border-radius:17px; cursor:pointer; transition: background 0.3s,border 0.5s;white-space:nowrap;}
    select { height: 2em;margin:0.5em 0 ;width: 60%; cursor: pointer; margin-right: 10px; text-indent: 5px;}
    .disable { background: #CCC}
    #blockBox { height: 100px;}



</style>
<article id="container">
    <form id="formNode">
        <input name="userId" value="<?php echo $uid?>" type="hidden">
    <section>
        <br>
        <h1><?php echo $row['alias']?></h1>
        <p>UID:<?php echo $uid?></p>
        <hr><p>&nbsp;</p>
        <div class="authorBox">
            <div id="authorHead" class="authorHead" style="background-image: url(<?php echo $row['img'] ?>)"></div>
            <em></em>
            <div class="con" id="authorComment">
                <?php
                    if($row['blockState']==0){
                        echo "当前账号状态: 正常";
                    } else {
                        echo "此账号已因'{$row[blockreason]}'被禁言! 解禁日期:{$row[blockToTime]}";
                    }
                ?>
            </div>
        </div>

        <div id="blockBox" >
            <div>
                <select id="blockBox1" class="input input-select input-size30 selectDefault" name="days">
                    <option value="-1">设置禁言状态</option>
                    <option value="0">解除禁言</option>
                    <option value="7">禁言7天</option>
                    <option value="14">禁言14天</option>
                    <option value="30">禁言30天</option>
                    <option value="3000">永久禁言</option>
                </select>
            </div>
            <div>
                <select id="blockBox2" class="input input-select input-size50 selectDefault" name="reason" style="display: none">
                    <option value="发表不当内容">发表不当内容</option>
                    <option value="发布非授权广告">发布非授权广告</option>
                    <option value="辱骂或攻击他人">辱骂他人</option>
                    <option value="恶意灌水">恶意灌水</option>
                </select>
            </div>
        </div>
        <hr>
    </section>
    <footer>
        <div id="btn" class="btn btn-center disable">提交</div>
    </footer>
    </form>
</article>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_footer.html";
?>
