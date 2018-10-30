<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 img.func.php Mr.Kwok
 * Created Time:2018/9/20 11:03
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//裁剪图片并生成缓存
function crop_img($img, $width = 120, $height = 150)
{
    global $_MCONFIG;
    if (file_exists($img)) {
        $arrtype = explode('.', $img);
        $filetype = '.' . end($arrtype);//文件扩展名
        $crop_img = '/data/cache' . str_replace(array(M_ROOT, $filetype, $_MCONFIG['attachmentdir']), '', $img) . '_' . $width . '_' . $height . $filetype;
        $crop_img = str_replace(array('//', '\\'), '/', $crop_img);
        if (file_exists(M_ROOT . $crop_img)) {
            return $crop_img;//如果存在缓存图片就直接返回
        }
        $imageValue = @getimagesize($img);
        $sourceWidth = $imageValue[0]; //原图宽
        $sourceHeight = $imageValue[1]; //原图高
        $x = 0;
        $y = 0;
        if ($sourceWidth <= $width && $sourceHeight <= $height) {
            return str_replace(M_ROOT, A_DIR, A_URL, $img);//如果原图小于缩略图就直接返回原图
        }
        if ($sourceHeight != $sourceWidth) {
            //不相等则先以最小边为长度截取图片中心部分
            if ($sourceWidth > $sourceHeight) {
                $x = ($sourceWidth - $sourceHeight) / 2;
                $sourceWidth = $sourceHeight;
            } else {
                $y = ($sourceHeight - $sourceWidth) / 2;
                $sourceHeight = $sourceWidth;
            }
        }
        switch ($imageValue[2]) {
            //选择要处理的图片格式
            case 2:
                $source = imagecreatefromjpeg($img);
                break;
            case 1:
                $source = imagecreatefromgif($img);
                break;
            case 3:
                $source = imagecreatefrompng($img);
                break;
            case 6:
                $source = imagecreatefromwbmp($img);
                break;
            default:
                return '';
        }
        $dirname = dirname(M_ROOT . $crop_img);
        //生成缓存目录并生成缩略图
        if (is_dir($dirname) || (!is_dir($dirname) && @mkdir($dirname, 0777, true))) {
            $thumb = imagecreatetruecolor($width, $height);
            imagefill($thumb, 0, 0, imagecolorallocate($thumb, 255, 255, 255));
            imagecopyresampled($thumb, $source, 0, 0, $x, $y, $width, $height, $sourceWidth, $sourceHeight);//$thumb新建图，$source原图，原图载入新图的X坐标，原图载入新图的Y坐标，$x原图要载入的X坐标，$y原图要载入的Y坐标，$width原图宽度，$height原图高度，$w原图要载入的宽度，$h原图要载入的高度
            imagejpeg($thumb, M_ROOT . $crop_img, 80);
        }
        return $crop_img;
    } else {
        return '';//函数参数有误
    }
}