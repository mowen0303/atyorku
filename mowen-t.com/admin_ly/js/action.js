// 页面效果动作
$(document).ready(function(){
	
//------菜单效果	-----
	var mt = $(".menuTit span");
	var mc = $(".menuCon");
		
	mt.each(function(index){		
			mc.eq(0).slideDown();						
			//标题滑动
			mt.hover(
				function(){
					$(this).stop().animate({"padding-right":"35px"},200);	
				},
				function(){
					$(this).stop().animate({"padding-right":"25px"});			
				}
			);
			
			//菜单展开
			$(this).click(function(){
				mc.eq(index).slideToggle().siblings(".menuCon").slideUp();
				return false;
			});
	});
	
	
	
//------表格背景色-----
	$(".titleList tr:odd").addClass("oddtr");
	
	
	
})
