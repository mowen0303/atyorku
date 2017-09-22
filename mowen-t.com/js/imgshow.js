$(document).ready(function(e) {
	
//scrollBar
	var imgBoxHDBox_w = $(".imgBoxHDBox").width();
	var imgBoxHD = $("#imgBoxHD");
	var imgBoxBg = $(".imgBoxBg");
	var imgBoxBg_w = imgBoxBg.outerWidth(true);
	var imgNum = imgBoxBg.size();
	var imgBoxHD_w = imgBoxBg_w*imgNum;
	imgBoxHD.width(imgBoxHD_w);
	
	var scrollbarBox = $("#scrollbarBox");
	var scrollbarBox_w =scrollbarBox.width();
	var scrollbar = $("#scrollbar");		
	var scrollbar_w = scrollbar.width();
	
	var scrollbar_x = scrollbarBox_w-scrollbar_w;
	
	var scrollZt =0;
	var scrollZt2 = 0;
	var scrollZt3 = 0;
	var mx = 0;
	var mx2 =0;
	var mx3 =0;
	var bfb =0;
	
	
//
	var thrCon = $("#thrCon");
	

	
	var imgleft_i = $("#imgleft div");
	var imgright_i = $("#imgright div");
	//初始化
	$(".imgBox .imgtxt").fadeTo(0,0.7);
	if(imgBoxHD_w>imgBoxHDBox_w){imgright_i.addClass("hover");}
	imgBoxBg.hover(function(){
			
			$(this).find(".imghover").stop().fadeTo(100,0.5);
			$(this).find(".imgtxt").fadeTo(0,0.8).stop().animate({bottom:0},100,"easeOutQuad");
			$(this).find(".imghoverIcon").stop().animate({top:-20},300,"easeOutQuad");
			imgBoxHD.find(".cur").removeClass("cur");
			$(this).addClass("cur");
		
		},function(){
			
			$(this).find(".imghover").stop().fadeTo(100,0);
			$(this).find(".imgtxt").fadeTo(0,0.7).stop().animate({bottom:-25},100,"easeOutQuad");
			$(this).find(".imghoverIcon").stop().animate({top:-132},300,"easeOutQuad");
		})

	//鼠标移动事件
	$(document).mousemove(function(e){		
			mx = e.pageX;
			if(scrollZt==1)
			{
				scrollmove();	
			}
		}).mouseup(function(){scrollZt = 0;});
		
	//滚轮事件
	$(".imgBoxHdOut").mousewheel(function(event,delta){
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
			scrollbar.css({"left":+mx3+"px"});
			mx2 = mx;
			bfb = (imgBoxHD_w-imgBoxHDBox_w)*(mx3/scrollbar_x);	
			scroolbtn(bfb);					
			imgBoxHD.css({"left":+(-bfb)+"px"});
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
			imgBoxHD.stop().animate({left:-mx3},400);					
			scrollbar.stop().animate({left:bfb},400);	
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

