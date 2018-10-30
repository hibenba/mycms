<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_article.php Mr.Kwok
 * Created Time:2018/9/26 15:12
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
$listcount = $count = $_MGLOBAL['db']->getcount('article', array('folder' => 0));//已发布
$auditnum = $_MGLOBAL['db']->getcount('article', array('folder' => 1));//待审箱
$trashnum = $_MGLOBAL['db']->getcount('article', array('folder' => 2));//垃圾箱
$digestcount = $_MGLOBAL['db']->getcount('article', array('digest' => 1));//精华
$_MGET['order'] = empty($_MGET['order']) ? '' : $_MGET['order'];
$order = '`top` DESC ,`lastpost` DESC';
//分页处理开始
$setarticlenum = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示文章数
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $setarticlenum;
$urlarr = array('action' => 'article');//分页地址
$folder = empty($_MGET['folder']) ? 0 : intval($_MGET['folder']);
$wheresqlstr = ' where folder=' . $folder;

//条件排序
if (!empty($_MGET['order'])) {
    if ($_MGET['order'] == 'id') {
        $order = '`id` ASC';
    }
    if ($_MGET['order'] == 'dateline') {
        $order = '`dateline` ASC';
    }
    if ($_MGET['order'] == 'comment') {
        $order = '`replynum` DESC';
    }
    if ($_MGET['order'] == 'view') {
        $order = '`viewnum` DESC';
    }
}
if ($folder == 1) {
    $count = $auditnum;
    $urlarr['folder'] = 1;
}
if ($folder == 2) {
    $count = $trashnum;
    $urlarr['folder'] = 2;
}
if (!empty($_MGET['digestlist'])) {
    $urlarr['digestlist'] = 1;
    $count = $digestcount;
    $wheresqlstr .= ' and digest=1';
}
if (!empty($_MGET['uid'])) {
    $uid = intval($_MGET['uid']);
    $urlarr['uid'] = $uid;
    $count = $_MGLOBAL['db']->getcount('article', array('folder' => 0, 'uid' => $uid));//用户已发布;
    $wheresqlstr .= ' and uid=' . $uid;
}
if (!empty($_MGET['catid'])) {
    $catid = intval($_MGET['catid']);
    $urlarr['catid'] = $catid;
    $count = $_MGLOBAL['db']->getcount('article', array('folder' => 0, 'catid' => $catid));//用户已发布;
    $wheresqlstr .= ' and catid=' . $catid;
}
if (submitcheck('searchkeyword')) {
    //搜索文章
    $_POST['keyword'] = strfilter($_POST['keyword']);
    $setarticlenum = 999;
    $wheresqlstr = ' WHERE  `subject` LIKE \'%' . $_POST['keyword'] . '%\'';
}
//分页处理结束

