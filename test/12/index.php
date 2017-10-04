<?php
require_once 'model/XMLUtil.class.php';
$xmlUtil = new XMLUtil();
$xmlData = simplexml_load_file('asset/4020a1-datasets.xml');
$articleTitleArr = $xmlUtil->extractArticleTitle($xmlData);
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <title>ITEC 4020 Assignment 1</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var startTime = new Date().getTime();
            var $container = $("#container");
            var currentIndex = 0;
            articleTitleArr = eval(<?php echo json_encode($articleTitleArr);?>);
            var totalCount = articleTitleArr.length || 0;
            totalCount = 2;
            if (totalCount > 0) {
                $container.prepend(totalCount + " ArticleTitles got. Start to work...");
            }

            for (var i = 0; i < totalCount; i++) {
                getPMIDAndSaveToSession(articleTitleArr[i], i);
            }


            //async
            function getPMIDAndSaveToSession(articleTitle, sessionIndex) {
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: 'controller/pubmedController.php?action=getPMIDAndSaveToSession',
                    data: {'articleTitle': articleTitle, 'sessionIndex': sessionIndex},
                    success: function (json) {
                        currentIndex++;
                        $container.prepend(json.result + ": " + currentIndex + "/" + totalCount + "<br>");
                        if (currentIndex >= totalCount) {
                            saveXML();
                        }
                    },
                    error:function(data){
                        $container.prepend("<b style='color:#fa0'>ERROR</b> : " + currentIndex + "/" + totalCount + "<br>");
                    }
                });
            }

            function saveXML() {
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: 'controller/pubmedController.php?action=saveXML',
                    success: function (json) {
                        $container.prepend();
                        endtime = new Date().getTime();
                        $container.prepend("<br>XML file generate " + json.result + "!  elapsed time: " + (endtime - startTime) / 1000 + " seconds <br>");
                    },
                    error: function (data) {
                        $container.prepend("Some data are missing. Please re-run the program.");
                    }
                });
            }


        })
    </script>
    <style>
        body {
            background: #f4f4f4;
            margin: 0;
            padding: 0
        }

        #pageBox {
            width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 50px
        }

        header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            text-align: center
        }

        #container {
            padding: 20px;
            text-align: center
        }
    </style>
</head>
<body>
<div id="pageBox">
    <header>
        <h1> Assignment 1 - Group 3</h1>
        <h3> ITEC4020</h3>
        <p>Team members: Jerry, Allen, Eva, Dennis, Effy.</p>
    </header>
    <section id="container"></section>
</div>
</body>
</html>