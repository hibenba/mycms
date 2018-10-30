<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 category.php Mr.Kwok
 * Created Time:2018/9/20 11:36
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
if (empty($_MGET['catid'])) {
    notfoundmessage('参数错误');
} else {
    $catid = intval($_MGET['catid']);
}
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$_MHTML = array('action' => $_MGET['action'], 'id' => $catid, 'page' => $page);
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    $_MGLOBAL['htmlfile'] = gethtmlfile($_MHTML);
    ehtml('get');
} else {
    getcache($_MHTML);
}
$thiscat = empty($thecat[$catid]) ? notfoundmessage() : $thecat[$catid];
$thispage = ($page - 1) * $thiscat['perpage'];
$thisurl = MURL . geturl('action/category/catid/' . $catid);
$thiswapurl = str_replace(MURL, WAPURL, $thisurl);
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'list.func.php');//列表页处理函数
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
connectMysql();//连接数据库
$listcount = $_MGLOBAL['db']->getcount('article', array('folder' => 0, 'catid' => $catid));//当前分类下文章总数
$urlarr = array('action' => 'category', 'catid' => $catid);
$multipage = multipage($listcount, $thiscat['perpage'], $page, $urlarr);
$query = $_MGLOBAL['db']->query('SELECT `id` FROM ' . tname('article') . ' WHERE `folder`=0 AND `catid` = ' . $catid . ' ORDER BY  `lastpost` DESC LIMIT ' . $thispage . ',' . $thiscat['perpage']);
$ids = $lists = $article = array();
while ($idarr = $_MGLOBAL['db']->fetch_array($query)) {
    $ids[] = $idarr['id'];
}
$cutstrnum = defined('IN_WAP') ? 100 : 500;//内容截取数量
if (!empty($ids)) {
    $query = $_MGLOBAL['db']->query('SELECT i.`id`, i.`subject`, i.`url`, i.`catid`, i.`uid`, i.`username`, i.`dateline`, i.`lastpost`, i.`viewnum`, i.`replynum`, i.`digest`, i.`top`, i.`good`, i.`cover`, i.`grade`,ii.`nid`,ii.`id`, ii.`content` FROM ' . tname('article') . ' as i LEFT JOIN ' . tname('article_content') . ' as ii  ON (i.id=ii.id) WHERE i.id in(' . implode(',', $ids) . ') ORDER BY  i.`lastpost` DESC');
    while ($article = $_MGLOBAL['db']->fetch_array($query)) {
        $aid = intval($article['cover']);
        if ($aid) {
            $att = $_MGLOBAL['db']->fetch_first('SELECT `url` FROM ' . tname('attachments') . ' WHERE `aid`=' . $aid);
            $article['image'] = $att['url'];
        }
        $article['url'] = geturl('action/article/id/' . $article['id']);
        $article['content'] = cutstr(format_string($article['content']), $cutstrnum, true);
        $lists[] = $article;
    }
}
$thiscat['title'] = empty($thiscat['title']) ? $thiscat['name'] : $thiscat['title'];
$title = strip_tags($thiscat['title'] . '_' . $_MCONFIG['sitename']);
$keywords = strip_tags(str_replace(array('_', '-', '，', '。', '、', '|', ',,'), ',', $thiscat['title']) . ',' . $thiscat['name']);
$description = cutstr(format_string($thiscat['note']), 200);
$tplname = empty($thiscat['tpl']) ? 'category' : $thiscat['tpl'];//分类自己定义模板
include template($tplname);
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}