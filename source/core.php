<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 core.php Mr.Kwok
 * Created Time:2018/9/20 9:38
 */
define('IN_MYCMS', TRUE);
define('M_ROOT', substr(dirname(__FILE__), 0, -6));//网站根目录
define('SOUREC_DIR', M_ROOT . 'source' . DIRECTORY_SEPARATOR);//定义主要功能源码目录
define('DATA_DIR', M_ROOT . 'data' . DIRECTORY_SEPARATOR);//定义数据目录
define('M_VER', '2.2');
define('M_RELEASE', '20180920');
define('M_DEBUG', true);//是否调试模式
if (M_DEBUG) {
    @error_reporting(E_ALL);
    @ini_set('error_log', DATA_DIR . 'log' . DIRECTORY_SEPARATOR . 'error_log' . date("Ymd") . '.txt'); //将出错信息输出到一个文本文件
} else {
    @error_reporting(E_ALL ^ E_NOTICE);
}
$_MBLOCK = $_MCACHE = array();
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'common.func.php');
//载入基本设置文件
if (!@include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'config.cache.php')) {
    errorlog('log', '系统配置错误，未成功载入配置文件！', true);
}
//数据处理
$_GET = maddslashes($_GET);
$_POST = maddslashes($_POST);
$_COOKIE = maddslashes($_COOKIE);
$_MGLOBAL = array('onlineip' => get_client_ip(), 'timestamp' => time(), 'authkey' => md5($_MCONFIG['sitekey']), 'uid' => 0, 'username' => 'Guest');//初始化超级全局数组变量
define('MURL', $_MCONFIG['siteurl']);//网站地址
define('WAPURL', empty($_MCONFIG['mobileurl']) ? MURL . '/wap' : $_MCONFIG['mobileurl']);//WAP地址
define('H_DIR', defined('IN_WAP') ? M_ROOT . 'wap' . DIRECTORY_SEPARATOR . $_MCONFIG['htmldir'] . DIRECTORY_SEPARATOR : M_ROOT . $_MCONFIG['htmldir'] . DIRECTORY_SEPARATOR);//HTML生成目录，区别wap
define('H_URL', $_MCONFIG['htmlurl']);//HTML访问目录
define('A_DIR', M_ROOT . $_MCONFIG['attachmentdir'] . DIRECTORY_SEPARATOR);//定义附件目录
define('A_URL', $_MCONFIG['attachmenturl']);//定义附件访问地址
//GZIP关闭后的缓存方式，这里开始缓冲数据
if ($_MCONFIG['gzipcompress'] && function_exists('ob_gzhandler')) {
    ob_start('ob_gzhandler');
} else {
    ob_start();
}
//连接数据库
function connectMysql()
{
    global $_MCONFIG, $_MGLOBAL;
    if (empty($_MGLOBAL['db'])) {
        include(SOUREC_DIR . 'class' . DIRECTORY_SEPARATOR . 'mysqli.class.php');
        $_MGLOBAL['db'] = new MyCMS_DataBase($_MCONFIG['dbhost'], $_MCONFIG['dbuser'], $_MCONFIG['dbpw'], $_MCONFIG['dbname'], $_MCONFIG['port'], $_MCONFIG['dbcharset']);
    }
}