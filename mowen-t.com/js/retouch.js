$(document).ready(function(e) {
var bgimgBox = $("#bgimg_box");
var bgimg = new Image();
bgimg.onload=function(){
	$(this).width("100%").height("auto").prependTo(bgimgBox);		
	//loading
	$("#loadingtxt").hide();
	bodyLoad.removeClass("loading").stop().animate({width:0},800,"easeOutQuint",function(){		
		var m_a=0;
		win_h<800?m_a=50:m_a=0;
		$(".secondCon").stop().animate({bottom:-m_a},400,"easeOutQuad");	
	});
}
bgimg.src = "http://cdn1.zygames.com/quote/mvcom/img/bg1-3.jpg";	
});

