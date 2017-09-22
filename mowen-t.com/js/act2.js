$(document).ready(function() {
//////////////////////////////////////
//                                  //
//        最新作品部分               //
//                                  //
//////////////////////////////////////
/*	
	框架结构:
	   <div id="showimg_box">                 //显示位置大框架
		  <div id="animate_contaienr">        //显示区域框架
			<div id="animate_box">            //滑动框架
			  <div class="imgBox"><img></div> //滑动内容
			  <div class="imgBox"><img></div>
			  <div class="imgBox"><img></div>
			  <div class="imgBox"><img></div>
			</div>
		  </div>
		  <div id="left" class="imgBtn" title="可按键盘方向键:左"></div>
		  <div id="right" class="imgBtn" title="可按键盘方向键:右"></div>
		  <div class="imgBar"></div>
	   </div>

*/
/****定义一些变量****/

   //可以修改部分
   var photoBox = $("#animate_box");              //滑动框架节点
   var photoBar = $(".imgBar");                   //指针框架节点
   var imgBox   = photoBox.children(".imgBox");   //图片内容DIV节点
   var curClass = "cur";                          //指针指向位置的样式
   var isteepG = 1200;                            //定义滑动速度
   var isteepB  = 600;                            //设置回转滑动速度
   var apSteep  = 5000;                           //自动播放间隔
   //以下部分不能修改
   var imgNum   = imgBox.length;                  //获得图片个数
   var imgWidth = imgBox.innerWidth();            //获得图片容器宽度
   var cur      = 0;                              //定义指针起始数值,不要修改
   var playT    = false;                         //定义自动播放变量
   var imgsteep  = false;                         //定义函数使用的滑动速度



/****初始化执行一些内容****/

	//初始化指针
	for(i=0;i<imgNum;i++)
	{
   		$("<div></div>").appendTo(photoBar);
	}
   photoBar.children("div").first().addClass(curClass);

   //初始划滑动容器宽度
   photoBox.css({"width":+(imgWidth*imgNum)+"px"});


/****事件触发****/

   //自动播放
   autoPlay();
   //鼠标移到滑动框上,清除自动播放
   imgBox.hover(function(){clearAutoPlay("stop")},function(){clearAutoPlay("play")});
   //点击向右按钮触发,右滑动,鼠标放上清除自动播放
   $("#right").click(function(){animatePlay("right");}).hover(function(){clearAutoPlay("stop")},function(){clearAutoPlay("play")});
   //点击向左按钮触发,左滑动,鼠标放上清除自动播放
   $("#left").click(function(){animatePlay("left");}).hover(function(){clearAutoPlay("stop")},function(){clearAutoPlay("play")});
   //按键上左右键触发,左37 右39
   $(document).keydown(function(event){
   		event.which == "37" ? animatePlay("left") : false;
		event.which == "39" ? animatePlay("right") : false;
   })

/****一些自定义函数****/

   /**
    * 滑动函数
	* 需要写入当前指针的值
	*/
   function photoHD(v){
	 photoBox.animate({"left":+(-imgWidth*v)+"px"},imgsteep);
	 photoBar.children("."+curClass).removeClass(curClass);
	 photoBar.children("div").eq(v).addClass(curClass);
   }

   /**
    * 当滑动框架处于非动画状态时执行
	*/
   function animatePlay (v){
	   if(!photoBox.is(":animated"))
	   {
		   //向右
		   if(v == "right")
		   {
			   if(cur>=imgNum-1){
					cur=-1;
			    	imgsteep = isteepB;
			   }else{
				    imgsteep = isteepG;
			   }
			   cur++;
			   photoHD(cur);
		   }
		   //向左
		   if(v == "left")
		   {
			  if(cur <= 0){
				  cur=imgNum;
				  imgsteep = isteepB;
			  }else{
				  imgsteep = isteepG;
			  }
			  cur--;
			  photoHD(cur);
		   }
	  }
  }
  /**
    * 自动播放
	*/

	function autoPlay(){
	  	 playT = setInterval(function(){animatePlay("right");},apSteep);
	}

  /**
    * 清除自动播放
	*/
   function clearAutoPlay(v){
	   v=="stop" ? clearInterval(playT) : false;
	   v=="play" ? autoPlay() : false;
   }



///////////////////////////////////////
//                                   //
//        最新动态部分                //
//                                  //
//////////////////////////////////////

/****定义一些变量****/

	var newsTit   = $(".newsInfo_Ctit");   //标题框
	var newsCon   = $(".newsInfo_con");    //内容框
	var newsSteep = 400;                   //新闻切换速度
	var newsClass = "cur";                 //标题当前hover状态class
	var newsTit_i = "open";                //标题前ICON Class
	var newsCon_h = newsCon.height();      //新闻内容高度
	
	//***初始化框架***
	
	//第一个内容框以外的框架高度全部设置为0
	newsCon.first().nextAll(".newsInfo_con").css({"height":"0px","opacity":"0"});
	newsTit.eq(0).addClass("cur");
	newsTit.children("span").eq(0).addClass("open");
	
	//为每一个标题绑定一个事件
	newsTit.each(function(index) {        
		//点击触发
		$(this).click(function(){
			//当前内容框没有进行动画,则执行
			if(!newsCon.is(":animated"))
			{
				//前标题不是展开状态则执行
				if(!$(this).hasClass(newsClass))
				{	
					//移除标题栏站状态的class和子节点icon的css			
					newsTit.siblings("."+newsClass).removeClass(newsClass).children("."+newsTit_i).removeClass(newsTit_i);
					//为当前点击的标题栏添加展开状态class
					$(this).toggleClass(newsClass).children("span").addClass(newsTit_i);
					//展开状态的内容框架收缩
					newsCon.animate({"height":"0px","opacity":"0"},newsSteep).dequeue();
					//使点击标题栏的内容框架展开
				    newsCon.eq(index).animate({"height":+newsCon_h+"px"},newsSteep).dequeue().animate({"opacity":"1"},newsSteep+300);
				}
			}	
		})	
    });

//////////////////////////////////////
//                                  //
//        最新动态下卡片              //
//                                  //
//////////////////////////////////////	
	
	//***定义一些变量***
	var infoCard    = $("#myinfo");  //卡片框架节点
	var infoSteep   = 400;           //动画速度
	var infoTimeout = 40000000;           //动作延迟
	var infoT       = false;
	
	
	//***事件动作***
	$("#myinfoAct").hover(function(){myinfo("out")},function(){myinfo("in")});
	infoCard.hover(function(){infoT = setTimeout(function(){myinfo("out")},infoTimeout)},function(){clearTimeout(infoT);myinfo("in")});
	
	//***自定义函数***
	function myinfo(act){
		if(act == "out")
		{             
			infoCard.stop().animate({"left":"500px"},infoSteep);	
		}
		if(act=="in")
		{
			infoCard.stop().animate({"left":"160px"},infoSteep);	
		}
	}
	
	
//////////////////////////////////////
//                                  //
//        menu菜单                  //
//                                  //
//////////////////////////////////////	
	var mt = $(".menuTit span");

		
	mt.each(function(index){						
			//标题滑动
			mt.hover(
				function(){
					$(this).stop().animate({"padding-right":"35px"},200);	
				},
				function(){
					$(this).stop().animate({"padding-right":"25px"});			
				}
			);
	});


//////////////////////////////////////
//                                  //
//        menu2菜单                  //
//                                  //
//////////////////////////////////////	

	var m2Bg     = $(".menu2_bg");    //menu2 背景滑块 节点
	var m2       = $(".menu2_Txt");   //menu2 菜单Txt
	var m2Bg_x   = false;            //menu2 背景滑块 位置
	var m2_steep = 300;               //背景滑块滑动速度
	var m2_time  = 700;               //背景滑块自动回位时间
	var m2Bg_x_e = false;
	var url      = document.location.href;
	//m2Bg_x_e=m2Bg_x_e.replace(/px/,"");	//去掉css属性left的px字符
	
	//延迟处理
	var m2_setT_o= false;            //悬停延迟
	var m2_setT_i= false;            //移开延迟
	
	menuCur("news","mene2_2");
	menuCur("opus","mene2_3");

	
	m2Bg.css({"left":+m2Bg_x_e+"px"}).fadeIn("slow");

	
	
	m2.each(function(index) {	
		
		//鼠标hover的时候
		$(this).hover(function()
				{
					//清除鼠标移除时的延迟
					clearTimeout(m2_setT_o);
					//获取当前hover状态menu2的left值
					m2Bg_x = $(this).css("left");
					//延迟处理	
					m2_setT_i = setTimeout(function(){			
								//去掉css属性left的px字符
								m2Bg_x = m2Bg_x.replace(/px/,"");
								//执行动画
								m2Bg.stop().animate({"left":+m2Bg_x+""},m2_steep);
								},200)
						
			    },						
			    function(){				  
					clearTimeout(m2_setT_i);  
					m2_setT_o = setTimeout(function(){m2Bg.stop().animate({"left":+m2Bg_x_e+""},m2_steep);},m2_time);				
			    });
    });
	
	function menuCur(name,dom){
		if(url.indexOf(name)>0)
		{
			m2Bg_x_e = $("."+dom).css("left");
			m2Bg_x_e = m2Bg_x_e.replace(/px/,"");
		}
	
	}

//////////////////////////////////////
//                                  //
//        test                      //
//                                  //
//////////////////////////////////////	



});