<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_upload.php Mr.Kwok
 * Created Time:2018/10/26 12:37
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('ajaxsubmit')) {
    //远程图片下载(未验证图片安全，有空再写)
    $string = stripcslashes($_POST['content']);//删除转义
    if (empty($string)) {
        header('HTTP/1.1 304 Not Modified');
        exit();
    }
    preg_match_all("/\<img.+src=('|\"|)?(.*)(\\1)([\s].*)?\>/ismUe", $string, $temp, PREG_SET_ORDER);
    $arrayimageurl = array();
    if (is_array($temp) && !empty($temp)) {
        foreach ($temp as $tempvalue) {
            $newarr = explode('?', $tempvalue[2]);//删除？后面的内容
            $arrayimageurl[] = str_replace('\"', '', $newarr[0]);
            $_POST['content'] = str_replace($tempvalue[0], '<img src="' . $newarr[0] . '" />', $_POST['content']);//格式化内容里的图片标签
        }
    }
    $arrayimageurl = array_unique($arrayimageurl); //删除重复项
    function downimg($imageurl)
    {
        if (strtolower(substr($imageurl, 0, 4)) != 'http') {
            return false;
        }
        global $_POST, $_MGLOBAL;
        //下载并保存到数据库
        $file_name = 'yc' . $_MGLOBAL['timestamp'] . rand(100000, 999999);
        $upload_dir = getattachdir();
        $upload_url = str_replace(array(M_ROOT, '\\'), array('', '/'), $upload_dir);
        $imgtype = strtolower(pathinfo($imageurl, PATHINFO_EXTENSION));
        $newimg = false;
        if (in_array($imgtype, array('jpg', 'jpeg', 'gif', 'png')) ? 1 : 0) {
            $filename = $file_name . '.' . $imgtype;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//跳过ssl安全认证
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_URL, $imageurl);
            curl_setopt($ch, CURLOPT_REFERER, $imageurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
            $img = curl_exec($ch);
            curl_close($ch);
            $size = strlen($img);
            if ($size < 10240) {
                return false;
            }
            $fp = @fopen($upload_dir . $filename, 'a');
            fwrite($fp, $img);
            fclose($fp);
            unset($img);
            if (@file_exists($upload_dir . $filename)) {
                //数据库记录开始
                global $_MGLOBAL;
                $newimg = $upload_url . $filename;
                $insertsqlarr = array(
                    'aid' => 0,
                    'id' => 0,
                    'uid' => $_MGLOBAL['uid'],
                    'dateline' => $_MGLOBAL['timestamp'],
                    'summary' => '',
                    'attachtype' => $imgtype,
                    'isimage' => 1,
                    'size' => $size,
                    'url' => $newimg,
                    'hash' => strfilter($_POST['hash'])
                );
                $_MGLOBAL['db']->inserttable('attachments', $insertsqlarr);//写入附件表并返回ID
            }
        }
        return $newimg;
    }
    $i = 0;
    foreach ($arrayimageurl as $imageurl) {
        //下载远程图片
        $newimg = downimg($imageurl);
        if ($newimg) {
            $_POST['content'] = str_replace($imageurl, $newimg, $_POST['content']);
            $i++;
        }
    }
    if ($i == 0) {
        header('HTTP/1.1 404 Not Modified');
    }
    echo $_POST['content'];
    exit();
} else {
    require(SOUREC_DIR . 'class' . DIRECTORY_SEPARATOR . 'upload.class.php');
    $upload_handler = new UploadHandler(
        $formName = 'files',//表单名
        $fileName = $_MGLOBAL['timestamp'] . rand(100000, 999999),//文件名
        getattachdir()//上传目录
    );
    $imgResult = $upload_handler->uploadFile();
    if (is_array($imgResult)) {
        //数据库记录开始
        $imgResult['url'] = str_replace(array(A_DIR, '\\'), array(A_URL, '/'), $imgResult['path']) . $imgResult['fileName'] . '.' . $imgResult['ext'];
        $insertsqlarr = array(
            'aid' => 0,
            'id' => 0,
            'uid' => $_MGLOBAL['uid'],
            'dateline' => $_MGLOBAL['timestamp'],
            'summary' => $imgResult['fileName'],
            'attachtype' => $imgResult['ext'],
            'isimage' => $imgResult['isImg'],
            'size' => $imgResult['size'],
            'url' => $imgResult['url'],
            'hash' => maddslashes(strfilter($_POST['hash']))
        );
        $result = $imgResult;
        $result['id'] = $_MGLOBAL['db']->inserttable('attachments', $insertsqlarr, 1);//写入附件表并返回ID
    } else {
        $result = array('error' => $upload_handler->showError());//出现错误
    }
    @header('Pragma:no-cache');//禁止缓存
    @header('Cache-Control:no-store,no-cache,must-revalidate');//浏览器兼容禁止缓存
    @header('X-Content-Type-Options:nosniff');//禁止浏览器加载CSS/JS
    @header('Content-type:application/json');//发布json类型
    exit(json_encode($result, JSON_UNESCAPED_UNICODE));
}