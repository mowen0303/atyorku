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

    /**
    * 获取1或多个图片
    * @param id 图片ID
    * @return 一维数组，如果是多个id，返回二维数组
    */
    public function getImageById($id) {
        if(is_array($id)) {
            $arrId=implode($id,",");
            $sql = "SELECT * FROM `{$this->table}` WHERE id in ({$arrId})";
            return $this->sqltool->getListBySql($sql);
        } else {
            $sql = "SELECT * FROM `{$this->table}` WHERE id in ({$id})";
            return $this->sqltool->getRowBySql($sql);
        }
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
    * 根据1或多张图片ID删除1或多张图片
    * @param id 要删除的图片ID或ID array
    * @return boolean
    */
    public function deleteImageById($id) {
        if(!$id) return true;
        $sql="";
        $img = null;
        if (is_array($id)) {
            $id = array_filter($id);
            if (!$id) return true;
            $img = $this->getImageById($id);
            $foundIdStr = implode(array_column($img, "id"), ",");
            foreach ($img as $v) {
                $this->deleteImg($v["url"]);
                $this->deleteImg($v["thumbnail_url"]);
            }
            $sql = "DELETE FROM {$this->table} WHERE id in ({$foundIdStr})";
        } else {
            $this->deleteImg($img["url"]);
            $this->deleteImg($img["thumbnail_url"]);
            $sql = "DELETE FROM {$this->table} WHERE id in ({$id})";
        }
        return $this->sqltool->query($sql);
    }

    /**
    * 删除指定路径的一张图片文件
    * @return boolean
    */
    public function deleteImg($path) {
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
    public function uploadImg($inputName, $path, $appliedTable, $generateThumbnail = true, $maxThumbnailFileSize = 600000, $maxThumbnailLength = 200, $maxFileSize = 1000000, $maxLength = 1000) {

        try {
            $file = $_FILES[$inputName];
            if ($file != null) {
                $count = $this->getNumOfUploadImages($inputName);   //获取文件上传数量

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
                        //压缩JPG
                        $src_im = imagecreatefromgif($fileTmpName);
                        $dst_im = imagecreatetruecolor($newWidth, $newHeight);

                        // Imagick 保存动态GIF
                        // $src_im = new Imagick($fileTmpName);
                        //
                        // $image = $src_im->coalesceImages();
                        //
                        // foreach ($image as $frame) {
                        //   $frame->thumbnailImage($newWidth, $newHeight);
                        //   $frame->setImagePage($newWidth, $newHeight, 0, 0);
                        // }
                        //
                        // $image = $image->deconstructImages();
                        // $image->writeImages($root . $uploadsDir . $newFileName, true) or BasicTool::throwException("图片存储失败" . $root . $uploadsDir);

                        imagealphablending($dst_im, false);
                        imagesavealpha($dst_im,true);
                        $transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
                        imagefilledrectangle($dst_im, 0, 0, $newWidth, $newHeight, $transparent);

                        imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagegif($dst_im, $root . $uploadsDir . $newFileName) or BasicTool::throwException("图片存储失败" . $root . $uploadsDir);     //输出压缩后的图片

                        //压缩缩略图
                        if ($generateThumbnail) {
                            imagedestroy($dst_im);  //销毁缓存
                            $dst_im = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
                            imagealphablending($dst_im, false);
                            imagesavealpha($dst_im,true);
                            $transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
                            imagefilledrectangle($dst_im, 0, 0, $newWidth, $newHeight, $transparent);
                            imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
                            imagepng($dst_im, $root . $thumbnailUploadsDir . $newFileName) or BasicTool::throwException("图片存储失败" . $root . $thumbnailUploadsDir);     //输出压缩后的缩略图
                        }
                        imagedestroy($dst_im);  //销毁缓存
                        imagedestroy($src_im);  //销毁缓存
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

    /** 获取上传图片数量, 同时检测是否所有上传图片成功获取且合法
    * @throws upload_error 如果任何文件上传出错
    * @throws invalid_format 如果任何文件类型不符合要求 (png | jpeg | gif)
    */
    public function getNumOfUploadImages($inputName) {
        $count = 0;
        $total = count($_FILES[$inputName]['name']);
        for($i=0; $i<$total; $i++) {
          $tmpFilePath = $_FILES[$inputName]['tmp_name'][$i];
          $size = $_FILES[$inputName]['size'][$i];
          $fileType = $_FILES[$inputName]['type'][$i];
          $fileError = $file["error"][$i];        //错误信息

          //检测文件是否成功获取
          !$fileError > 0 or BasicTool::throwException("上传出错,状态码:" . $fileError);

          //检测文件类型是否合法
          (($fileType == "image/gif") || ($fileType == "image/png") || ($fileType == "image/jpeg") || ($fileType == "image/pjpeg")) or BasicTool::throwException("只支持上传 jpg|png|gif 格式的文件");


          //Make sure we have a filepath and size
          if ($tmpFilePath != "" and $size > 0){
              $count++;
          }
        }
        return $count;
    }


    /**
    * 更新某Row多张图片排序或/和添加新图，自动删除不相关联图片,如果有上传文件需要提供 $uploadInputName, $path, $tableName
    * @param modifiedImageIds 修改后的所有图片Id array 顺序需要与数据库图片Fields顺序一致
    * @param currentImageIds 当前Row的图片Fields所关联的图片ID array 顺序需要与数据库图片Fields顺序一致
    * @param maxNum 最大容许图片数量
    * @param uploadInputName 上传文件输入名称
    * @param path 新图片上传路径
    * @param tableName 数据库表名
    * @return 返回需要上传的图片Fields的Id array
    * -----------------------------------------
    * 举例： 修改当前一本book的图片, book 容许 3张图片
    *       // 获取POST的所有图片Fields并做成array
    *       $modifiedImageIds = array(BasicTool::post("image_id_one"),BasicTool::post("image_id_two"),BasicTool::post("image_id_three"));
    *       $result = $bookModel->getBookById($arr['id']);  // 从数据库获取当前书信息
    *       $currentImageIds = array($result["image_id_one"],$result["image_id_two"],$result["image_id_three"]);
    *       $imageModel->uploadImagesWithExistingImages($modifiedImageIds,$currentImageIds,3,"imgFile",$currentUser->userId,"table");
    */
    public function uploadImagesWithExistingImages($modifiedImageIds,$currentImageIds=false,$maxNum=false,$uploadInputName=false,$path=false,$tableName=false) {
        $imgArr = array_values(array_filter($modifiedImageIds));
        if(!$maxNum) {
            $maxNum = count($modifiedImageIds);
        }
        $numOfNewImages=0;
        // 上传新图片并添加新图id到$imgArr
        if($uploadInputName) {
            $numOfNewImages = $this->getNumOfUploadImages($uploadInputName);
            if ($numOfNewImages > 0) {
                $path or BasicTool::throwException("上传新图片需要提供上传路径");
                $tableName or BasicTool::throwException("上传新图片需要提供表名");
                ($numOfNewImages <= $maxNum) or BasicTool::throwException("图片上传最多3张");
                ($numOfNewImages+count($imgArr)<=$maxNum) or BasicTool::throwException("上传图片数量过多，请先删除当前图片");
                $newImageIds = $this->uploadImg($uploadInputName, $path, $tableName) or BasicTool::throwException($this->errorMsg);
                $imgArr = array_merge($imgArr, $newImageIds);
                (count($imgArr)<=$maxNum) or BasicTool::throwException("Unexpected Error: imgArr size over {$maxNum}.");
            }
        }

        // 检查并删除替代掉的图片
        if($currentImageIds) {
            $needDeletedIds=array();
            foreach($currentImageIds as $v) {
                if($v != null and !in_array($v,$imgArr)) {
                    array_push($needDeletedIds,$v);
                }
            }
            if(count($needDeletedIds)>0) {
                $this->deleteImageById($needDeletedIds);
            }
        }

        return $imgArr;
    }


}



?>
