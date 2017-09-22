<?php
/*//图片压缩并另存函数
//scal_pic(图片地址及名称,目标地址)
//相互协调的文件 - 字库pict.ttf - 相同目录下 - 汉仪综艺体简

//示例：

//if(!scal_pic('pic.jpg','new_pic.jpg')){ die('您上传的图片格式存在问题！'); //然后再删除掉图片文件。。。 }
function scal_pic($file_name,$file_new){
        //验证参数
        if(!is_string($file_name) || !is_string($file_new)){
                return false;
        }
        //获取图片信息
        $pic_scal_arr = @getimagesize($file_name);   //getimagesize -- 取得图像大小  getimagesize() 函数将测定任何 GIF，JPG，PNG，SWF，SWC，PSD，TIFF，BMP，IFF，JP2，JPX，JB2，JPC，XBM 或 WBMP 图像文件的大小并返回图像的尺寸以及文件类型和一个可以用于普通 HTML 文件中 IMG 标记中的 height/width 文本字符串。 返回一个具有四个单元的数组。[索引 0] 包含图像[宽度]的像素值，[索引 1] 包含图像[高度]的像素值。索引 2 是图像类型的标记：1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM。这些标记与 PHP 4.3 新加的 IMAGETYPE 常量对应。[索引 3 ]是文本字符串，内容为“height="yyy" width="xxx"”，可直接用于 IMG 标记。 
        if(!$pic_scal_arr){
                return false;
        }
        //获取图象标识符
        $pic_creat = '';
        switch($pic_scal_arr['mime']){
                case 'image/jpeg':
                        $pic_creat = @imagecreatefromjpeg($file_name);
                        break;
                case 'image/gif':
                        $pic_creat = @imagecreatefromgif($file_name);
                        break;
                case 'image/png':
                        $pic_creat = @imagecreatefrompng($file_name);
                        break;
                case 'image/wbmp':
                        $pic_creat = @imagecreatefromwbmp($file_name);
                        break;
                default:
                        return false;
                        break;
        }
        if(!$pic_creat){
                return false;
        }
        //判断/计算压缩大小
        $max_width = 300;//最大宽度,象素，高度不限制
        $min_width = 15;
        $min_heigth = 20;
        if($pic_scal_arr[0]<$min_width || $pic_scal_arr[1]<$min_heigth){
                return false;
        }
        $re_scal = 0;
        if($pic_scal_arr[0]>$max_width){
                $re_scal = ($max_width / $pic_scal_arr[0]);
        }
        $re_width = round($pic_scal_arr[0] * $re_scal);
        $re_height = round($pic_scal_arr[1] * $re_scal);
		
        //创建空图象
        $new_pic = @imagecreatetruecolor($re_width,$re_height);
        if(!$new_pic){
                return false;
        }
        //复制图象
		// imagecopyresample()共有10个参数，前面两个是目标文件和源文件，后面4个分别是dst和src的width,height，中间的src_x,src_y,dst_x,dst_y看得比较晕。。例子里面都是设置为0，后来试了下，发现把这4个参数写成 dst_start_x, dst_start_y, src_start_x, src_start_y的话就很好理解了。举个例子说吧。假如要在缩略图上下各留10 pixel的空白，那就可以用imagecopyresampled($dst, $src, dst_start_x, dst_start_y + 10, src_start_x, src_start_y, dst_width, dst_height – 20, src_width, src_height)来实现。
        if(!@imagecopyresampled($new_pic,$pic_creat,0,0,0,0,$re_width,$re_height,$pic_scal_arr[0],$pic_scal_arr[1])){
                return false;
        }
		
        //输出文件
        $out_file = '';
        switch($pic_scal_arr['mime']){
                case 'image/jpeg':
                        $out_file = @imagejpeg($new_pic,$file_new);
                        break;
                case 'image/gif':
                        $out_file = @imagegif($new_pic,$file_new);
                        break;
                case 'image/png':
                        $out_file = @imagepng($new_pic,$file_new);
                        break;
                case 'image/wbmp':
                        $out_file = @imagewbmp($new_pic,$file_new);
                        break;
                default:
                        return false;
                        break;
        }
        if($out_file){
                return true;
        }else{
                return false;
        }
}*/

//////////////////////////////////////////////////////
class resizeimage
{
        //图片类型
        var $type;
        //实际宽度
        var $width;
        //实际高度
        var $height;
        //改变后的宽度
        var $resize_width;
        //改变后的高度
        var $resize_height;
        //是否裁图
        var $cut;
        //源图象
        var $srcimg;
        //目标图象地址
        var $dstimg;
        //临时创建的图象
        var $im;

