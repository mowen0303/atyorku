<?php

abstract class Model
{
//--------------不要重写此部分-----------
    //寄存SqlTool的对象
    protected $sqltool = null;
    protected $pageHtml = null;
    protected $table = null;  //这个需要被继承类重写

    protected $totalPage = 0;

    public $idOfInsert = 0;
    public $errorMsg = null;

    public function __construct()
    {
        //获取连接指针
        $this->sqltool = SqlTool::getSqlTool();
    }

    /**
     * 通过一个select查询语句返回一个二维数组,并同时封装一个分页代码
     * @param $table  表名
     * @param $sql
     * @param $countSql    计算表内共有多少条数据
     * @param int $pageCurrent
     * @param int $pageSize
     * @return array
     */
    protected function getListWithPage($table, $sql, $countSql = null, $pageSize = 50, $debug = false)
    {
        $pageCurrent = BasicTool::get('page')?:1;
        $limitX = ($pageCurrent - 1) * $pageSize;

        $sql .= " limit {$limitX},{$pageSize}";
        echo $debug ? $sql . "<br>" . $countSql : null;

        //将列表放到一个数组
        $arr = $this->sqltool->getListBySql($sql);


        //封装分页
        $count = $countSql == null ? $this->sqltool->getCountByTable($table) : $this->sqltool->getCountBySql($countSql);
        $pageCount = ceil($count / $pageSize);
        $this->totalPage = $pageCount;
        $url = basename($_SERVER['REQUEST_URI']);
        $pageName = "?page=";
        if(strpos($url, '?') !== false && strpos($url, '?page=') === false){
            $pageName = "&page=";
        }
        if(BasicTool::get('page')){
            $url = explode($pageName,$url)[0];
        }
        $pageHtml = '<div class="pageListBox">';
        if ($pageCount > 1) {
            if ($pageCurrent == 1) {
                $pageHtml .= '<i >&lt;&lt;</i>';
            } else {
                $pageHtml .= '<a href="' . $url . $pageName . 1 . '">&lt;&lt;</a>';
                $pageHtml .= '<a href="' . $url . $pageName . ($pageCurrent - 1) . '">&lt;</a>';
            }
            for ($i = 1; $i <= $pageCount; $i++) {
                $pageCurrentHtml = null;
                if ($pageCurrent == $i) {
                    $pageCurrentHtml = 'class="current"';
                }

                if($pageCurrent-$i<15 && $i-$pageCurrent<15){
                    $pageHtml .= '<a ' . $pageCurrentHtml . ' href="' . $url . $pageName . $i . '">' . $i . '</a>';
                }

            }
            if ($pageCurrent == $pageCount) {
                $pageHtml .= '<i >&gt;&gt;</i>';
            } else {
                $pageHtml .= '<a href="' . $url . $pageName . ($pageCurrent + 1) . '">&gt;</a>';
                $pageHtml .= '<a href="' . $url . $pageName . $pageCount . '">&gt;&gt;</a>';
            }
            $pageHtml .= '</div>';
            $this->pageHtml = $pageHtml;
        }

        return $arr;
    }

    public function getTotalPage()
    {

        return ["totalPage" => $this->totalPage];

    }


    /**
     * 据id值从某张表内获得一条数据
     * @param $table
     * @param $id
     * @param bool $debug
     * @return 一维关联数组
     */
    public function getRowById($table, $id, $debug = false)
    {
        $id += 0;
        $sql = "select * from {$table} where id in ({$id})";
        echo $debug ? $sql : null;
        return $this->sqltool->getRowBySql($sql);
    }


