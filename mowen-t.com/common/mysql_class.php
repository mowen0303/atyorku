<?
class mysql {
	private $db_host;     //数据库主机地址
	private $db_user;     //数据库用户名
	private $db_pwd;      //数据库密码
	private $db_datebase; //数据库名
	private $coding;      //数据库编码,GBK,UTF8,gb2312
	private $sql;         //msylq语句
	private $sqlerror;    //mysql的错误信息
    public  $msg=false;   //提示到页面的错误信息
	
	/*初始化*/
	function __construct($db_host,$db_user,$db_pwd,$db_datebase,$coding){		
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_pwd = $db_pwd;
		$this->db_datebase = $db_datebase;
		$this->coding = $coding;
		$this->connect();		
	}
	
	
	/*数据库连接*/
	function connect(){		
		$conn = @mysql_connect($this->db_host,$this->db_user,$this->db_pwd) or die ("连接数据库出错，请检查：主机地址、用户名、密码");
		@mysql_select_db($this->db_datebase,$conn) or die ("未找到数据库:".$this->db_datebase);
		mysql_query("SET NAMES $this->coding");
	}
	
	/*query  语句*/
	public function query($sql){	
	
		$this->sql = $sql;	
		$result = mysql_query($this->sql);
		if ($result)
		{
			return $result;
		}else
		{
			echo "query出错".mysql_error();
			exit();
		}
	}
	
	/*fetch_array 语句*/
	function fetch_array($v){
		return mysql_fetch_array($v);
	}
	
	/*insert 语句简化  insert(表名，字段名，值)*/
	function insert($table,$zd='now()',$v='0'){
		return $this->query("INSERT INTO $table ($zd) VALUES ($v)");		
	}	
	
	/*insert_id 返回上一次执行insert语句的id*/
	function insert_id(){		
		return mysql_insert_id();	
	}
	
	/*num_rows() 返回查询结果中所得条数*/
	function num_rows($v){
		return mysql_num_rows($v);	
	}
	
	/*select(表名，字段，条件，是否输出sql语句)*/
	function select($table,$tit="*",$condition="",$debug=""){
		if($condition)
		{
			$condition = "WHERE ".$condition;
		}else
		{
			$condition = NULL;	
		}
		if($debug)
		{
			echo "SELECT $tit FROM $table $condition";
		}else
		{
			return $this->query("SELECT $tit FROM $table $condition");
		}
	}
	
	/*update(表名，字段='值'，条件) */
	function update($t,$v,$w,$echo=""){		
		if($echo)
		{			
			echo "UPDATE $t SET $v WHERE $w";
		}else
		{
			return $this->query("UPDATE $t SET $v WHERE $w");	
		}
		
	}
	
	/*DELETE FROM `imgclass` WHERE `imgclass`.`id` = 7*/
	function delete($t,$w){
		return $this->query("DELETE FROM $t WHERE $t.$w");
	}
	
	/*user_login 用户_验证用户名密码*/
	function user_login($name,$pw,$code){		
		if(empty($name) || empty($pw) || empty($code))
		{
			$this->msg = "请填写 '用户名' '密码' '验证码'";
		}else
		{
			if($_SESSION['code'] == $code)
			{
				$name = str_replace(" ","",$name);
				$query = $this->select("admin","*","name='$name'");
				$a = is_array($row = $this->fetch_array($query)); //查询是否有此用户
				if($a)
				{
					if($row['pw']==md5($pw))
					{
							$_SESSION['uname']=$row['name']; //将用户名，设置成session uname
							$_SESSION['authority'] = $row['authority'];
							$_SESSION['utime'] =mktime();
							$this->jump("main.php");
					}else
					{
						$this->msg = "密码错误";
					}
				}else
				{
					$this->msg = "用户名错误";
				}	
			}else
			{
				$this->msg = "请重新输入验证码";
			}
		}		
	}
	/*判断用户级别*/
	function authority($v){
		if(!empty($_SESSION['authority']) && $_SESSION['authority']<=$v)
		{
			return true;	
		}else
		{
			return false;	
		}
			
	}
	
	
	/*user_login_check 用户_检查是否登陆*/			
	function user_login_check(){  //如果未设置session则跳到登陆页面

		if($this->user_login_tf() == false)
		{
			$this->user_logout();
			exit();				
		}
		else if ($this->user_time() == true)
		{
			echo "<script>alert('由于你长时间未作任何动作，登陆已超时，请重新登陆')</script>";
			$this->user_logout();
			exit();
		}
		else
		{
			$_SESSION['utime']=time();	
			return true;
		}		
		
	}
	
	function user_login_tf(){   //检查是否设置session
		if(!empty($_SESSION['uname']))
		{
			return true;				
		}
		else
		{
			return false;
		}		
	}
	
	/*user_logout 用户_退出登陆*/
	function user_logout(){
		session_destroy();
		$this->jump();
		exit();	
	}
	
	/*user_time 用户_登录超时*/
	function user_time($time="3600"){
		$thetime = time();
		if(!empty($_SESSION['utime']))
		{
			$a = $thetime-$_SESSION['utime'];
			if($a > $time)
			{
				return true;
			}
		}			
	}
	
	
	/*jump 跳转页面*/
	function jump($v="login.php"){
			echo "<script>window.location.href=\"".$v."\";</script>";
			exit();
	}
	
	
	/*echoMsg 输出错误语句  choMsg(输出内容,输出class,0关闭返回:默认开启,往后条的页数) */	
	function echoMsg($value="",$class="act3",$back="1",$step="-1"){
		if($this->msg == true || !empty($value))
		{
			if($back==0)
			{
				echo "<div id='act' class='$class'>".$this->msg.$value."</div>";	
			}else
			{
				echo "<div id='act' class='$class'>".$this->msg.$value."&nbsp;&nbsp;<a href='javascript:history.go($step);'>返回</a></div>";	
			}
			$this->msg = false;	
		}			
	}
	
	
	/*code 验证码*/
	function code($x="55",$y="32"){	
		$code = false;	
		for ($i=0;$i<4;$i++)
		{
			$code .= dechex(rand(0,15));
		}		
		$_SESSION['code'] = $code;		
		$img = imagecreatetruecolor ($x,$y);
		$img_bg = imagecolorallocate($img,0,0,0);
		$img_txt = imagecolorallocate($img,255,255,255);
		imagestring($img,6,10,9,$code,$img_txt);
		header("Content-Type:image/png");
		return imagepng($img);			
	}
		
	/*显示草稿箱内草稿数*/
	function draft_num(){
		$draft_num  = $this->num_rows($this->select("imglist","status","status='0'"));
		if($draft_num > 0 )
		{
			echo "<div id='act' class='act1'>草稿箱中有 <span>".$draft_num."</span> 篇作品未发布！<a href='opus_manage.php?type=0'>点击查看</a></div>";
		}
	}
	
	
	//inject_check 防止注入	
	function inject_check($sql_str) { 
		$check = eregi('select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str);
		if ($check) {
			echo "输入非法注入内容！";
			$this->msg = "输入非法注入内容！";
			exit ();
		} else {
			return $sql_str;
		}
	}
	
	
	
}
?>