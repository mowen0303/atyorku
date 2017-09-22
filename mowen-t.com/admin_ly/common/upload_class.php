<?
class upclass {
	public $name;    //上传表单的name	
	//上传方法
	function fn_up($name=""){		
		//$name = $this->name;		
		//判断文件是否通过post上传
		if(is_uploaded_file($_FILES[$name]['tmp_name']))
		{
			$file         = $_FILES[$name];
			$file_name    = $file['name'];      // 被上传的文件名
			$file_nametmp = $file['tmp_name'];  //被上传的临时文件名
			$file_size    = $file['size'];      //被上传文件的大小
			
			$time = date("YmdHis");      //获得当前时间年月日时分秒
		
			$file_name_array =  pathinfo($file_name);  //解析文件路径和名称
			
			$extension  = ".".$file_name_array['extension']; //文件名后缀
			
			//$file_namebas = iconv("UTF-8","GB2312",$file_name_array['filename']); //原始文件名，解决中文乱码
			
			
			$valid_chars_regex = '.A-Z0-9_!@#$%^&()+={}\[\]\',~`-';				//允许的文件名字
			$file_namebas = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "",$file_name_array['filename']);		
			
			$root =$_SERVER['DOCUMENT_ROOT'];
			$file_dir = "/upfile/snews/";     //指定文件上传路径
			
			$new_name = $file_dir.$file_namebas."_".$time.$extension; //重新组合文件名
			
			move_uploaded_file($file_nametmp,$root.$new_name);
			
			return $new_name;
			echo "上传成功";
			
			
		}else
		{
		
			echo $_FILES[$name]['error'];	
			
		}

	}
	
	
	
	
}
?>