    /**
     * 向某张表内插入一条数据
     * @param $table 表名
     * @param $arr 把字段和值封装到键值对数组中
     * @param bool $debug
     * @return bool
     */
    public function addRow($table, $arr, $debug = false)
    {
        $field = "";
        $value = "";
        foreach ($arr as $k => $v) {
            $field .= $k . ",";
            $value .= "'$v'" . ",";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into {$table} ({$field}) values ({$value})";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            $this->idOfInsert = $this->sqltool->getInsertId();
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }

    /**
     * 通过主键id修改一条数据
     * @param $table 表名
     * @param $id id的值
     * @param $arrKV 把字段和值封装到键值对数组中
     * @param bool $debug
     * @return bool
     */
    public function updateRowById($table, $id, $arrKV, $debug = false)
    {
        $str = "";
        foreach ($arrKV as $k => $v) {
            $str .= "{$k}='{$v}'" . ",";
        }
        $str = substr($str, 0, -1);
        $sql = "update $table set {$str} where id in ('{$id}')";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "没有数据受到影响";
        return false;
    }


    /**
     * 真删除
     * @param $table 表名
     * @param $field 字段名
     * @param $value 字段所对应的值,可以是数组
     * @param bool $debug
     * @return bool
     */
    public function realDeleteByFieldIn($table, $field, $value, $debug = false)
    {
        if (is_array($value)) {
            $where = null;
            foreach ($value as $v) {
                $v = $v + 0;
                $v = "'" . $v . "'";
                $where .= $v . ",";
            }
            $value = substr($where, 0, -1);
        } else {
            $value = "'" . $value . "'";
        }

        $sql = "delete from {$table} where $field in ($value)";
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        echo $debug ? $sql : null;
        $this->errorMsg = "删除失败,数据未受影响";
        return false;
    }

    /**
     * 软删除
     * @param $table 表名
     * @param $fieldName 字段名
     * @param $fieldValue 字段所对应的值
     * @param bool $debug
     * @return bool
     */
    public function logicalDeleteByFieldIn($table, $fieldName, $fieldValue, $debug = false)
    {
        if (is_array($fieldValue)) {
            $where = null;
            foreach ($fieldValue as $v) {
                $v = $v + 0;
                $v = "'" . $v . "'";
                $where .= $v . ",";
            }
            $fieldValue = substr($where, 0, -1);
        } else {
            $fieldValue = "'" . $fieldValue . "'";
        }
        $sql = "UPDATE {$table} SET is_del='1' WHERE $fieldName in ($fieldValue)";
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        if ($this->sqltool->getAffectedRows() > 0) {
            return true;
        }
        $this->errorMsg = "删除失败数据未受影响";
        return false;
    }

    /**
     * 判断某个字段下,除指定id行之外的其他行的值是不是唯一
     * @param $table
     * @param $fieldName
     * @param $fieldValue
     * @param $id
     * @return bool
     */
    public function isExistByFieldValue($table, $fieldName, $fieldValue, $id = false, $debug = false)
    {
        //SELECT * FROM `user` WHERE name = 'jerry' and id NOT IN ('1')
        $sql = "select $fieldName from $table where $fieldName = '{$fieldValue}'";
        if ($id) {
            $sql .= " and id NOT IN ('{$id}')";
        }
        echo $debug ? $sql : null;
        $this->sqltool->query($sql);
        return $this->sqltool->getAffectedRows() > 0 ? true : false;
    }


    /**
     * 输出翻页html代码块
     */
    public function echoPageList()
    {
        echo $this->pageHtml;
    }

    /**
     * 上传,所有的文件都在/uploads/的子目录里
     * @param $inputName
     * @param $path
     * @param int $maxFileSize 最大文件大小
     * @param int $maxLength 最大边长
     * @return bool|string        文件路径
     */
    function uploadImg($inputName, $path, $maxFileSize = 1000000, $maxLength = 1000)
    {

        try {
            $file = $_FILES[$inputName];
            if ($file != null) {
                //初始化参数
                $fileName = $file['name'];          //文件名
                $fileTmpName = $file['tmp_name'];   //临时文件
                $fileType = $file["type"];          //文件类型
                $fileSize = $file["size"];          //文件大小
                $fileError = $file["error"];        //错误信息
                $root = $_SERVER["DOCUMENT_ROOT"];
                $uploadsFolder = "/uploads/";
                $uploadsDir = $uploadsFolder . $path . "/";
                //检测文件是否成功获取
                !$file["error"] > 0 or BasicTool::throwException("上传出错,状态码:" . $file["error"]);

                //检测文件类型是否合法
                (($fileType == "image/gif") || ($fileType == "image/png") || ($fileType == "image/jpeg") || ($fileType == "image/pjpeg")) or BasicTool::throwException("只支持上传 jpg|png|gif 格式的文件");

                //检测上传目录文件夹权限
                if (is_dir($root . $uploadsDir)) {
                    //目录存在,检查是否可写
                    if (!is_writable($root . $uploadsFolder)) {
                        chmod($root . $uploadsFolder, 0777) or BasicTool::throwException("文件夹权限修改失败:" . $root . $uploadsFolder);;
                    }
                    if (!is_writable($root . $uploadsDir)) {
                        chmod($root . $uploadsDir, 0777) or BasicTool::throwException("文件夹权限修改失败:" . $root . $uploadsDir);
                        //BasicTool::throwException("不可以写:".$uploadsDirRoot);
                    }

                } else {
                    //目录不存在,创建目录
                    mkdir($root . $uploadsDir, 0777);
                }


                //配置上传文件名
                $newFileName = trim($fileName);
                $newFileName = substr(strrchr($newFileName, '.'), 1);
                $newFileName = time() . "." . $newFileName;

                //记录图像尺寸,如果图片不需要压缩,则用原尺寸重新渲染,防止上传漏洞
                list($width, $height) = getimagesize($fileTmpName);
                $newWidth = $width;
                $newHeight = $height;
                //需要压缩图片,重新计算图片尺寸
                if ($fileSize > $maxFileSize) {
                    $imgRatio = sprintf("%.2f", $width / $height);

                    if ($imgRatio > 1) {
                        //横向图片
                        $newWidth = $maxLength;
                        $newHeight = floor($maxLength / $imgRatio);
                    } else if ($imgRatio == 1) {
                        //正方形图片
                        $newWidth = $maxLength;
                        $newHeight = $maxLength;
                    } else {
                        //竖向图片
                        $newWidth = floor($maxLength * $imgRatio);
                        $newHeight = $maxLength;
                    }
                }

                //压缩
                if ($fileType == "image/jpeg") {
                    //压缩JPG
                    $src_im = imagecreatefromjpeg($fileTmpName);
                    if (function_exists("imagecopyresampled")) {
                        //高保真压缩
                        $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    } else {
                        //快速压缩
                        $dst_im = imagecreate($newWidth, $newHeight);
                        imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    }
                    imagejpeg($dst_im, $root . $uploadsDir . $newFileName, 80) or BasicTool::throwException("图片存储失败:" . $root . $uploadsDir . $newFileName . "newwidth:" . $newWidth . "newheight:" . $newHeight . "width:" . $width . "height:" . $height);     //输出压缩后的图片
                    imagedestroy($dst_im);  //销毁缓存
                    imagedestroy($src_im);  //销毁缓存

                } else if ($fileType == "image/png") {
                    //压缩PNG
                    $src_im = imagecreatefrompng($fileTmpName);
                    $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagepng($dst_im, $root . $uploadsDir . $newFileName) or BasicTool::throwException("图片存储失败" . $root . $uploadsDir);     //输出压缩后的图片
                    imagedestroy($dst_im);  //销毁缓存
                    imagedestroy($src_im);  //销毁缓存

                } else if ($fileType == "image/gif") {
                    if ($fileSize > $maxFileSize) {
                        BasicTool::throwException("只支持上传小于" . ($maxFileSize / 1000000) . "MB的GIF图片");
                    }
                }

                //返回上传文件名
                return $uploadsDir . $newFileName;

            } else {
                //无文件需要上传
                $this->errorMsg = "未获取到上传文件";
                return " ";
            }

        } catch (Exception $e) {
            $this->errorMsg = $e->getMessage();
            return false;
        }

    }

    /**
     * @param $arr 数组或二维数组
     * @param String $key when $arr is an 2D array, $key specifies the field to concatenate with
     * @return String  i.e. "37,38,49,51"
     */
    function concatField($arr,$key=false){
        $concat = "";
        foreach ($arr as $item){
            if ($key)
                $temp = $item[$key];
            else
                $temp = $item;
            $temp .= ",";
            $concat .= $temp;
        };
        return substr($concat,0,-1);
    }


}

?>