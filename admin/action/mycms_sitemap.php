<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_sitemap.php Mr.Kwok
 * Created Time:2018/10/26 22:53
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('baidusendurlsubmit')) {
    //开启百度推送服务
    $baidusendurl = trim(strip_tags($_POST['baidusendurl']));
    if (substr($baidusendurl, 0, 35) == 'http://data.zz.baidu.com/urls?site=') {
        $_MGLOBAL['db']->query("REPLACE INTO " . tname('settings') . " (variable, value) VALUES ('baidusendurl', '$baidusendurl')");//更新接口地址
    } else {
        $_MGLOBAL['db']->query("REPLACE INTO " . tname('settings') . " (variable, value) VALUES ('baidusendurl', '')");//更新接口地址
    }
    include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updatesettingcache();//更新设置缓存
    sheader($theurl);
}
if (!empty($_MGET['deletelog'])) {
    //删除日志
    if (substr($_MGET['deletelog'], -5, 5) == 'baidu') {
        @unlink(DATA_DIR . 'log/' . str_replace('baidu', '.baidu', $_MGET['deletelog']));//附件删除
    }
    sheader($theurl);
}
$jsoncon = array();
//读取推送日志
if (submitcheck('sendlogsubmit')) {
    $logfile = DATA_DIR . 'log' . DIRECTORY_SEPARATOR . $_POST['filename'];
    if (!is_readable($logfile) || substr($_POST['filename'], -6, 6) != '.baidu') {
        showmessage(2, '日志文件不可读');
    }
    print_r($_POST);
    $filecon = explode(PHP_EOL, file_get_contents($logfile));
    foreach ($filecon as $value) {
        if (!empty($value)) {
            $jsoncon[] = json_decode($value, TRUE);
        }
    }
    krsort($jsoncon);//倒序数组
}
$filename = '';
if (!empty($_MCONFIG['baidusendurl'])) {
    $filearr = sreaddir(DATA_DIR . 'log', 'baidu');
    foreach ($filearr as $value) {
        $filename .= '<option value="' . $value . '">' . str_replace(array('.baidu', ' posturl_'), '', $value) . '</option>';
    }
}

$baidusitemap = MURL . '/main.php?action-sitemap';
$baidupattern = MURL . '/(.*) ' . WAPURL . '/${1}';
$sm_pattern = MURL . '/data/sitemap/sm_pattern.xml';
$sitemapfile = '';
$filearr = sreaddir(DATA_DIR . 'sitemap', 'xml');
if ($filearr) {
    foreach ($filearr as $value) {
        $sitemapfile .= PHP_EOL . MURL . '/data/sitemap/' . $value;
    }
}
include_once template(TPLDIR . 'sitemap.htm', 1);