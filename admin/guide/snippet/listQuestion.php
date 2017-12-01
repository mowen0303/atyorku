<?php
$guideModel = new \admin\guide\GuideModel();
$userModel = new \admin\user\UserModel();
$question_id=BasicTool::get('q_id');
$arr = $guideModel->getQuestion($question_id);
$options=explode(',',$arr['options']);
$next_question=$question_id+1;
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=formQuestion&q_id=<?php echo $question_id ?>">增加</a>
<?php if($pre_question != 0){?>
            <a class="btn"href="javascript:history.go(-1)"">上一题</a>
            <?php
            }
            ?>

    <a class="btn" href="index.php?s=listGuideClass">返回指南</a>
</nav>

<article class="mainBox">
    <header>
        <ul>
            <li>
            <h2 id="question">问题 <?php echo $question_id;?>: <?php echo $arr['question'];?></h2>
            </li>
            <li><a href="index.php?s=formQuestion&q_id=<?php echo $question_id;?>">修改</a></li>
            <li>
                <div style="text-align:center;">
            <?php if($arr['img'] != null)
                {
            ?>
                <img width="20%"src="<?php echo $arr['img']?>">
                 <?php
                }
                 ?>
             </div>
            </li>
        </ul>
        <input name="q_no" id="q_no"value="<?php echo $question_id?>" type="hidden">
        <input name="correct" id="correct"value="<?php echo $arr['solution']?>" type="hidden">
    </header>
    <table class="tab">
        <thead>
        <tr>
            <th colspan="3" width="21px">选项</th>
        </tr>
        </thead>
        <tbody>
    <?php
    for ($i = 0; $i < count($options); $i++){
     ?>
        <tr>
            <td><input type="radio" class="cBox" name="answer" value="<?php echo $i ?>"></td>

            <td ><?php echo $options[$i]; ?></td>
        </tr>
    <?php
    }
    ?>
        </tbody>

    </table>
    <p id="right">正确</p>
    <p id="wrong">错误</p>

        <footer class="buttonBox">
                <input type="submit" value="提交" id="btnSubmit" class="btn">
                <input type="submit" value="下一题" id="btnnext" class="btn" >
        </footer>
 <script>
     $(function(){
         $("#right").hide();
         $("#wrong").hide();
         $("#btnnext").hide();

             $("#btnSubmit").click(function(){
                 var q_no=document.getElementById("q_no").value;
                 var val=$('input:radio[name="answer"]:checked').val();
                 var correct=document.getElementById("correct").value;
                 if(val==correct){
                     q_no++;
                     $("#right").show();
                     $("#wrong").hide();
                     $("#btnnext").show();
                     $("#btnnext").click(function () {
                         $.ajax({
                             type: "get",
                             dataType: "json",
                             url:"http://localhost/admin/guide/guideController.php",
                             data:{action:'getQuestionWithJson',q_id:q_no},
                             success: function (msg) {
//                                 for(int v=0; v<msg.length; v++){
//                                 alert(msg.v);}
                                 //var info=JSON.stringify(msg);

                                 var result = eval(msg.result);

                                 alert(result.id);


                                 $("#q_no").val(msg.id);
                                 $("#question").text("问题"+msg.id+":"+msg.question);
                             }
                         });
                     });

                 }
                 else {
                     $("#wrong").show();
                     $("#right").hide();
                 }
             });



         //=======
         });
 </script>
</article>

