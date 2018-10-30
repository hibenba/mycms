<?php
/*
	[MyCMS] (C) 2018 Mr.Kwok.
	Info:upload.class.php 2018/2/5 17:33 ver:2.0
*/
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}

//成功后返回一个数组，如果有错误请使用UploadHandler->showError()调用返回值
class UploadHandler
{
    private $formName;//表单名
    private $fileName;//最终返回文件名
    private $maxSize;//上传文件大小限制
    private $allowMime;//允许的MIME类型
    private $allowExt;//允许的扩展名
    private $uploadPath;//上传目录
    private $fileInfo = array();//上传文件数组
    private $ext;//文件扩展名
    private $img;//存放图片相关信息
    private $destination;//最终地址
    private $imgArr = array('jpg', 'jpeg', 'png', 'gif');//遇到图片进行检测
    private $imgFlag = 0;
    private $error = null;
    public $returnArr;

    public function __construct(
        $formName = 'myfiles',//表单名
        $fileName = '',//文件名
        $uploadPath = A_DIR,//上传目录
        $maxSize = 5242880,//文件大小限制
        $allowExt = array(),//文件扩展名
        $allowMime = array('image/jpeg', 'image/png', 'image/gif')//MIME类型
    )
    {
        $this->formName = $formName;
        $this->fileName = $fileName;
        $this->maxSize = $maxSize;
        $this->allowMime = $allowMime;
        $this->allowExt = empty($allowExt) ? $this->imgArr : $allowExt;//默认只允许图片上传
        $this->uploadPath = $uploadPath;
        if (empty($_FILES[$this->formName])) {
            $this->error = '未获取到' . $this->formName . '表单$_FILES内容！';
            $this->showError();
        } else {
            $this->fileInfo = $_FILES[$this->formName];
        }

    }

    private function checkError()
    {
        if (empty($this->fileInfo)) {
            $this->error = '上传文件信息为空';
            return false;
        } else {
            if ($this->fileInfo['error'] == 0) {
                return true;
            } else {
                switch ($this->fileInfo['error']) {
                    case 1:
                        $this->error = '超出了php.ini中文件upload_max_filesize设置大小';
                        break;
                    case 2:
                        $this->error = '超出了表单MAX_FILE_SIZE的文件大小';
                        break;
                    case 3:
                        $this->error = '文件被部分上传';
                        break;
                    case 4:
                        $this->error = '没有选择上传文件';
                        break;
                    case 6:
                        $this->error = '没有临时文件夹';
                        break;
                    case 7:
                        $this->error = '文件无法写入磁盘';
                        break;
                    case 8:
                        $this->error = 'PHP未开启上传扩展或PHP扩展程序中断！';
                        break;
                    default:
                        $this->error = '发生未知错误：' . $this->fileInfo['error'];
                        break;
                }
            }
            return false;
        }

    }

    private function checksize()
    {
        if ($this->fileInfo['size'] > $this->maxSize) {
            $this->error = '上传文件超过限制！';
            return false;
        } else {
            return true;
        }
    }

    private function checkExt()
    {
        $this->ext = strtolower(pathinfo($this->fileInfo['name'], PATHINFO_EXTENSION));
        if (in_array($this->ext, $this->imgArr)) {
            //如果扩展名是图片，就进行检测
            $this->img = @getimagesize($this->fileInfo['tmp_name']);//读取图片信息
            if (empty($this->img)) {
                $this->error = '图片不能识别（可能不是真实图片）！';
                return false;
            } else {
                $typearr = array(1 => 'gif', 2 => 'jpg', 3 => 'png');
                $this->ext = $typearr[$this->img[2]];
                $this->imgFlag = 1;//初步检测为图片，等下一步重绘操作！
            }
        }
        if (in_array($this->ext, $this->allowExt)) {
            return true;
        } else {
            $this->error = '不允许的扩展名！';
            return false;
        }
    }

    private function checkMime()
    {
        if (in_array($this->fileInfo['type'], $this->allowMime)) {
            return true;
        } else {
            $this->error = '不允许的文件类型！';
            return false;
        }
    }

    private function checkHTTPPost()
    {
        if (is_uploaded_file($this->fileInfo['tmp_name'])) {
            return true;
        } else {
            $this->error = '不是通过HTTP Post方式上传的！';
            return false;
        }
    }

    public function showError()
    {
        $this->error = empty($this->error) ? '未定义错误通知！' : $this->error;
        return $this->error;
    }

    private function creatFolder()
    {
        if (is_dir($this->uploadPath) || (!is_dir($this->uploadPath) && @mkdir($this->uploadPath, 0777, true))) {
            return true;
        } else {
            $this->error = '上传目录不存在并无法创建，请检测目录权限！';
            return false;
        }
    }

    public function uploadFile()
    {
        if ($this->checkError() && $this->checksize() && $this->checkExt() && $this->checkMime() && $this->checkHTTPPost() && $this->creatFolder()) {
            $this->destination = $this->uploadPath . $this->fileName . '.' . $this->ext;
            $this->returnArr = array('fileName' => $this->fileName, 'ext' => $this->ext, 'path' => $this->uploadPath, 'size' => $this->fileInfo['size'], 'isImg' => $this->imgFlag, 'destination' => $this->destination);
            if ($this->imgFlag == 1) {
                //对图片进行重绘操作，防止图片木马
                if ($this->ext == 'gif') {
                    @$im = imagecreatefromgif($this->fileInfo['tmp_name']);
                } elseif ($this->ext == 'png') {
                    @$im = imagecreatefrompng($this->fileInfo['tmp_name']);
                } else {
                    @$im = imagecreatefromjpeg($this->fileInfo['tmp_name']);
                }
                if ($im == false) {
                    $this->error = '重绘图片失败！';
                    $this->showError();
                } else {
                    //按格式保存并返回
                    if ($this->ext == 'gif') {
                        imagegif($im, $this->destination);
                    } elseif ($this->ext == 'png') {
                        imagepng($im, $this->destination);
                    } else {
                        imagejpeg($im, $this->destination, 80);
                    }
                    imagedestroy($im);//销毁一图像
                    return $this->returnArr;
                }
            } else {
                if (move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)) {
                    return $this->returnArr;
                } else {
                    $this->error = '移动文件失败！';
                    $this->showError();
                }
            }
        } else {
            $this->showError();
        }
    }

}