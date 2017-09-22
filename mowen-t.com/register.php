<?
session_start();
include_once("./global.php");

$nickname  = false;  
$gender = false;	
$work  = false;  
$name= false;
$phone   = false;   
$call   = false;    
$qq   = false;   
$date   = false; 
$dress   =false;   
$page   = "http://";

$act = $_SERVER['PHP_SELF'];

if(!empty($_GET['id']) && $db->authority('4'))
{
	$id=$_GET['id']	;
	$query = $db->select("extend_userinfo","*","id='$id'");
	$row = $db->fetch_array($query);
	$action = $act."?id=$id";
	
	$nickname  = $row['nickname'];  
	$gender = $row['gender'];	
	$work  = $row['work'];  
	$name= $row['name'];
	$phone   = $row['phone'];   
	$call   = $row['call'];    
	$qq   = $row['qq'];   
	$date   = $row['date']; 
	$dress   = $row['dress'];   
	$page   = $row['page'];
	
	
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>上海欧欧摄影俱乐部 - 会员注册表</title>
<link href="css/register.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<? include_once("config/congif_jq.php") ?>"></script>
<script type="text/javascript">
//<![CDATA[
function randcode(){
	var a=new Date();
	var b=a.getTime();
	$(".codeimg").attr("src","admin_ly/_code.php?"+b);	
}
$(function(){
       //如果是必填的，则加红星标识.
       $("form :input.required").each(function(){
           var $required = $("<strong class='high'> *</strong>"); //创建元素
           $(this).parent().append($required); //然后将它追加到文档中
       });
        //文本框失去焦点后
       $('form :input').blur(function(){
            var $parent = $(this).parent();
            $parent.find(".formtips").remove();
            //验证用户名
			var ithis = $(this);
            if( $(this).is('.required') ){
                   if( this.value==""){
                       $parent.append('<span class="formtips onError"></span>');
                   }
            }
            
       }).keyup(function(){
          $(this).triggerHandler("blur");
       }).focus(function(){
            $(this).find(".formtips").css({"display":"none"});
       });//end blur

       
       //提交，最终验证。
        $('#putBtn').click(function(){
		
			   $("form :input.required").trigger('blur'); 
               var numError = $('form .onError').length;
               if(numError){
                   return false;
               }
			   
			  
			   
			  
        });

       //重置
        $('#res').click(function(){
               $(".formtips").remove();
        });
})
//]]>
</script>
</head>


<body>
<div id="container">
  <form method="post" name="regform" onsubmit="return validate_form(this);">
    <div class="fmBoxTit">上海欧欧摄影俱乐部 - 会员注册表</div>
    <div class="fm imgListFm fmBoxCon">
    <div id="codeer"></div>
      <?
     	//点击提交按钮
		if(!empty($_POST['putBtn']))
		{
			
			if($_SESSION['code']==$_POST['code'])
			{
				$nickname  = $_POST['nickname'];  
				$gender = $_POST['gender'];	
				$work  = $_POST['work'];  
				$name= $_POST['name'];
				$phone   = $_POST['phone'];   
				$call   = $_POST['call'];    
				$qq   = $_POST['qq'];   
				 
				$dress   = $_POST['dress'];   
				$page   = $_POST['page'];
	
				//修改文章
				if(!empty($_GET['id']))
				{
					if($db->authority("3"))
					{
						$date   = $_POST['date'];
						$db->update("extend_userinfo","nickname='$nickname',gender='$gender',work='$work',name='$name',phone='$phone',`call`='111',qq='$qq',dress='$dress',page='$page'","id=$id");				
						echo "修改成功";	
					}else
					{
						echo "你无权修改";	
					}
				}
				else
				//添加新闻文章
				{
					$date   = $_POST['Date_Year']."-".$_POST['birthday_m']."-".$_POST['birthday_d'];
					$db->query("INSERT INTO `extend_userinfo` (`id` ,`nickname` ,`gender` ,`work` ,`name` ,`phone` ,`call` ,`qq` ,`date` ,`dress` ,`page`)VALUES (NULL ,  '$nickname',  '$gender',  '$work',  '$name',  '$phone',  '$call',  '$qq',  '$date',  '$dress',  '$page');"); 				
					$l_id  = $db->insert_id();      //获得插入的id
					echo "<div class='dlBox' align='center'>信息注册成功</div>";
				}
			} else
			{
				echo "<div class='dlBox' align='center'>验证码错误&nbsp;&nbsp;&nbsp;&nbsp;<a href='javascript:history.go(-1);'>返回</a></div></div>";		
			}
		}
		else
		//没有点击提交按钮
		{
	 ?>
      <? $db->echoMsg(); //错误信息提示 ?>
      <div class="dlBox">
      <div id="errorinfo"></div>
        <dl>
          <dt>昵称</dt>
          <dd>
            <input name="nickname" type="text" class="required" value="<? echo $nickname?>">
          </dd>
        </dl>
        <dl>
          <dt>性别</dt>
          <dt class="spanDl">
            <label>男</label>
            <input name="gender" type="radio" value="男" checked>
            <label>女</label>
            <input type="radio" name="gender" value="女">
          </dt>
        </dl>
        <dl>
          <dt>群内职业</dt>
          <dd>
            <select name="work" id="select">
<option value="摄影师" <? if(!empty($_GET['id'])){if($row['work']=="摄影师"){ echo " selected ";}}?>>摄影师</option>
              <option value="模特" <? if(!empty($_GET['id'])){if($row['work']=="模特"){ echo " selected ";}}?>>模特</option>
              <option value="模特兼摄影" <? if(!empty($_GET['id'])){if($row['work']=="模特兼摄影"){ echo " selected ";}}?>>模特兼摄影</option>
            </select>
          </dd>
        </dl>
        <dl>
          <dt>真实姓名</dt>
          <dd>
            <input name="name" type="text" class="required" value="<? echo $name?>" />
          </dd>
        </dl>
        <dl>
          <dt>手机</dt>
          <dd>
            <input name="phone" type="text" class="required" value="<? echo $phone?>" />
          </dd>
        </dl>
        <dl>
          <dt>固话</dt>
          <dd>
            <input name="call" type="text" value="<? echo $call?>" />
          </dd>
        </dl>
        <dl>
          <dt>QQ</dt>
          <dd>
            <input name="qq" type="text" class="required" value="<? echo $qq?>" />
          </dd>
        </dl>
        <dl>
          <dt>出生日期</dt>
          <?
          	if(!empty($_GET['id']))
			{
		  ?>
           		<dd><input name="date" type="text" class="required" value="<? echo $date?>" /></dd>
           <?
			}
			else
			{
		  ?>
          <dd>
            <select name="Date_Year">
              <option value="1"> </option>
              <option value="2011" label="2011">2011</option>
              <option value="2010" label="2010">2010</option>
              <option value="2009" label="2009">2009</option>
              <option value="2008" label="2008">2008</option>
              <option value="2007" label="2007">2007</option>
              <option value="2006" label="2006">2006</option>
              <option value="2005" label="2005">2005</option>
              <option value="2004" label="2004">2004</option>
              <option value="2003" label="2003">2003</option>
              <option value="2002" label="2002">2002</option>
              <option value="2001" label="2001">2001</option>
              <option value="2000" label="2000">2000</option>
              <option value="1999" label="1999">1999</option>
              <option value="1998" label="1998">1998</option>
              <option value="1997" label="1997">1997</option>
              <option value="1996" label="1996">1996</option>
              <option value="1995" label="1995">1995</option>
              <option value="1994" label="1994">1994</option>
              <option value="1993" label="1993">1993</option>
              <option value="1992" label="1992">1992</option>
              <option value="1991" label="1991">1991</option>
              <option value="1990" label="1990">1990</option>
              <option value="1989" label="1989">1989</option>
              <option selected="" value="1988" label="1988">1988</option>
              <option value="1987" label="1987">1987</option>
              <option value="1986" label="1986">1986</option>
              <option value="1985" label="1985">1985</option>
              <option value="1984" label="1984">1984</option>
              <option value="1983" label="1983">1983</option>
              <option value="1982" label="1982">1982</option>
              <option value="1981" label="1981">1981</option>
              <option value="1980" label="1980">1980</option>
              <option value="1979" label="1979">1979</option>
              <option value="1978" label="1978">1978</option>
              <option value="1977" label="1977">1977</option>
              <option value="1976" label="1976">1976</option>
              <option value="1975" label="1975">1975</option>
              <option value="1974" label="1974">1974</option>
              <option value="1973" label="1973">1973</option>
              <option value="1972" label="1972">1972</option>
              <option value="1971" label="1971">1971</option>
              <option value="1970" label="1970">1970</option>
              <option value="1969" label="1969">1969</option>
              <option value="1968" label="1968">1968</option>
              <option value="1967" label="1967">1967</option>
              <option value="1966" label="1966">1966</option>
              <option value="1965" label="1965">1965</option>
              <option value="1964" label="1964">1964</option>
              <option value="1963" label="1963">1963</option>
              <option value="1962" label="1962">1962</option>
              <option value="1961" label="1961">1961</option>
              <option value="1960" label="1960">1960</option>
              <option value="1959" label="1959">1959</option>
              <option value="1958" label="1958">1958</option>
              <option value="1957" label="1957">1957</option>
              <option value="1956" label="1956">1956</option>
              <option value="1955" label="1955">1955</option>
              <option value="1954" label="1954">1954</option>
              <option value="1953" label="1953">1953</option>
              <option value="1952" label="1952">1952</option>
              <option value="1951" label="1951">1951</option>
              <option value="1950" label="1950">1950</option>
              <option value="1949" label="1949">1949</option>
              <option value="1948" label="1948">1948</option>
              <option value="1947" label="1947">1947</option>
              <option value="1946" label="1946">1946</option>
              <option value="1945" label="1945">1945</option>
              <option value="1944" label="1944">1944</option>
              <option value="1943" label="1943">1943</option>
              <option value="1942" label="1942">1942</option>
              <option value="1941" label="1941">1941</option>
              <option value="1940" label="1940">1940</option>
              <option value="1939" label="1939">1939</option>
              <option value="1938" label="1938">1938</option>
              <option value="1937" label="1937">1937</option>
              <option value="1936" label="1936">1936</option>
              <option value="1935" label="1935">1935</option>
              <option value="1934" label="1934">1934</option>
              <option value="1933" label="1933">1933</option>
              <option value="1932" label="1932">1932</option>
              <option value="1931" label="1931">1931</option>
              <option value="1930" label="1930">1930</option>
              <option value="1929" label="1929">1929</option>
              <option value="1928" label="1928">1928</option>
              <option value="1927" label="1927">1927</option>
              <option value="1926" label="1926">1926</option>
              <option value="1925" label="1925">1925</option>
              <option value="1924" label="1924">1924</option>
              <option value="1923" label="1923">1923</option>
              <option value="1922" label="1922">1922</option>
              <option value="1921" label="1921">1921</option>
              <option value="1920" label="1920">1920</option>
              <option value="1919" label="1919">1919</option>
              <option value="1918" label="1918">1918</option>
              <option value="1917" label="1917">1917</option>
              <option value="1916" label="1916">1916</option>
              <option value="1915" label="1915">1915</option>
              <option value="1914" label="1914">1914</option>
              <option value="1913" label="1913">1913</option>
              <option value="1912" label="1912">1912</option>
              <option value="1911" label="1911">1911</option>
              <option value="1910" label="1910">1910</option>
              <option value="1909" label="1909">1909</option>
              <option value="1908" label="1908">1908</option>
              <option value="1907" label="1907">1907</option>
              <option value="1906" label="1906">1906</option>
              <option value="1905" label="1905">1905</option>
              <option value="1904" label="1904">1904</option>
              <option value="1903" label="1903">1903</option>
              <option value="1902" label="1902">1902</option>
              <option value="1901" label="1901">1901</option>
              <option value="1900" label="1900">1900</option>
            </select>
            年
            <select init_value="11" name="birthday_m">
              <option value="0"></option>
              <option value="01" selected>01</option>
              <option value="02">02</option>
              <option value="03">03</option>
              <option value="04">04</option>
              <option value="05">05</option>
              <option value="06">06</option>
              <option value="07">07</option>
              <option value="08">08</option>
              <option value="09">09</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
            </select>
            月
            <select init_value="18" value="18" name="birthday_d">
              <option value="0"></option>
              <option value="01" selected>01</option>
              <option value="02">02</option>
              <option value="03">03</option>
              <option value="04">04</option>
              <option value="05">05</option>
              <option value="06">06</option>
              <option value="07">07</option>
              <option value="08">08</option>
              <option value="09">09</option>
              <option value="10">10</option>
              <option value="11">11</option>
              <option value="12">12</option>
              <option value="13">13</option>
              <option value="14">14</option>
              <option value="15">15</option>
              <option value="16">16</option>
              <option value="17">17</option>
              <option value="18">18</option>
              <option value="19">19</option>
              <option value="20">20</option>
              <option value="21">21</option>
              <option value="22">22</option>
              <option value="23">23</option>
              <option value="24">24</option>
              <option value="25">25</option>
              <option value="26">26</option>
              <option value="27">27</option>
              <option value="28">28</option>
              <option value="29">29</option>
              <option value="30">30</option>
            </select>
            日</dd>
            <?}?>
        </dl>
        <dl>
          <dt>住址</dt>
          <dd>
            <input name="dress" type="text" class="required" value="<? echo $dress?>" />
          </dd>
        </dl>
        <dl>
          <dt>个人网站</dt>
          <dd>
            <input name="page" type="text" value="<? echo $page?>" />
          </dd>
        </dl>
        <dl class="code">
          <dt>验证码</dt>
          <dd>
            <input name="code" id="code" class="code required" type="text" value="" />
            <img class="codeimg" src="admin_ly/_code.php" onClick="randcode();" title="看不清，换一张"></dd>
        </dl>
      </div>
      <div class="fmBtn">
        <input name="putBtn" id="putBtn" type="submit" class="resBtn" value="提   交" />
      </div>
      <?
		}
		?>
    </div>
  </form>
</div>
</body>
</html>
