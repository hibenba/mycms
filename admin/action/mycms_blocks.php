<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_blocks.php Mr.Kwok
 * Created Time:2018/10/26 23:11
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
$cachefile = DATA_DIR . 'cache' . DIRECTORY_SEPARATOR . 'block' . DIRECTORY_SEPARATOR . 'article.cache.php';
if (submitcheck('makeblocksubmit')) {
    if (empty($_POST['name'])) {
        showmessage(3, '变量名称不能为空!', '');
    }
    $block = '<!--{block name="article" parameter="';
    if (!empty($_POST['catid'])) {
        $block .= '/catid/' . implode(',', $_POST['catid']);
    }
    if (!empty($_POST['uid'])) {
        $block .= '/uid/' . $_POST['uid'];
    }
    if ($_POST['isid'] == 1) {
        if (empty($_POST['haveid'])) {
            if (empty($_POST['name'])) {
                showmessage(3, '文章ID不能为空!', '');
            }
        } else {
            $block .= '/id/' . $_POST['haveid'];
        }
    }
    if (!empty($_POST['cover'])) {
        $block .= '/cover/1';
    }
    if (!empty($_POST['digest'])) {
        $block .= '/digest/' . $_POST['digest'];
    }
    if (!empty($_POST['top'])) {
        $block .= '/top/' . $_POST['top'];
    }
    if (!empty($_POST['grade'])) {
        $_POST['grade'] = intval($_POST['grade']) + 1;
        $block .= '/grade/' . $_POST['grade'];
    }
    if (!empty($_POST['subjectlen'])) {
        $block .= '/subjectlen/' . $_POST['subjectlen'];
        if (!empty($_POST['subjectdot'])) {
            $block .= '/subjectdot/1';
        }
    }
    if (!empty($_POST['getcon'])) {
        $block .= '/showdetail/' . $_POST['getcon'];
        if (!empty($_POST['conlen'])) {
            $block .= '/messagelen/' . $_POST['conlen'];
        }
        if (!empty($_POST['condot'])) {
            $block .= '/messagedot/' . $_POST['condot'];
        }
    }
    $_POST['odertype1'] = empty($_POST['odertype1']) ? 'dateline' : $_POST['odertype1'];
    $order = empty($_POST['oder1']) ? ' DESC' : ' ASC';
    $block .= '/order/i.' . $_POST['odertype1'] . $order;
    if (!empty($_POST['odertype2'])) {
        $order = empty($_POST['oder2']) ? ' DESC' : ' ASC';
        $block .= ',i.' . $_POST['odertype2'] . $order;
    }
    if (!empty($_POST['odertype3'])) {
        $order = empty($_POST['oder3']) ? ' DESC' : ' ASC';
        $block .= ',i.' . $_POST['odertype3'] . $order;
    }
    $limitstart = empty($_POST['limitstart']) ? 0 : intval($_POST['limitstart']);
    $limit = empty($_POST['limit']) ? 10 : intval($_POST['limit']);
    $block .= '/limit/' . $limitstart . ',' . $limit;
    $cachetime = empty($_POST['cachetime']) ? 0 : intval($_POST['cachetime']);
    $block .= '/cachetime/' . $cachetime;
    $block .= '/cachename/' . $_POST['name'] . '"}-->';
    $block = str_replace('"/', '"', $block);
    $block .= '
<ul>
<!--{loop $_MBLOCK[' . $_POST['name'] . '] $value}-->
<li><a href="$value[url]" title="$value[subjectall]">$value[subject]</a></li>
<!--{/loop}-->
</ul>';
    $block = htmlspecialchars($block);
}
$checkgrade = empty($_MCONFIG['checkgrade']) ? array('', '', '', '', '') : explode("\t", $_MCONFIG['checkgrade']);
$_MGET['type'] = empty($_MGET['type']) ? '' : $_MGET['type'];
include_once template(TPLDIR . 'blocks.htm', 1);