        function resizeimage($img, $wid, $hei,$c)
        {
                //echo $img.$wid.$hei.$c;
                $this->srcimg = $img;
                $this->resize_width = $wid;
                $this->resize_height = $hei;
                $this->cut = $c;
                //图片的类型
                $this->type = substr(strrchr($this->srcimg,"."),1);
                //初始化图象
                $this->initi_img();
                //目标图象地址
                $this -> dst_img();
     if(file_exists($this->dstimg))
     {
     header('Content-Type: image/jpeg');
     $image = imagecreatefromjpeg($this->dstimg);
     imagejpeg($image);
     ImageDestroy($image);
     die();
     }
                //imagesx imagesy 取得图像 宽、高
                $this->width = imagesx($this->im);
                $this->height = imagesy($this->im);
                //生成图象
                $this->newimg();
                ImageDestroy ($this->im);
        }
        function newimg()
        {

                // +----------------------------------------------------+
                // | 增加LOGO到缩略图中 Modify By Ice
                // +----------------------------------------------------+
                //Add Logo
                //$logoImage = ImageCreateFromJPEG('t_a.jpg');
                //ImageAlphaBlending($this->im, true);
                //$logoW = ImageSX($logoImage);
                //$logoH = ImageSY($logoImage);
                // +----------------------------------------------------+

     //改变后的图象的比例
                $resize_ratio = ($this->resize_width)/($this->resize_height);
                //实际图象的比例
                $ratio = ($this->width)/($this->height);
                if(($this->cut)=="1")
                //裁图
                {
                        if($ratio>=$resize_ratio)
                        //高度优先
                        {
                                //imagecreatetruecolor — 新建一个真彩色图像
                                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);
                                //imagecopyresampled — 重采样拷贝部分图像并调整大小
                                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width,$this->resize_height, (($this->height)*$resize_ratio), $this->height);


                                // +----------------------------------------------------+
                                // | 增加LOGO到缩略图中 Modify By Ice
                                // +----------------------------------------------------+
                                //ImageCopy($newimg, $logoImage, 0, 0, 0, 0, $logoW, $logoH);
                                // +----------------------------------------------------+

                                //imagejpeg — 以 JPEG 格式将图像输出到浏览器或文件
                                ImageJpeg ($newimg,$this->dstimg);
                        }
                        if($ratio<$resize_ratio)
                        //宽度优先
                        {
                                $newimg = imagecreatetruecolor($this->resize_width,$this->resize_height);
                                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, $this->resize_height, $this->width, (($this->width)/$resize_ratio));


                                // +----------------------------------------------------+
                                // | 增加LOGO到缩略图中 Modify By Ice
                                // +----------------------------------------------------+
                                //ImageCopy($newimg, $logoImage, 0, 0, 0, 0, $logoW, $logoH);
                                // +----------------------------------------------------+


                                ImageJpeg ($newimg,$this->dstimg);
                        }

                }
                else
                //不裁图
                {
                        if($ratio>=$resize_ratio)
                        {
                                $newimg = imagecreatetruecolor($this->resize_width,($this->resize_width)/$ratio);
                                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, $this->resize_width, ($this->resize_width)/$ratio, $this->width, $this->height);


                                // +----------------------------------------------------+
                                // | 增加LOGO到缩略图中 Modify By Ice
                                // +----------------------------------------------------+
                                //ImageCopy($newimg, $logoImage, 0, 0, 0, 0, $logoW, $logoH);
                                // +----------------------------------------------------+


                                ImageJpeg ($newimg,$this->dstimg);
                        }
                        if($ratio<$resize_ratio)
                        {
                                $newimg = imagecreatetruecolor(($this->resize_height)*$ratio,$this->resize_height);
                                imagecopyresampled($newimg, $this->im, 0, 0, 0, 0, ($this->resize_height)*$ratio, $this->resize_height, $this->width, $this->height);


                                // +----------------------------------------------------+
                                // | 增加LOGO到缩略图中 Modify By Ice
                                // +----------------------------------------------------+
                                //ImageCopy($newimg, $logoImage, 0, 0, 0, 0, $logoW, $logoH);
                                // +----------------------------------------------------+


                                ImageJpeg ($newimg,$this->dstimg);
                        }
                }

                // +----------------------------------------------------+
                // | 释放资源 Modify By Ice
                // +----------------------------------------------------+
                //ImageDestroy($logoImage);
                // +----------------------------------------------------+

        }
        //初始化图象
       
        function initi_img()
        {
                if($this->type=="jpg")
                {
                        $this->im = imagecreatefromjpeg($this->srcimg);
                }
                if($this->type=="gif")
                {
                        $this->im = imagecreatefromgif($this->srcimg);
                }
                if($this->type=="png")
                {
                        $this->im = imagecreatefrompng($this->srcimg);
                }
        }
       
        //图象目标地址
        function dst_img()
        {
                $full_length = strlen($this->srcimg);
                $type_length = strlen($this->type);
                $name_length = $full_length-$type_length;
                $name         = substr($this->srcimg,0,$name_length-1);
                $this->dstimg = $name."_small.".$this->type;
        }
}


//////////////////////////////////////
?>
