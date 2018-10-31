<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_login_logs.php Mr.Kwok
 * Created Time:2018/10/31 10:26
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
$errorcount = $_MGLOBAL['db']->getcount('login_logs', array('state' => 0));//失败日志
$okcount = $_MGLOBAL['db']->getcount('login_logs', array('state' => 1));//失败日志
$count = $errorcount + $okcount;
$setarticlenum = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示数
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $setarticlenum;
$urlarr = array('action' => 'login_logs');//分页地址
if ($count > $setarticlenum) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage = multipage($count, $setarticlenum, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
} else {
    $multipage = '';
}
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('login_logs') . ' ORDER BY id DESC  LIMIT ' . $thispage . ',' . $setarticlenum);
$logarr = array();
while ($logs = $_MGLOBAL['db']->fetch_array($query)) {
    $logarr[] = $logs;
}
include_once template(TPLDIR . 'login_logs.htm', 1);