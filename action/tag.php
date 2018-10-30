<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 tag.php Mr.Kwok
 * Created Time:2018/9/20 16:46
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
if (empty($_MGET['tagid'])) {
    notfoundmessage('参数错误');
} else {
    $tagid = intval($_MGET['tagid']);
}
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$_MHTML = array('action' => $_MGET['action'], 'id' => $tagid, 'page' => $page);
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    $_MGLOBAL['htmlfile'] = gethtmlfile($_MHTML);
    ehtml('get');
} else {
    getcache($_MHTML);
}
connectMysql();
$tag = $_MGLOBAL['db']->fetch_first('SELECT `tagid`, `tagname`, `dateline`, `close` FROM ' . tname('tags') . ' WHERE `tagid`=' . $tagid);
if (empty($tag)) {
    notfoundmessage();
} elseif ($tag['close'] != 0) {
    showmessage(3, '当前标签已被禁用...', MURL);
}
$thisurl = MURL . geturl('action/tag/tagid/' . $tagid);
$thiswapurl = str_replace(MURL, WAPURL, $thisurl);
$query = $_MGLOBAL['db']->query('SELECT articleid FROM ' . tname('tags_map') . ' WHERE tagid=' . $tagid);
$tagcount = $query->num_rows;//TAG数量统计
$ids = array();
while ($getid = $_MGLOBAL['db']->fetch_array($query)) {
    $ids[] = $getid['articleid'];
}
$articlearr = array();
if (!empty($ids)) {
    $query = $_MGLOBAL['db']->query('SELECT `id`, `subject`, `url`, `catid`, `uid`, `username`, `dateline`, `lastpost`, `viewnum`, `replynum`, `digest`, `top`, `good`, `allowreply`, `hash`, `cover`, `grade`, `folder` FROM ' . tname('article') . ' WHERE `folder`=0 and `id` in(' . implode(',', $ids) . ')');
    while ($article = $_MGLOBAL['db']->fetch_array($query)) {
        $article['url'] = geturl('action/article/id/' . $article['id']);
        $articlearr[] = $article;
    }
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
$title = strip_tags($tag['tagname'] . '_标签_' . $_MCONFIG['sitename']);
$keywords = $tag['tagname'];
$description = cutstr(format_string($tag['tagname']), 200);
include template('tag');
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}