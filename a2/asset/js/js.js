/** * Created by Jerry on 2017-11-06.
 */

$(document).ready(function () {


    //#login page
    var $containerLogin = $("#containerLogin");
    if($containerLogin.length>0){
        //
        $userName = $("#containerLogin [name='username']");
        $password = $("#containerLogin [name='password']");
        $errorBox = $("#errorBox");
        $errorBoxCloseButton = $("#errorBox i");
        $submitButton = $("#submitButton");
        inputMessage($userName,"username");
        inputMessage($password,"password");

        $submitButton.click(function(){
            $.ajax({
                    method:"POST",
                    dataType:"json",
                    url:"http://www.atyorku.ca/a2/page/user/userController.php?action=loginWithJson",
                    data:{username:$userName.val(),password:$password.val()},
                })
                .done(function(data){
                    if(data.code==1){
                        window.location.href = "http://www.atyorku.ca/a2/page/frame/index.php";
                    }else{
                        $errorBox.children("span").html(data.message);
                        $errorBox.slideDown();

                        $errorBoxCloseButton.click(function(){
                            $(this).parent().slideUp();
                        });
                    }
                })
                .fail(function(error){
                    console.log(error.responseText);
                })
        })

    }

    //

    $(".protectedImg").each(function(){
        $(this).css({"background-image":"url("+$(this).attr("data-src")+")"});
    })


})


//custom functions
function inputMessage($element, txt) {
    var type = $element.attr("type");
    if (type == "password") {
        $element.attr("type", "text");
    }
    $element.val(txt);
    $element.focus(function () {
        if (type == "password") {
            $element.attr("type", "password");
        }
        if ($(this).val() == txt) {
            $(this).val("");
        }
    }).blur(function () {
        if ($(this).val() == "") {
            if (type == "password") {
                $element.attr("type", "text");
            }
            $(this).val(txt)
        }
    });
}