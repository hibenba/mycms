<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_action_logs.php Mr.Kwok
 * Created Time:2018/10/26 23:05
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
$goodcount = $_MGLOBAL['db']->getcount('action_logs', array('name' => 'good'));//点赞
$hotcount = $_MGLOBAL['db']->getcount('action_logs', array('name' => 'hot'));//点赞
$commentount = $_MGLOBAL['db']->getcount('action_logs', array('name' => 'comments'));//评论
$count = $hotcount + $goodcount + $commentount;
$setarticlenum = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示数
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $setarticlenum;
$urlarr = array('action' => 'action_logs');//分页地址
if ($count > $setarticlenum) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage = multi($count, $setarticlenum, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
} else {
    $multipage = '';
}
$actionarr = array();
$aclang = array('comments' => '用户评论', 'hot' => '评论点赞', 'good' => '文章点赞', 'votebook' => '小说推荐');
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('action_logs') . ' ORDER BY id DESC  LIMIT ' . $thispage . ',' . $setarticlenum);
while ($ac = $_MGLOBAL['db']->fetch_array($query)) {
    $ac['action'] = json_decode($ac['action'], true);
    $actionarr[] = $ac;
}
include_once template(TPLDIR . 'action_logs.htm', 1);