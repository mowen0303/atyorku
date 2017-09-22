// JavaScript Document
$(document).ready(function(){

/*TAB_function*/
	
	tabpalyNews("#tab1","dd","1");
	tabpalyNews("#tab2","dd","1");
		
	var indexAuto = 0;
	var tabTime = 200;
	
	//自动切换tab,不需要的话可以删掉此函数
	function autoPlayTab(id,tit,dh,time){
		//获取tab的标签个数
		var autoPlayTime = false;
		var tabTitNum = $(id+" .tab_tit:first "+tit).length;
		autofn();
		//每隔n秒自动播放
		function autofn(){
			autoPlayTime = setInterval(function(){
				indexAuto +=1; 
				if(indexAuto>=tabTitNum)
				{
					indexAuto =0;	
				}
				tabAct(id,tit,indexAuto,dh);},time)
		}		
		$(id).hover(function(){clearInterval(autoPlayTime)},function(){autofn();});	
	}
	
	//鼠标点击切换tab
	function tabpalyNews(id,tit,dh){	
		tabAct2(id,dh);
		$(id+" .tab_tit:first "+tit).each(function(index){				
			$(this).click(function(){
				
				if(!$(this).hasClass("hover"))
				{
				tabAct(id,tit,index,dh);
				indexAuto = index;
				}
			})				
		});			
	}
	
	//鼠标hover切换tab
	function tabpalyNewsHover(id,tit,dh){	
		tabAct2(id,dh);
		$(id+" .tab_tit:first "+tit).each(function(index){				
			$(this).mousemove(function(){
				tabAct(id,tit,index,dh);
				indexAuto = index;
			})				
		});			
	}
	
	//切换tab时候的动作 tabAct(id,tit,index,dh) hd=1的时候启动渐隐动画，h2=2的时候，双重渐隐，但是要启动css .tab_conin绝对定位，hd=0的时候不启用
	function tabAct(id,tit,index,dh){
		if(!$(".tab_conin").is(":animated"))
		{
			$(id+" .tab_tit:first .hover").removeClass("hover");
			$(id+" .tab_tit:first "+tit).eq(index).addClass("hover");
			if(dh==0)
			{				
				
				$(id+" .tab_con:first").children(".tab_conin.tabconshow").removeClass("tabconshow"); 
				$(id).children(".tab_con").children(".tab_conin").eq(index).addClass("tabconshow");
				
			}else if(dh==1)
			{
				$(id+" .tab_con:first").children(".tab_conin.tabconshow").css({"display":"none"}).removeClass("tabconshow"); //如果在切换时，要同时隐出和隐入，但无法自适应高度，如果想要自适应高度则将css({"display":"none"})该为fadeOut(500)，并将上面统一css解除注释				
				$(id).children(".tab_con").children(".tab_conin").eq(index).fadeIn(tabTime,function(){$(this).addClass("tabconshow")});
			}else if(dh==2)
			{
				$(id+" .tab_con:first").children(".tab_conin.tabconshow").fadeOut(500).removeClass("tabconshow");		
				$(id).children(".tab_con").children(".tab_conin").eq(index).fadeIn(500).addClass("tabconshow");
			}
		}
	}
	
	function tabAct2(id,dh){
		if(dh==2)
		{
			$(id+" .tab_con:first .tab_conin").css({"display":"none"});
			$(id+" .tab_con:first .tab_conin:first").css({"display":"block"});	
		}		
		
	}
		
	
	
})
