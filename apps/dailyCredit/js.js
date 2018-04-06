$(document).ready(function(){


    //progress
    var progressAmount = $("#progress").attr("data-amount");
    var progressVal = $("#progress").attr("data-val");
    var progressConf = {
            color: '#585858',
            strokeWidth: 4,
            trailWidth: 4,
            easing: 'easeInOut',
            duration: 1000,
            text: {autoStyleContainer: false},
            from: { color: '#f53053', width: 4},
            to: { color: '#f53053', width: 4 },
            // Set default step function for all animate calls
            step: function(state, circle) {
                circle.path.setAttribute('stroke', state.color);
                circle.path.setAttribute('stroke-width', state.width);
                var value = circle.value().toFixed(1);
                circle.setText(progressVal);
            }
    }
    var bar = new ProgressBar.Circle(progress, progressConf);
    bar.text.style.fontFamily = 'DINMittelschrift';
    bar.text.style.fontSize = '2.5rem';
    bar.animate(progressVal/progressAmount);  // Number from 0.0 to 1.0

    //get credit
    $creditBtn = $("#creditBtn");
    $closeBtn = $("#closeBtn");
    $creditBtnState = $creditBtn.attr("data-click-state");
    $resultCard = $("#resultCard");
    $resultTitle = $("#resultTitle");
    $resultCon = $("#resultCon");
    $statusText = $("#statusText");

    $disableText1 = "请少侠明日再来领取";
    $disableText2 = "明日可领";

    if($creditBtnState==0){
        $creditBtn.addClass("disableBtn").html($disableText1);
        $statusText.html($disableText2);
    }



    $creditBtn.click(function(){
        if($creditBtnState==1){
            fetch(`/admin/user/userController.php?action=getDailyCredit`, {credentials:'same-origin'}).then(response => response.json()).then(json=>{
                if(json.code==1){
                    $resultTitle.html("领取成功");
                    $resultCon.html(json.message);
                    $resultCard.animate({top:70},800,"easeOutElastic");
                    $creditBtn.addClass("disableBtn").html($disableText1);
                    $statusText.html($disableText2);
                }else{
                    $resultTitle.html("领取失败");
                    $resultCon.html("你今日已经领取过了");
                    $resultCard.animate({top:70},800,"easeOutElastic");
                }
            })
        }
    })

    $closeBtn.click(function(){
        $resultCard.animate({top:-500},800,"easeOutElastic");
    })



})