//操作开始
if (submitcheck('opsubmit')) {
    //批量操作
    if (empty($_POST['articlearr'])) {
        showmessage(3, '请选择你要操作的文章!', $theurl);
    }
    switch ($_POST['oparticle']) {
        case 'top'://置顶
            $sqlarr = array('top' => 1);
            break;
        case 'untop'://取消置顶
            $sqlarr = array('top' => 0);
            break;
        case 'digest'://精华
            $sqlarr = array('digest' => 1);
            break;
        case 'undigest'://取消精华
            $sqlarr = array('digest' => 0);
            break;
        case 'delete'://移动到回收站
            $sqlarr = array('folder' => 2);
            break;
        case 'reviewed'://恢复正常
            $sqlarr = array('folder' => 0);
            break;
        case 'deleted'://彻底删除
            foreach ($_POST['articlearr'] as $value) {
                delarticle(intval($value));
            }
            sheader($_SERVER['REQUEST_URI']);
            break;
        default:
            showmessage(3, '请选择要操作的类型,如:批量精华', $theurl);
            break;
    }
    if ($sqlarr) {
        foreach ($_POST['articlearr'] as $value) {
            $id = intval($value);
            if ($id) {
                $_MGLOBAL['db']->updatetable('article', $sqlarr, array('id' => $id));
            }
        }
    }
    sheader($refer);
}
if (submitcheck('movecatsubmit')) {
    //移动分类
    if (empty($_POST['articlearr'])) {
        showmessage(3, '请选择你要操作的文章!', $_SERVER['REQUEST_URI']);
    } elseif (empty($_POST['move_cat'])) {
        showmessage(3, '请选择文章将要移动的分类!', $_SERVER['REQUEST_URI']);
    } else {
        foreach ($_POST['articlearr'] as $value) {
            $id = intval($value);
            if ($id) {
                $_MGLOBAL['db']->updatetable('article', array('catid' => intval($_POST['move_cat'])), array('id' => $id));
            }
        }
        sheader($_SERVER['REQUEST_URI']);
    }
}
if (submitcheck('setsubmit')) {
    //应用文章显示数
    if ($_POST['setarticlenum'] > 1000) {
        $setarticlenum = 1000;
    } elseif ($_POST['setarticlenum'] < 1) {
        $setarticlenum = 1;
    } else {
        $setarticlenum = intval($_POST['setarticlenum']);
    }
    $_MGLOBAL['db']->query("REPLACE INTO " . tname('settings') . " (variable, value) VALUES ('setarticlenum', '$setarticlenum')");
    include_once(M_ROOT . 'function/cache.func.php');
    updatesettingcache();//更新设置缓存
    sheader($_SERVER['REQUEST_URI']);
}
if (submitcheck('modifytag')) {
    //编辑TAG
    if (!empty($_POST['articleid'])) {
        tags_insert($_POST['tags'], intval($_POST['articleid']));
    }
    sheader($_SERVER['REQUEST_URI']);
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $_MGLOBAL['db']->updatetable('article', array('folder' => 2), array('id' => intval($_MGET['delete'])));
    sheader($refer);
}
if (!empty($_MGET['recovery'])) {
    //恢复文章
    $_MGLOBAL['db']->updatetable('article', array('folder' => 0), array('id' => intval($_MGET['recovery'])));
    sheader($refer);
}
if (!empty($_MGET['dump'])) {
    //彻底删除
    delarticle(intval($_MGET['dump']));
    showmessage(1, '您已经成功删除文章与文章相关的附件TAG等!', $theurl . '-folder-2');
}
if (!empty($_MGET['digest'])) {
    //单个精华
    $_MGLOBAL['db']->updatetable('article', array('digest' => 1), array('id' => intval($_MGET['digest'])));
    sheader($refer);
}
if (!empty($_MGET['undigest'])) {
    //取消精华
    $_MGLOBAL['db']->updatetable('article', array('digest' => 0), array('id' => intval($_MGET['undigest'])));
    sheader($refer);
}
if (!empty($_MGET['top'])) {
    //单个置顶
    $_MGLOBAL['db']->updatetable('article', array('top' => 1), array('id' => intval($_MGET['top'])));
    sheader($refer);
}
if (!empty($_MGET['untop'])) {
    //取消置顶
    $_MGLOBAL['db']->updatetable('article', array('top' => 0), array('id' => intval($_MGET['untop'])));
    sheader($refer);
}
if ($count > $setarticlenum) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage = multipage($count, $setarticlenum, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
} else {
    $multipage = '';
}
//操作结束

//生成列表页
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('article') . $wheresqlstr . ' ORDER BY ' . $order . '  LIMIT ' . $thispage . ',' . $setarticlenum);
$articles = array();
while ($article = $_MGLOBAL['db']->fetch_array($query)) {
    $article['catname'] = $_MGLOBAL['category'][$article['catid']]['name'];
    $article['tags'] = $article['tagnames'] = '';
    $queryTag = $_MGLOBAL['db']->query('SELECT i.tagid,ii.tagname FROM ' . tname('tags_map') . ' as i left join ' . tname('tags') . ' as ii on i.tagid=ii.tagid where i.articleid=' . $article['id']);
    while ($tag = $_MGLOBAL['db']->fetch_array($queryTag)) {
        $article['tags'] .= ' <a href="' . geturl('action/tag/tagid/' . $tag['tagid']) . '" target="_blank">' . $tag['tagname'] . '</a> ';
        $article['tagnames'] .= $tag['tagname'] . ' ';
    }
    $articles[] = $article;
}
//生成分类下拉列表
$move_cat = '';
foreach ($_MGLOBAL['category'] as $value) {
    $move_cat .= '<option value="' . $value['catid'] . '">' . $value['name'] . '</option>';
}
include_once template(TPLDIR . 'article.htm', 1);