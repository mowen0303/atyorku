$(document).ready(function(e) {
		
	var imgBoxHDBox_w = win_h-80;
	var aboutBox = $("#aboutBox").height(imgBoxHDBox_w);
	var imgBoxHD = $("#mainLine_HD");
	var imgBoxBg = $(".tlist");
	var imgBoxBg_w = 100;
	
	var scrollbarBox = $("#scrollbarBox");
	var scrollbarBox_w =scrollbarBox.height();
	var scrollbar = $("#scrollbar");		
	var scrollbar_w = scrollbar.height();
	
	var scrollbar_x = scrollbarBox_w-scrollbar_w;
	
	var scrollZt =0;
	var scrollZt2 = 0;
	var scrollZt3 = 0;
	var mx = 0;
	var mx2 =0;
	var mx3 =0;
	var bfb =0;
	var imgBoxHD_w = 0;

	leftAndRightCon();
	//左右布局
	function leftAndRightCon()
	{
		var msgBoxHL = 0 ;
		var msgBoxHR = 320 ;
		
		$(".tlist").each(function(index, element) {
			if($(this).hasClass("start") && index==0)
			{
				$(this).css({"top":+msgBoxHL+"px"});
				msgBoxHL += $(this).height()+80;
				
			}else if($(this).hasClass("start"))
			{
				if($(this).prev(".tlist").hasClass("msgBox_l"))
				{
					$(this).css({"top":+msgBoxHL+"px"});
					msgBoxHL += $(this).height()+80;
					msgBoxHR = msgBoxHL;
					msgBoxHL += 250; 
				}else
				{
					$(this).css({"top":+msgBoxHR+"px"});
					msgBoxHR += $(this).height()+80;
					msgBoxHL = msgBoxHR;
					msgBoxHR += 250;
				}
			}else if($(this).hasClass("msgBox_l"))
			{
				$(this).css({"top":+msgBoxHL+"px"});
				msgBoxHL += $(this).height()+80;
			}else if($(this).hasClass("msgBox_r"))
			{
				$(this).css({"top":+msgBoxHR+"px"});
				msgBoxHR += $(this).height()+80;
			}
				
		});	
		imgBoxHD_w = msgBoxHL+300;
		imgBoxHD.height(imgBoxHD_w);
	}
	
	
	//右侧布局
	function rightCon()
	{
		var tlist_y = 0;
		var tlist = $(".tlist");
		tlist.each(function(index, element) {
				
				$(this).css({"top":+tlist_y+"px"});
				tlist_y += $(this).height()+40;				
		});
		
		imgBoxHD_w = tlist_y+300;
		imgBoxHD.height(imgBoxHD_w);
	}
	
	var taps1 = $(".taps1");
	var taps2 = $(".taps2");
	
	taps1.click(function(){
		$(".tlist").each(function(index, element) {            
			if($(this).hasClass("msgBox_t"))
			{
				$(this).removeClass("msgBox_t").removeClass("msgBox_r").addClass("msgBox_l");
				$("#mainLine_HD").css({"left":"50%"});
			}
        });
		taps2.removeClass("hover");
		$(this).addClass("hover");		
		
		leftAndRightCon();
	});
	
	taps2.click(function(){
		$(".tlist").each(function(index, element) {            
			if($(this).hasClass("msgBox_l"))
			{
				$(this).removeClass("msgBox_l").addClass("msgBox_r msgBox_t");
				$("#mainLine_HD").css({"left":"40%"});
			}
        });
		taps1.removeClass("hover");
		$(this).addClass("hover");	
		
		rightCon();	
		
		
	});

	
//scrollBar
	
	
	
//

	var thrCon = $("#thrCon");
	var imgleft_i = $("#imgleft div");
	var imgright_i = $("#imgright div");

	//鼠标移动事件
	$(document).mousemove(function(e){		
			mx = e.pageY;
			if(scrollZt==1)
			{
				scrollmove();	
			}
		}).mouseup(function(){scrollZt = 0;});
		
	//滚轮事件
	aboutBox.mousewheel(function(event,delta){
		if(scrollZt3==1)
		{
			mx3 = bfb;
			scrollZt3=0;
		}
		delta>0?mx3 -=imgBoxBg_w:mx3 +=imgBoxBg_w;		
		scroollmove2();
		return false; 
	});
	//按钮事件
	
	$("#imgleft").click(function(){
		if(scrollZt3==1)
		{
			mx3 = bfb;
			scrollZt3=0;
		}
		mx3-=imgBoxBg_w;
		scroollmove2();	
		
			
	});
	$("#imgright").click(function(){
		if(scrollZt3==1)
		{
			mx3 = bfb;
			scrollZt3=0;
		}
		
		mx3+=imgBoxBg_w;
		scroollmove2();
		
		
		
	});
	//拖动事件
	scrollbar.mousedown(function(){
		scrollZt = 1;
		mx2 = mx;
		if(scrollZt2 == 1)
		{
			mx3 = bfb;
			scrollZt2 =0;
		}
		
	})	;
	//滚动条控制图像滑动方法
	function scrollmove(){			
			mx3+=(mx-mx2);				
			mx3<=0?mx3=0:false;
			mx3>=scrollbar_x?mx3=scrollbar_x:false;
			scrollbar.css({"top":+mx3+"px"});
			mx2 = mx;
			bfb = (imgBoxHD_w-imgBoxHDBox_w)*(mx3/scrollbar_x);	
			scroolbtn(bfb);					
			imgBoxHD.stop().animate({top:-bfb+100},700);
			gdx = 	mx3;	
			scrollZt3 = 1;
			//$("#say").html(mx3);			
		}
	//滚轮和按钮控制图像和滚动条滑动方法
	function scroollmove2(){	
			mx3<=5?mx3=0:false;
			mx3>=(imgBoxHD_w-imgBoxHDBox_w-5)?mx3=(imgBoxHD_w-imgBoxHDBox_w):false;	
			scroolbtn(mx3);					
			bfb = (scrollbarBox_w-scrollbar_w)*(mx3/(imgBoxHD_w-imgBoxHDBox_w));	
			imgBoxHD.stop().animate({top:-mx3+100},700);					
			scrollbar.stop().animate({top:bfb},300);	
			scrollZt2 =1;
			//$("#say").html(mx3);
		}
		
	function scroolbtn(v){
		
		if(v==0){
				imgleft_i.removeClass("hover");
				
			}else if(v==imgBoxHD_w-imgBoxHDBox_w){
				imgright_i.removeClass("hover");
			}else
			{
				imgleft_i.addClass("hover");
				imgright_i.addClass("hover");
			}
		
	}

	
	
	
	
	
	
	
	
	
	
	
	
});




