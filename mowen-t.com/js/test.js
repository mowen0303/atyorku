// JavaScript Document
$(document).ready(function(){
	
	setInterval(function(){going()},500)
	function going(){
		var newsTit = $(".newsTit").html();
		if(newsTit == "网站开发日志.")
		{
			$(".newsTit").append(".");
		}else if(newsTit == "网站开发日志..")
		{
			$(".newsTit").append(".");
		}else if(newsTit == "网站开发日志...")
		{
			$(".newsTit").append(".");
		}else if(newsTit == "网站开发日志....")
		{
			$(".newsTit").html("网站开发日志.");
		}		
	}
	
	var a = new Array();
	var i=0;	
	$(document).keydown(function(event){			
			a[i] = event.which;		
			i++;			
			txt = a.toString();
			$(".num").html(txt);			
			if(txt.indexOf("38,38,40,40,37,39,37,39,66,65")>=0)  //
			{
				
				hd();				
				a=new Array();
				i=0;
			}			
		})  
	
	function hd() {
		
		var pup = $(".pup");
		if (!pup.is(":animated")){		
		    
			$(".lb").load("indexsound.html");
					
			var speet = 3000;
			var wdWidth = $(window).width();
			var wdHeight = $(window).height()+$(document).scrollTop();		
			
			var bgcH = $("html").height() ;
			var bgcH2 = $(window).height();
			if(bgcH < $("body").height())
			{
				bgcH = $("body").height();
			}
			if(bgcH < bgcH2)
			{
					bgcH = bgcH2;					
			}

			
			var bgcW = $("body").width() ;
			
			
			var pupWidth = pup.width();
			var pupHeight = pup.height();
			
			var pupLeft = parseInt((wdWidth -pupWidth)/2);
			var pupTop = parseInt((wdHeight -pupHeight)/2);
			
		pup.css({"left":"0px"});
		
		
		$("#bgC").css({"width":wdWidth+"px","height":bgcH+"px"}).fadeTo(0,0.8);
		
		//pup.css({"left":pupLeft+"px","top":pupTop+"px"})		
		//alert (pupTop)		
		pupLeft = pupLeft-45;	
		pup.css({"top":pupTop+"px"}).show().animate({"left":+pupLeft+"px"},speet);
		}
	}
	
	$(".close").click(function(){closePup()});

	
	
})

/*ready() END*/


function closePup(){		
		$(".pup").fadeOut();
		$("#bgC").fadeOut();
		$(".ld").html(" ");
	}