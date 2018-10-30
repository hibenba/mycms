<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 article.php Mr.Kwok
 * Created Time:2018/9/20 13:34
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
if (empty($_MGET['id'])) {
    notfoundmessage('参数错误');
} else {
    $id = intval($_MGET['id']);
}
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$_MHTML = array('action' => $_MGET['action'], 'id' => $id, 'page' => $page);
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    $_MGLOBAL['htmlfile'] = gethtmlfile($_MHTML);
    ehtml('get');
} else {
    getcache($_MHTML);
}
connectMysql();//连接数据库
$news = $_MGLOBAL['db']->fetch_first('SELECT i.`id`, i.`subject`, i.`url`, i.`catid`, i.`uid`, i.`username`, i.`dateline`, i.`lastpost`, i.`viewnum`, i.`replynum`, i.`digest`, i.`top`, i.`good`, i.`cover`, i.`grade`,i.`folder`,ii.`nid`,ii.`id`, ii.`content` FROM ' . tname('article') . ' as i LEFT JOIN ' . tname('article_content') . ' as ii  ON (i.`id`=ii.`id`) WHERE i.`id`=' . $id);
if (empty($news)) {
    notfoundmessage();
} elseif ($news['folder'] != 0) {
    showmessage(3, '文章已删除或者正在审核中...', MURL);
}
$thisurl = MURL . geturl('action/article/id/' . $id);
$thiswapurl = str_replace(MURL, WAPURL, $thisurl);
@include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');//载入cookie处理函数
@include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'news.func.php');//文章专用函数处理如：查看数
if (freshcookie($id)) updateviewnum($id);//更新统计数
//GET TAG
$tags = $keywords = $tagid = array();
$query = $_MGLOBAL['db']->query('SELECT i.`tagname`,i.`tagid` FROM ' . tname('tags') . ' as i LEFT JOIN ' . tname('tags_map') . ' as ii  ON (i.`tagid`=ii.`tagid`) WHERE ii.`articleid`=' . $id);
while ($newtagarr = $_MGLOBAL['db']->fetch_array($query)) {
    $tagid[] = $newtagarr['tagid'];
    $tags['name'][] = '|' . $newtagarr['tagname'] . '|';
    $keywords[] = $newtagarr['tagname'];
    $tags['link'][] = '<a href="' . geturl('action/tag/tagid/' . $newtagarr['tagid']) . '" title="' . $newtagarr['tagname'] . '" target="_blank">' . $newtagarr['tagname'] . '</a>';
}
//相关文章推荐
if (!empty($tagid)) {
    $query = $_MGLOBAL['db']->query('SELECT i.id,i.subject FROM ' . tname('article') . ' as i WHERE i.`id` in(SELECT  ii.`articleid` FROM  ' . tname('tags_map') . ' as ii WHERE  ii.`tagid` IN (' . implode(',', $tagid) . ')) and folder=0 group by i.id ORDER BY  i.`lastpost` DESC  LIMIT 30');
    while ($related = $_MGLOBAL['db']->fetch_array($query)) {
        if ($id != $related['id']) {
            $related['url'] = geturl('action/article/id/' . $related['id']);
            $relatedarr[] = $related;
        }
    }
}
//给内容关键字加链接
if (!empty($_MCONFIG['tagshow']) && !empty($tags)) {
    $news['content'] = preg_replace($tags['name'], $tags['link'], $news['content'], 1);//使用正则替换1次
}
$news['content'] = str_replace('<img src=', '<img alt="' . $news['subject'] . '" src=', stripslashes($news['content']));//内容图片加alt
//上下篇文章查询
$prearticle = $nexarticle = '';
$pre = $_MGLOBAL['db']->fetch_first('SELECT `id`,`subject` FROM  ' . tname('article') . ' WHERE `folder`=0 and `id`<' . $id . ' ORDER BY `id` DESC LIMIT 1');//上一篇
if ($pre['id']) {
    $prearticle = '<a href="' . geturl('action/article/id/' . $pre['id']) . '" id="prearticle">' . $pre['subject'] . '</a>';
}
$nex = $_MGLOBAL['db']->fetch_first('SELECT `id`,`subject` FROM  ' . tname('article') . ' WHERE `folder`=0 and `id`>' . $id . ' ORDER BY `id` ASC LIMIT 1');//下一篇
if ($nex['id']) {
    $nexarticle = '<a href="' . geturl('action/article/id/' . $nex['id']) . '" id="nexarticle">' . $nex['subject'] . '</a>';
}
//文章评论
$count = $_MGLOBAL['db']->getcount('comments', array('id' => $id, 'status' => 0));//评论数
if ($news['replynum'] !== $count) {
    $_MGLOBAL['db']->updatetable('article', array('replynum' => $count), array('id' => $id));//评论数量校对
}
if ($count > 0) {
    $query = $_MGLOBAL['db']->query('SELECT `cid`,`uid`,`username`,`dateline`,`message`,`hot`,`hideauthor` FROM ' . tname('comments') . ' WHERE `id`=' . $id . ' and `status`=0 ORDER BY `cid` DESC LIMIT 30');
    $comments = array();
    $i = $count;
    while ($comment = $_MGLOBAL['db']->fetch_array($query)) {
        $comment['i'] = $i;
        $comments[] = $comment;
        $i--;
    }
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
$title = strip_tags($news['subject'] . '_' . $thecat[$news['catid']]['name'] . '_' . $_MCONFIG['sitename']);
$keywords = empty($keywords) ? array(strip_tags($news['subject']), $thecat[$news['catid']]['name']) : $keywords;
$keywords = implode(',', str_replace(array('_', '-', '，', '。', '、', '|', ',,'), ',', $keywords));
$description = cutstr(format_string($news['content']), 200);
$tplname = empty($thecat[$news['catid']]['viewtpl']) ? 'article' : $thecat[$news['catid']]['viewtpl'];
include template($tplname);
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}