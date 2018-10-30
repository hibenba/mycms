<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 topic.php Mr.Kwok
 * Created Time:2018/9/20 16:08
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
if (empty($_MGET['name'])) {
    notfoundmessage('参数错误');
} else {
    $name = strfilter($_MGET['name']);
}
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$_MHTML = array('action' => $_MGET['action'], 'id' => $name, 'page' => $page);
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    $_MGLOBAL['htmlfile'] = gethtmlfile($_MHTML);
    ehtml('get');
} else {
    getcache($_MHTML);
}
connectMysql();//连接数据库
$topic = $_MGLOBAL['db']->fetch_first('SELECT `id`, `name`, `note`, `title`, `dateline`, `lastpost`, `content`, `tpl`, `htmlpath`, `perpage`, `viewnum`, `close` FROM ' . tname('topic') . ' WHERE `htmlpath`=\'' . $name . '\'');
if (empty($topic)) {
    notfoundmessage();
} elseif ($topic['close'] != 0) {
    notfoundmessage('当前专题已禁用！');
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
$thisurl = MURL . geturl('action/topic/name/' . $name);
$thiswapurl = str_replace(MURL, WAPURL, $thisurl);
$topic['title'] = empty($topic['title']) ? $topic['name'] : $topic['title'];
$title = strip_tags($topic['title'] . '_' . $_MCONFIG['sitename']);
$keywords = str_replace(array('_', '-', '，', '。', '、', '|', ',,'), ',', $title);
$description = empty($topic['note']) ? cutstr(format_string($topic['content']), 200) : strip_tags($topic['note']);
$tplname = empty($topic['tpl']) ? 'topic/view' : 'topic/' . $topic['tpl'];
include template($tplname);
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}