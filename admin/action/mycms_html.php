<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_html.php Mr.Kwok
 * Created Time:2018/10/31 10:08
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('htmltimesubmit')) {
    //更新时间设置
    //$_MGLOBAL['db']->updatetable('settings', array('value' => empty($_POST['htmltime']) ? $_MGLOBAL['timestamp'] : sstrtotime($_POST['htmltime'])), array('variable' => 'htmltime'));
    $htmltime = empty($_POST['htmltime']) ? $_MGLOBAL['timestamp'] : sstrtotime($_POST['htmltime']);
    $_MGLOBAL['db']->query('REPLACE INTO ' . tname('settings') . ' (`variable`, `value`) VALUES (\'htmltime\',' . $htmltime . ')');
    include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updatesettingcache();
    showmessage(1, '已经成功设置全站HTML立即刷新操作', $theurl);
}
$urlarr = array();
$i = 0;
if (submitcheck('makehtmlsubmit')) {
    $notice = '正在生成首页，完成后程序将自动跳转，请稍候.....';
    if (!empty($_POST['index'])) {
        $urlarr[$i]['url'] = array(MURL . '/main.php?php-1');
        $urlarr[$i]['name'] = '网站首页 index.html';
        if (defined('WAPURL')) {
            $i++;
            $urlarr[$i]['url'] = array(MURL . '/wap' . '/main.php?php-1');
            $urlarr[$i]['name'] = '网站手机版首页 index.html';
        }
    }
    $gourl = $theurl . '-pernum-' . $_POST['pernum'];
    if (!empty($_POST['category'])) {
        $gourl .= '-category-1';
    }
    if (!empty($_POST['topic'])) {
        $gourl .= '-topic-1';
    }
    if (!empty($_POST['tag'])) {
        $gourl .= '-tag-1';
    }
    if (!empty($_POST['article'])) {
        $gourl .= '-article-1';
    }
} elseif (!empty($_POST['category']) && !empty($_POST['topic']) && !empty($_POST['tag']) && !empty($_POST['article'])) {
    showmessage(1, '已经成功的生成了全部HTML', $theurl);
}
$pernum = empty($_MGET['pernum']) ? 10 : intval($_MGET['pernum']);
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$thispage = ($page - 1) * $pernum;
if (!empty($_MGET['category'])) {
    $notice = '正在生成分类页，完成后程序将自动跳转，请稍候.....';
    foreach ($_MGLOBAL['category'] as $value) {
        $i++;
        $urlarr[$i]['url'] = MURL . '/main.php?action-category-catid-' . $value['catid'] . '-php-1';
        $urlarr[$i]['name'] = 'PC版分类ID：' . $value['catid'] . ' 分类名：' . $value['name'];
        if (defined('WAPURL')) {
            $i++;
            $urlarr[$i]['url'] = MURL . '/wap' . '/main.php?action-category-catid-' . $value['catid'] . '-php-1';
            $urlarr[$i]['name'] = '手机版分类ID：' . $value['catid'] . ' 分类名：' . $value['name'];
        }
    }
    $gourl = $theurl . '-pernum-' . $pernum;
    if (!empty($_MGET['topic'])) {
        $gourl .= '-topic-1';
    }
    if (!empty($_MGET['tag'])) {
        $gourl .= '-tag-1';
    }
    if (!empty($_MGET['article'])) {
        $gourl .= '-article-1';
    }
} elseif (!empty($_MGET['topic'])) {
    $notice = '正在生成专题页，完成后程序将自动跳转，请稍候.....';
    $query = $_MGLOBAL['db']->query('SELECT id,name,htmlpath FROM ' . tname('topic') . ' LIMIT ' . $thispage . ' , ' . $pernum);
    if (empty($query->num_rows)) {
        $gourl = $theurl . '-pernum-' . $pernum;
        if (!empty($_MGET['tag'])) {
            $gourl .= '-tag-1';
        }
        if (!empty($_MGET['article'])) {
            $gourl .= '-article-1';
        }
    } else {
        while ($value = $_MGLOBAL['db']->fetch_array($query)) {
            $i++;
            $urlarr[$i]['url'] = MURL . '/main.php?action-topic-name-' . $value['htmlpath'] . '-php-1';
            $urlarr[$i]['name'] = '专题ID：' . $value['id'] . ' 名称：' . $value['name'];
            if (defined('WAPURL')) {
                $i++;
                $urlarr[$i]['url'] = MURL . '/wap' . '/main.php?action-topic-name-' . $value['htmlpath'] . '-php-1';
                $urlarr[$i]['name'] = '专题ID：' . $value['id'] . ' 名称：' . $value['name'] . '(手机版)';
            }
        }
        $page++;
        $next = '';
        if (!empty($_MGET['tag'])) {
            $next = '-tag-1';
        }
        if (!empty($_MGET['article'])) {
            $next .= '-article-1';
        }
        $gourl = $theurl . '-topic-1-pernum-' . $pernum . '-page-' . $page . $next;//跳转页
    }
} elseif (!empty($_MGET['tag'])) {
    $notice = '正在生成标签页，完成后程序将自动跳转，请稍候.....';
    $query = $_MGLOBAL['db']->query('SELECT tagid,tagname FROM ' . tname('tags') . ' LIMIT ' . $thispage . ' , ' . $pernum);
    if (empty($query->num_rows)) {
        $gourl = $theurl . '-pernum-' . $pernum;
        if (!empty($_MGET['article'])) {
            $gourl .= '-article-1';
        } else {
            showmessage(1, '已经成功的生成了全部HTML', $theurl);
        }
    } else {
        while ($value = $_MGLOBAL['db']->fetch_array($query)) {
            $i++;
            $urlarr[$i]['url'] = MURL . '/main.php?action-tag-tagid-' . $value['tagid'] . '-php-1';
            $urlarr[$i]['name'] = '标签ID：' . $value['tagid'] . ' 名称：' . $value['tagname'];
            if (defined('WAPURL')) {
                $i++;
                $urlarr[$i]['url'] = MURL . '/wap' . '/main.php?action-tag-tagid-' . $value['tagid'] . '-php-1';
                $urlarr[$i]['name'] = '标签ID：' . $value['tagid'] . ' 名称：' . $value['tagname'] . '(手机版)';
            }
        }
        $page++;
        if (!empty($_MGET['article'])) {
            $next = '-article-1';
        } else {
            $next = '';
        }
        $gourl = $theurl . '-tag-1-pernum-' . $pernum . '-page-' . $page . $next;//跳转页
    }
} elseif (!empty($_MGET['article'])) {
//内容页
    $notice = '正在生成内容页，完成后程序将自动跳转，请稍候.....';
    $query = $_MGLOBAL['db']->query('SELECT id,subject FROM ' . tname('article') . ' WHERE folder=0  LIMIT ' . $thispage . ' , ' . $pernum);
    if (empty($query->num_rows)) {
        showmessage(1, '已经成功的生成了全部HTML', $theurl);
    } else {
        while ($value = $_MGLOBAL['db']->fetch_array($query)) {
            $i++;
            $urlarr[$i]['url'] = MURL . '/main.php?action-article-id-' . $value['id'] . '-php-1';
            $urlarr[$i]['name'] = '文章ID：' . $value['id'] . ' 标题：' . $value['subject'];
            if (defined('WAPURL')) {
                $i++;
                $urlarr[$i]['url'] = MURL . '/wap' . '/main.php?action-article-id-' . $value['id'] . '-php-1';
                $urlarr[$i]['name'] = '文章ID：' . $value['id'] . ' 标题：' . $value['subject'] . '(手机版)';
            }
        }
    }
    $page++;
    $gourl = $theurl . '-article-1-pernum-' . $pernum . '-page-' . $page;//跳转页
}
$urljosn = json_encode($urlarr);
include_once template(TPLDIR . 'html.htm', 1);