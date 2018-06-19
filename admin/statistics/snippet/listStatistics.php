<?php
$statisticsModel = new \admin\statistics\StatisticsModel();
$currentUser = new \admin\user\UserModel();
$offSet = 200;
?>
<header class="topBox">
    <h1><?php echo $pageTitle?></h1>
</header>
<nav class="mainNav">
    <a class="btn" href="index.php?s=listForumClass">返回论坛</a>
</nav>
<article class="mainBox">
    <header><h2>统计数据</h2></header>
    <section>
        <p><b>最近30天平均访问：</b><span id="view30"></span></p>
        <p><b>最近7天平均访问：</b><span id="view7"></span></p>
        <p>有效用户:<?php echo $offSet+700+$currentUser->getCountOfUserForValid() ?></p>
        <p>设备注册量:<?php echo $currentUser->getCountOfDevice()+$offSet+700 ?></p>
        <canvas id="lineChart" width="400" height="400"></canvas>
        <script type="text/javascript">
        $(document).ready(function(){

            let $view30 = $("#view30");
            let $view7 = $("#view7");

            let label = [];
            let data4 = [];

            let $type4Label = $('.type-4-label');
            $type4Label.each((i,val)=>{
                label.unshift($type4Label.eq(i).text());
            });

            let $type4Val = $('.type-4-val');
            $type4Val.each((i,val)=>{
                data4.unshift($type4Val.eq(i).text());
            });

            sum30 = 0;
            data4.forEach(val=>{
                sum30+= parseInt(val);
            })

            sum7 = 0;
            data4.length
            for(i=0;i<7;i++){
                sum7 += parseInt(data4[data4.length-i-1]);
            }

            $view30.html(Math.ceil(sum30/data4.length));
            $view7.html(Math.ceil(sum7/7));

            new Chart(document.getElementById("lineChart"), {
                type: "line",
                data: {
                    labels: label,
                    datasets: [{
                        label: "独立用户",
                        data: data4,
                        fill: false,
                        borderColor: "rgb(75, 192, 192)",
                        lineTension: 0.2
                    }]
                },
                options: {
                    maintainAspectRatio:false,
                    responsive: true,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                offsetGridLines: false,
                                display: true
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            },
                            gridLines: {
                                display: true
                            }
                        }]
                    }
                }
            });
        });
        </script>
    </section>
</article>
<article class="mainBox">
    <header><h2>浏览量</h2></header>
    <form action="forumController.php?action=deleteForum" method="post">
        <section>
            <table class="tab">
                <thead>
                <tr>
                    <th>日期</th>
                    <th>类型</th>
                    <th>浏览量</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $arr = $statisticsModel->getListOfStatic(4,30);
                foreach ($arr as $row) {
                ?>
                    <tr>
                        <td class="type-<?php echo $row['type']?>-label"><?php echo $row['date'] ?></td>
                        <td>
                            <?php
                            switch($row['type']){
                                case 1:
                                    echo "新鲜事";
                                    break;
                                case 2:
                                    echo "资讯";
                                    break;
                                case 3:
                                    echo "课评";
                                    break;
                                case 4:
                                    echo "今日用户数量";
                                    break;
                            }
                            ?>
                        </td>
                        <td class="type-<?php echo $row['type']?>-val"><?php echo $row['amount_view']+$offSet ?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <?php echo $statisticsModel->echoPageList()?>
        </section>
        <footer class="buttonBox">
            <input type="submit" value="删除" class="btn" onclick="return confirm('确认删除吗?')">
        </footer>
    </form>
</article>
