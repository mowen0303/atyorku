/**
 * Created by Jerry on 2017-10-05.
 */
$(document).ready(function () {

    var $processInfoBox = $("#processInfoBox");
    var $processingBox = $("#processingBox");
    var $currentProcess = $("#currentProcess");
    var $stateBox = $("#stateBox");
    var processPercent = 0;
    var currentCount = 0;
    var totalCount = 0;
    var startTime = 0;

    $("#startBtn").click(function () {
        $(this).hide();
        $("#container").show();
        $stateBox.html("Extracting article title...");
        startTime = new Date().getTime()
        start();
    })

    /**
     * make an ajax request to get article title extracted from database xml file.
     */
    function start() {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: 'controller/pubmedController.php?action=getArticleTitle',
            success: function (json) {
                articleTitleArr = json.result;
                totalCount = articleTitleArr.length || 0;
                //totalCount = 1;
                if (totalCount > 0) {
                    $stateBox.html("<b>" + totalCount + "</b> ArticleTitles are extracted from Data XML file.<br><br>Async requests start...");
                    for (var i = 0; i < totalCount; i++) {
                        getPMIDAndSaveToSession(articleTitleArr[i], i);
                    }
                } else {
                    $stateBox.html("no article title get");
                }

            },
            error: function (data) {
                $stateBox.html(data + "<br>");
            }
        });
    }

    /**
     * Use multi-thread features of browser to make async ajax request. Use article title as a param to retrieve pmid and save the PMIDs and article title into the session.
     * @param articleTitle:string
     * @param sessionIndex:
     */
    function getPMIDAndSaveToSession(articleTitle, sessionIndex) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: 'controller/pubmedController.php?action=getPMIDAndSaveToSession',
            data: {'articleTitle': articleTitle, 'sessionIndex': sessionIndex},
            success: function (json) {
                currentCount++;
                $processingBox.html(currentCount + "/" + totalCount + "<br>");
                processPercent = currentCount / totalCount * 100;
                $currentProcess.css({"width": processPercent + "%"})
                if (currentCount >= totalCount) {
                    saveXML();
                }
            },
            error: function (data) {
                currentCount++;
                $processInfoBox.append("<b style='color:#fa0'>ERROR</b> : " + currentCount + "/" + totalCount + "<br>");
            }
        });
    }

    function saveXML() {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: 'controller/pubmedController.php?action=saveXML',
            success: function (json) {
                $processInfoBox.prepend();
                endtime = new Date().getTime();
                $processInfoBox.append("<br>XML file (<a href='group3_result.xml' target='_blank'>group3_result.xml</a>) generate successfully!<br> You can download it by means of click the like.<br> Or you can check it through by server, file has been saved to current path (same path with index.html)  <br><br>Elapsed time: " + (endtime - startTime) / 1000 + " seconds <br>");
            },
            error: function (data) {
                $processInfoBox.append("Some data are missing. Please re-run the program.<br>");
            }
        });
    }


})