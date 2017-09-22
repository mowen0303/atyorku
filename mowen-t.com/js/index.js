$(document).ready(function(e) {
    var container = $("#container");
    step = $(".step").width(win_w).height(win_h);
    var stepBox = $("#stepBox");
    var zt = 0;
    var slogin1 = $("#slogin1");
    var slogin1_w = slogin1.width();
    var slogin1_h = slogin1.height();
    var slogin2 = $("#slogin2");
    var slogin2_w = slogin2.width();
    var slogin2_h = slogin2.height();
    var slogin3 = $("#slogin3");
    var slogin3_w = slogin3.width();
    var slogin3_h = slogin3.height();
    var bgimgBox = $("#bgimg_box");
	var slogin_line1 = $("#slogin_line1").fadeTo(0,0.2);
	var slogin_line2 = $("#slogin_line2").fadeTo(0,0.2);
	
    var bgimg = new Image();
    bgimg.onload = function() {
        $(this).width("100%").height("auto").prependTo(bgimgBox);
        $("#loadingtxt").hide();
        bodyLoad.removeClass("loading").stop().animate({
            width: 0
        },
        1000, "easeOutQuint",
        function() {
            var menuBg = $("#menuBg").stop().animate({
                width: win_w
            },
            400, "easeInQuart",
            function() {
                var menucur = $("#menucur").fadeTo(0, 1);
                var menuBox = $("#menuBox").stop().animate({
                    top: 0
                },
                400, "easeOutQuint",
                function() {
                    slogin1.stop().animate({
                        left: win_w * 0.5
                    },
                    200, "easeOutQuart",
                    function() {
                        slogin2.stop().animate({
                            left: win_w * 0.5
                        },
                        200, "easeOutQuart",
                        function() {
                            slogin3.stop().animate({
                                top: 500
                            },
                            600, "easeOutQuart",
                            function() {
                                var mousex;
                                var newx = 0;
								var newy = 0;
                                var movex;
                                $(document).mousemove(function(e) {
                                    mousex = e.pageX;
                                    mousey = e.pageY;
									slogin_line1.css({"left":+mousex+"px"});
									slogin_line2.css({"top":+mousey+"px"});
                                    if (Math.abs(mousex - newx) > 50 || Math.abs(mousey - newy) > 50 ) {
                                        movey2_1 = (win_h) * 0.5 - slogin1_h * 0.5 - (mousey - win_h * 0.5) * 0.6;
                                        movex2_1 = (win_w) * 0.5 - (mousex - win_w * 0.5) * 0.1;
                                        slogin1.stop().animate({left: movex2_1,top: movey2_1},700);
                                        movey2_2 = (win_h) * 0.5 - slogin2_h * 0.5 - (mousey - win_h * 0.5) * 0.1;
                                        movex2_2 = (win_w) * 0.5 - (mousex - win_w * 0.5) * 0.3;
                                        slogin2.stop().animate({
                                            left: movex2_2,
                                            top: movey2_2
                                        },
                                        700);
                                        movey2_3 = (win_h) * 0.5 - slogin3_h * 0.5 + (mousey - win_h * 0.5) * 0.4;
                                        movex2_3 = (win_w) * 0.5 + (mousex - win_w * 0.5) * 0.5;
                                        slogin3.stop().animate({
                                            left: movex2_3,
                                            top: movey2_3
                                        },
                                        700);				
                                        newx = mousex;
										newy = mousey;
                                    }
									
                                })
                            })
                        })
                    })
                })
            })
        })
    }
	bgimg.src = "http://cdn1.zygames.com/quote/mvcom/img/bg1-1.jpg";
});