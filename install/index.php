<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 index.php Mr.Kwok
 * Created Time:2018/10/30 16:47
 */
define('INSTALL', dirname(__FILE__));//后台目录
include dirname(INSTALL) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'core.php';
$errmes = '';
$lockfile = DATA_DIR . 'install.lock';
if (file_exists($lockfile)) {
    $errmes = '<li>安装锁定，已经安装过了，如果您确定要重新安装，请删除:' . $lockfile . '</li>';
}
if (dir_writeable(DATA_DIR) == 0) {
    $errmes .= '<li>目录权限出错，无法写入内容:' . DATA_DIR . '</li>';
}
if (!empty($_POST) && empty($errmes)) {
    extract($_POST);
    try {
        $link =  mysqli_connect($dbhost, $dbuser, $dbpw, $dbname, $port);
    } catch (Exception $e) {
        showmessage(2, '未成功连接到数据库，请检查数据库配置信息！！');
    }
    if(mysqli_connect_errno())
    {
        exit;
    }
    //更新数据库配置文件
    //更新缓存
    @fopen($lockfile, 'w');//安装成功后写入锁定安装的文件
    showmessage(1, 'MyCMS已成功安装！', MURL);
}
include_once template('install' . DIRECTORY_SEPARATOR . 'install.htm', 1);
function dir_writeable($dir)
{
    $writeable = 0;
    if (is_dir($dir)) {
        if ($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}