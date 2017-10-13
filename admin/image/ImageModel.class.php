<?php
namespace admin\image;   //-- 注意 --//
use admin\statistics\StatisticsModel;
use admin\user\UserModel;
use \Model as Model;
use \BasicTool as BasicTool;
use \Exception as Exception;

class ImageModel extends Model
{
    public function __construct() {
        parent::__construct();
        $this->table = "image";
    }

    public function getImageById($id) {
        $sql = "SELECT * FROM `{$this->table}` WHERE id in ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    /**
    * 添加一张图片
    * @param imageUrl 大图片URL
    * @param thumbnailUrl 缩略图URL
    * @param appliedTable 图片应用的db表名
    * @param size 大图大小
    * @param width 大图宽
    * @param height 大图高
    * @return boolean
    */
    public function addImage($imageUrl, $thumbnailUrl, $appliedTable, $size, $width, $height) {
        $arr = [];
        $arr["url"] = $imageUrl;
        $arr["thumbnail_url"] = $thumbnailUrl;
        $arr["height"] = $height;
        $arr["width"] = $width;
        $arr["size"] = $size;
        $arr["applied_table"] = $appliedTable;
        $arr["publish_time"] = time();

        return $this->addRow($this->table, $arr);
    }

    /**
    * 根据图片ID删除此图片
    * @param id 要删除的图片ID
    * @return boolean
    */
    public function deleteImageById($id) {
        $img = $this->getImageById($id);
        if ($img) {
            $this->deleteImg($img["url"]);
            $this->deleteImg($img["thumbnail_url"]);
            $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
            return $this->sqltool->query($sql);
        }
        return false;
    }

    /**
    * 删除指定路径的一张图片文件
    * @return boolean
    */
    function deleteImg($path) {
        $root = $_SERVER["DOCUMENT_ROOT"];
        return unlink($root . $path);
    }

    /**
     * 上传,所有的文件都在/uploads/的子目录里
     * 原图默认保存在 /uploads/rawimage/
     * 缩略图默认开启，默认保存路径为 /uploads/[user ID]/
     * @param string $inputName 图片输入名称
     * @param int $path 缩略图路径, 默认需要传user_id, 缩略图如果关闭，原图路径改为$path, 如果开启，原图路径不变，缩略图存在$path
     * @param boolean $generateThumbnail 是否生成缩略图
     * @param int $maxThumbnailFileSize 最大缩略图文件大小
     * @param int $maxThumbnailLength 最大缩略图边长
     * @param int $maxFileSize 最大文件大小
     * @param int $maxLength 最大边长
     *
     * @return int[] array of new image id
     */
    function uploadImg($inputName, $path, $appliedTable, $generateThumbnail = true, $maxThumbnailFileSize = 600000, $maxThumbnailLength = 200, $maxFileSize = 1000000, $maxLength = 1000) {

        try {
            $file = $_FILES[$inputName];
            if ($file != null) {
                $count = count($file['name']);

                $root = $_SERVER["DOCUMENT_ROOT"];
                $uploadsFolder = "/uploads/";
                $uploadsDir = $generateThumbnail ? ("{$uploadsFolder}rawimage/") : ("{$uploadsFolder}{$path}/");
                $thumbnailUploadsDir = "{$uploadsFolder}{$path}/";

                $result = [];
                for ($i=0; $i<$count; $i++) {
                    //初始化参数
                    $fileName = $file['name'][$i];          //文件名
                    $fileTmpName = $file['tmp_name'][$i];   //临时文件
                    $fileType = $file["type"][$i];          //文件类型
                    $fileSize = $file["size"][$i];          //文件大小
                    $fileError = $file["error"][$i];        //错误信息

                    //检测文件是否成功获取
                    !$fileError > 0 or BasicTool::throwException("上传出错,状态码:" . $fileError);

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
                    $newFileName = uniqid(rand(), true) . "." . $newFileName;
                    while (true) {
                        $newFileName = uniqid(rand(), true) . "." . $newFileName;
                        if (!file_exists($uploadsDir . $newFileName)) break;
                    }

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

                    if ($generateThumbnail) {
                        //检测缩略图上传目录文件夹权限
                        if (is_dir($root . $thumbnailUploadsDir)) {
                            if (!is_writable($root . $thumbnailUploadsDir)) {
                                chmod($root . $thumbnailUploadsDir, 0777) or BasicTool::throwException("文件夹权限修改失败:" . $root . $thumbnailUploadsDir);
                            }
                        } else {
                            mkdir($root . $thumbnailUploadsDir, 0777);
                        }

                        // 重新计算缩略图尺寸
                        $thumbnailWidth = $newWidth;
                        $thumbnailHeight = $newHeight;
                        if ($fileSize > $maxThumbnailFileSize) {
                            $imgRatio = sprintf("%.2f", $width / $height);
                            if ($imgRatio > 1) {
                                //横向图片
                                $thumbnailWidth = $maxThumbnailLength;
                                $thumbnailHeight = floor($maxThumbnailLength / $imgRatio);
                            } else if ($imgRatio == 1) {
                                //正方形图片
                                $thumbnailWidth = $maxThumbnailLength;
                                $thumbnailHeight = $maxThumbnailLength;
                            } else {
                                //竖向图片
                                $thumbnailWidth = floor($maxThumbnailLength * $imgRatio);
                                $thumbnailHeight = $maxThumbnailLength;
                            }
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

                        //压缩缩略图
                        if ($generateThumbnail) {
                            imagedestroy($dst_im);  //销毁缓存
                            if (function_exists("imagecopyresampled")) {
                                //高保真压缩
                                $dst_im = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
                                imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
                            } else {
                                //快速压缩
                                $dst_im = imagecreate($thumbnailWidth, $thumbnailHeight);
                                imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
                            }
                            imagejpeg($dst_im, $root . $thumbnailUploadsDir . $newFileName, 80) or BasicTool::throwException("图片存储失败:" . $root . $thumbnailUploadsDir . $newFileName . "thumbnailWidth:" . $thumbnailWidth . "thumbnailHeight:" . $thumbnailHeight . "width:" . $width . "height:" . $height);     //输出压缩后的图片

                        }

                        imagedestroy($dst_im);  //销毁缓存
                        imagedestroy($src_im);  //销毁缓存

                    } else if ($fileType == "image/png") {
                        //压缩PNG
                        $src_im = imagecreatefrompng($fileTmpName);
                        $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagepng($dst_im, $root . $uploadsDir . $newFileName) or BasicTool::throwException("图片存储失败" . $root . $uploadsDir);     //输出压缩后的图片

                        //压缩缩略图
                        if ($generateThumbnail) {
                            imagedestroy($dst_im);  //销毁缓存
                            $dst_im = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
                            imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
                            imagepng($dst_im, $root . $thumbnailUploadsDir . $newFileName) or BasicTool::throwException("图片存储失败" . $root . $thumbnailUploadsDir);     //输出压缩后的缩略图
                        }
                        imagedestroy($dst_im);  //销毁缓存
                        imagedestroy($src_im);  //销毁缓存

                    } else if ($fileType == "image/gif") {
                        if ($fileSize > $maxFileSize) {
                            BasicTool::throwException("只支持上传小于" . ($maxFileSize / 1000000) . "MB的GIF图片");
                        }
                    }

                    //添加图片到数据库
                    if ($this->addImage(
                        $uploadsDir . $newFileName,
                        ($generateThumbnail) ? ($thumbnailUploadsDir . $newFileName) : ($uploadsDir . $newFileName),
                        $appliedTable,
                        filesize($root . $uploadsDir . $newFileName),
                        $newWidth,
                        $newHeight)
                    ) {
                        $result[$i] = $this->idOfInsert;
                    } else {
                        $this->errorMsg = "图片上传失败";
                        return " ";
                    }
                }
                return $result;
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



}



?>