//滚轮插件
(function($){var types=['DOMMouseScroll','mousewheel'];if($.event.fixHooks){for(var i=types.length;i;){$.event.fixHooks[types[--i]]=$.event.mouseHooks}}$.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var i=types.length;i;){this.addEventListener(types[--i],handler,false)}}else{this.onmousewheel=handler}},teardown:function(){if(this.removeEventListener){for(var i=types.length;i;){this.removeEventListener(types[--i],handler,false)}}else{this.onmousewheel=null}}};$.fn.extend({mousewheel:function(fn){return fn?this.bind("mousewheel",fn):this.trigger("mousewheel")},unmousewheel:function(fn){return this.unbind("mousewheel",fn)}});function handler(event){var orgEvent=event||window.event,args=[].slice.call(arguments,1),delta=0,returnValue=true,deltaX=0,deltaY=0;event=$.event.fix(orgEvent);event.type="mousewheel";if(orgEvent.wheelDelta){delta=orgEvent.wheelDelta/120}if(orgEvent.detail){delta=-orgEvent.detail/3}deltaY=delta;if(orgEvent.axis!==undefined&&orgEvent.axis===orgEvent.HORIZONTAL_AXIS){deltaY=0;deltaX=-1*delta}if(orgEvent.wheelDeltaY!==undefined){deltaY=orgEvent.wheelDeltaY/120}if(orgEvent.wheelDeltaX!==undefined){deltaX=-1*orgEvent.wheelDeltaX/120}args.unshift(event,delta,deltaX,deltaY);return($.event.dispatch||$.event.handle).apply(this,args)}})(jQuery);

