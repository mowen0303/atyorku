<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonClass/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_header.html";
$courseModel = new \admin\course\CourseModel();
$arr = $courseModel->getUserListOfCourseDescription();
?>

<script>

    $(function(){

        $(".headImg").each(function(index){

            $(this).click(function(){

                //alert($(this).attr("data-uid"))

                var url = 'jsbridge://doAction?'+$(this).attr("data-uid");
                var iframe = document.createElement('iframe');
                iframe.style.width = '1px';
                iframe.style.height = '1px';
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);
                setTimeout(function() {
                    iframe.remove();
                }, 100);

            })

        })


        $(".courseNode").each(function(index){

            $(this).click(function(){

                //alert($(this).attr("data-uid"))

                var url = 'jscourse://doAction?title='+$(this).attr("data-title")+'&courseId='+$(this).attr("data-courseId");
                var iframe = document.createElement('iframe');
                iframe.style.width = '1px';
                iframe.style.height = '1px';
                iframe.style.display = 'none';
                iframe.src = url;
                document.body.appendChild(iframe);
                setTimeout(function() {
                    iframe.remove();
                }, 100);

            })

        })






        $("#coverImgBox img").addClass("animate2");

    })
</script>
<style>
    .userBox { position: relative; min-height: 100px; overflow: hidden}
    .headImg {width: 50px; height:50px; border-radius: 50px; border:3px solid #eee;position: overflow: hidden; background-color:#efefef; background-size: 100% auto; background-position: center; position: absolute; left: 25px; top:20px}
    .userBox .userInfo { margin-left: 95px; color: #999}
    .userBox span { font-size: 1.2em; line-height: 1.2em; color: #666; color: #c91448}
    .userBox a {display:inline-block; width:30%; float: left;  overflow: hidden; text-align: center; border: 1px solid #eee; text-decoration: none; margin: 4px 4px 0 0; color: #aaa; border-radius: 4px; line-height: 1.5em}
    .userBox a:hover {text-decoration: none}
    .userBox em { font-style: normal; color: #999}
    .userBox p {overflow: hidden}
</style>
<article id="container">
    <div id="coverImgBox"><img style="width: 100%; height: auto" src="/uploads/guide/images/1000-503-10.jpg"></div>

    <section>
   		<h2>学霸智囊团名单</h2>
        <?php
        foreach($arr as $row){
        ?>
            <div class="userBox">
                <div class="headImg" data-uid="<?php echo $row['id'] ?>" style="background-image: url(<?php echo $row['img'] ?>)"></div>
                <p class="userInfo"><span><?php echo $row['alias']?></span><br><?php echo BasicTool::translateEnrollYear($row['enroll_year'])?> <?php echo $row['major']?></p>
                <p><em>总结的课程</em> <br>
                    <?php
                    $courseArr = $courseModel->getCourseListByUserId($row['id']);
                    foreach($courseArr AS $courseRow){
                        ?>
                        <a class="courseNode" data-courseId="<?php echo $courseRow['id']?>" data-title="<?php echo $courseRow['mtitle'].$courseRow['title'];?>"><?php echo $courseRow['mtitle'].$courseRow['title'];?></a>
                        <?php
                    }
                    ?>
            </div>
        <hr>
        <?php
        }
        ?>
        <h2>什么是学霸智囊团</h2>
		<p>学霸智囊团是由atYorkU组织发起的一个纯公益学术团体。主要工作是对每门课程进行总结，目的是将学霸们的上课、选课经验凝聚起来，汇集成约克最大、最靠谱的课程参考平台，从而让同学们在选课之前对课程有一个清晰的认识，科学选课，减少挂科。</p>
		<p>看似一门简单的课程总结，其实都至少有一个学霸在背后默默的付出了大量的心血。在你使用课评系统的时候，你能想象到在你的背后有多少学霸在为你助力吗？他们的付出不求回报，只为让我们的青春更加美好，请为默默付出的学霸们默默的点个赞。</p>
        <h2>加入学霸智囊团</h2>
        <p>如果你对某一门课程了解的比较透彻，欢迎加入学霸智囊团，给学弟学妹们讲述一下这个课程修课经验。</p>
        <p>有兴趣的同学请联系微信号: jiyu55</p>
        
        
        
    </section>
</article>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/apps/templete/article/_footer.html";
?>
