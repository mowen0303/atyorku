// JavaScript Document
$(document).ready(function(){
	
	
	
})









/*---------外-----------*/

//验证码
function randcode(){
	var a=new Date();
	var b=a.getTime();
	$(".codeimg").attr("src","_code.php?"+b);	
}