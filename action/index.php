<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 index.php Mr.Kwok
 * Created Time:2018/9/20 10:37
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
$_MHTML = array('action' => $_MGET['action'], 'id' => 0, 'page' => 0);
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    $_MGLOBAL['htmlfile'] = gethtmlfile($_MHTML);
    ehtml('get');
} else {
    getcache($_MHTML);
}
$thisurl = MURL . '/';
$thiswapurl = WAPURL . '/';
$friendlinks = array();
connectMysql();//连接数据库
//首页友情连接100条
$query = $_MGLOBAL['db']->query('SELECT name,url,description,logo FROM ' . tname('friendlinks') . ' WHERE `displayorder` >=0 ORDER BY  `displayorder` DESC LIMIT 100');
while ($link = $_MGLOBAL['db']->fetch_array($query)) {
    $friendlinks[] = $link;
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
$title = empty($_MCONFIG['seotitle']) ? $_MCONFIG['sitename'] : $_MCONFIG['seotitle'];
$keywords = $_MCONFIG['seokeywords'];
$description = cutstr(format_string($_MCONFIG['seodescription']), 200);
include template($_MGET['action']);
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}