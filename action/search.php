<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 search.php Mr.Kwok
 * Created Time:2018/9/21 11:55
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
$title = $keywords = $description = '站内搜索';
$title .= '_' . $_MCONFIG['sitename'];
connectMysql();
$searchlist = array();
$booklist = '';
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'img.func.php');//图片处理函数
//include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
//有安全检测不是太稳定，暂时取消
if (!empty($_POST['q'])) {
    $searchkeyword = maddslashes(strfilter($_POST['q']));
    if (strlen($searchkeyword) > 2 && strlen($searchkeyword) < 30) {
        $keywords = $_MGLOBAL['db']->fetch_first('SELECT `sid`,`updatetime`,`content` FROM ' . tname('search') . ' WHERE `keywords` =\'' . $searchkeyword . '\'  LIMIT 1');
        if (empty($keywords) || ($_MGLOBAL['timestamp'] - 3600) > $keywords['updatetime']) {
            $query = $_MGLOBAL['db']->query('SELECT i.`id`,i.`subject`,i.`username`,i.`dateline`,i.`cover`,ii.`content` FROM ' . tname('article') . ' as i LEFT JOIN ' . tname('article_content') . ' as ii ON (i.`id`=ii.`id`) WHERE i.`subject` LIKE  \'%' . $searchkeyword . '%\' or  ii.`content` LIKE  \'%' . $searchkeyword . '%\' ORDER BY i.`lastpost` DESC LIMIT 99');
            while ($row = $_MGLOBAL['db']->fetch_array($query)) {
                $row['url'] = geturl('action/article/id/' . $row['id']);
                $row['content'] = cutstr(format_string($row['content']), 350);
                $searchlist[] = $row;
            }
            if (empty($keywords)) {
                $sid = $_MGLOBAL['db']->inserttable('search', array('sid' => 0, 'keywords' => $searchkeyword, 'dateline' => $_MGLOBAL['timestamp'], 'updatetime' => $_MGLOBAL['timestamp'], 'content' => json_encode($searchlist, JSON_UNESCAPED_UNICODE), 'count' => 1), 1);//写入表
            } elseif (!empty($searchlist)) {
                $_MGLOBAL['db']->updatetable('search', array('content' => json_encode($searchlist, JSON_UNESCAPED_UNICODE), 'updatetime' => $_MGLOBAL['timestamp']), array('sid' => $keywords['sid']));//更新表
                $sid = $keywords['sid'];
            }
            $_MHTML['id'] = empty($sid) ? 0 : intval($sid);
            $booklist = $searchlist;
        } else {
            sheader(geturl('action/search/id/' . $keywords['sid']));
        }
    } else {
        $booklist = '<h1>输入的关键字请在2~10个汉字之间。</h1>';
    }
} elseif (!empty($_MGET['id'])) {
    $keywords = $_MGLOBAL['db']->fetch_first('SELECT `sid`,`keywords`,`content` FROM ' . tname('search') . ' WHERE `sid` =' . intval($_MGET['id']));
    if (empty($keywords['content'])) {
        $booklist = '<h1>没有搜索到相关内容，请更换关键字重试！</h1>';
    } else {
        $_MHTML['id'] = $keywords['sid'];
        $title = $keywords['keywords'] . '_' . $title;
        $booklist = json_decode($keywords['content'], true);
    }
}
include template($_MGET['action']);
ob_out();
if ($_MCONFIG['actions'][$_MGET['action']]['url_model'] == 2 && !empty($_MCONFIG['htmlmode'])) {
    ehtml('make');
} else {
    makecache($_MGLOBAL['content'], $_MHTML);
}