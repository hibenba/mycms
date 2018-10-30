<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_postarticle.php Mr.Kwok
 * Created Time:2018/10/26 10:53
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if ($_MGLOBAL['grouparr'][$_MGLOBAL['member']['groupid']]['allowpost'] != 1) {
    //检查是否允许发布文章
    showmessage(2, '您没有发布文章的权限，请联系管理员处理！', CPURL);
}
//POST接收开始
if (submitcheck('valuesubmit')) {
    if (strlen($_POST['subject']) < 2) {
        showmessage(3, '您输入的标题长度不符合要求(2~80个字符)！');
    }
    if (strlen($_POST['content']) < 20) {
        showmessage(3, '内容不能为空或者小于10个字，请返回修改！');
    }
    if (empty($_POST['catid'])) {
        showmessage(3, '您没有选择分类，请返回修改');
    }
    if (empty($_POST['id'])) {
        $lastpost = $dateline = sstrtotime($_POST['posttime']);
        $id = 0;
    } else {
        $id = intval($_POST['id']);
        $dateline = sstrtotime($_POST['posttime']);
        $lastpost = $_MGLOBAL['timestamp'];
    }
    $nid = empty($_POST['nid']) ? 0 : intval($_POST['nid']);
    $_POST['digest'] = empty($_POST['digest']) ? 0 : 1;
    $_POST['top'] = empty($_POST['top']) ? 0 : 1;
    $_POST['catid'] = intval($_POST['catid']);
    $_POST['allowreply'] = empty($_POST['allowreply']) ? 0 : 1;
    $_POST['grade'] = empty($_POST['grade']) ? 0 : intval($_POST['grade']);
    $_POST['hash'] = strfilter($_POST['hash']);
    $setsqlarr = array(
        'id' => $id,
        'subject' => cutstr(strip_tags(trim($_POST['subject'])), 80),
        'url' => strfilter($_POST['url']),
        'catid' => $_POST['catid'],
        'uid' => $_MGLOBAL['uid'],
        'username' => $_MGLOBAL['username'],
        'dateline' => $dateline,
        'lastpost' => $lastpost,
        'viewnum' => 0,
        'replynum' => 0,
        'digest' => $_POST['digest'],
        'top' => $_POST['top'],
        'good' => 0,
        'allowreply' => $_POST['allowreply'],
        'hash' => $_POST['hash'],
        'cover' => '',
        'grade' => $_POST['grade']
    );
    //插入数据
    if (!empty($id)) {
        $_MGLOBAL['db']->updatetable('article', $setsqlarr, array('id' => $id));
    } else {
        $id = $_MGLOBAL['db']->inserttable('article', $setsqlarr, 1);
    }
    unset($setsqlarr);
    $setsqlarr = array(
        'nid' => $nid,
        'id' => $id,
        'content' => filters_outcontent($_POST['content']),
        'postip' => $_MGLOBAL['onlineip'],
        'pageorder' => 1
    );
    if (!empty($nid)) {
        $_MGLOBAL['db']->updatetable('article_content', $setsqlarr, array('nid' => $nid));
    } else {
        $_MGLOBAL['db']->inserttable('article_content', $setsqlarr);
    }
    //上传图片处理
    if (!empty($_POST['attnew'])) {
        foreach ($_POST['attnew'] as $value) {
            $_MGLOBAL['db']->updatetable('attachments', array('id' => $id, 'hash' => $_POST['hash']), array('aid' => intval($value)));
        }
    }
    //图片附件
    $getaid = $_MGLOBAL['db']->fetch_first('SELECT aid FROM ' . tname('attachments') . ' WHERE isimage=1 and id=\'' . $id . '\' LIMIT 1');
    if ($getaid['aid']) {
        $_MGLOBAL['db']->updatetable('article', array('cover' => $getaid['aid']), array('id' => $id));
    }
    //TAG 标签处理
    if (!empty($_POST['tags'])) {
        tags_insert($_POST['tags'], $id);
    }
    $iframe = '<iframe width="0" height="0" scrolling="no" src="/main.php?action-article-id-' . $id . '-php-1"></iframe><iframe width="0" height="0" scrolling="no" src="/main.php?action-category-catid-' . $_POST['catid'] . '-php-1"></iframe>';
    if (defined('WAPURL')) {
        $iframe .= '<iframe width="0" height="0" scrolling="no" src="' . WAPURL . '/main.php?action-article-id-' . $id . '-php-1"></iframe><iframe width="0" height="0" scrolling="no" src="' . WAPURL . '/main.php?action-category-catid-' . $_POST['catid'] . '-php-1"></iframe>';
    }
    showmessage(1, '文章发布成功！' . $iframe, CPURL . '?action-article');
}
$hashstr = smd5($_MGLOBAL['uid'] . '/' . $_MGLOBAL['timestamp'] . random(6));//附件识别码
if (!empty($_MGET['id'])) {
    //编辑文章内容
    $id = intval($_MGET['id']);
    $thevalue = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('article') . ' WHERE id=' . $id);
    if (empty($thevalue)) {
        showmessage(2, '编辑的文章不存在，请检查！');
    } else {
        $content = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('article_content') . ' WHERE id=' . $id);
        if (empty($content)) {
            $thevalue['nid'] = $thevalue['content'] = '';
        } else {
            $thevalue = array_merge($thevalue, $content);//合并数据
        }
        $thevalue['tags'] = '';
        $query = $_MGLOBAL['db']->query('SELECT i.tagname FROM ' . tname('tags') . ' as i LEFT JOIN ' . tname('tags_map') . ' as ii  ON (i.tagid=ii.tagid) WHERE ii.articleid=' . $id);
        while ($tag = $_MGLOBAL['db']->fetch_array($query)) {
            $thevalue['tags'] .= ' ' . $tag['tagname'];
        }
        $thevalue['tags'] = empty($thevalue['tags']) ? '' : trim($thevalue['tags']);
        $thevalue['content'] = stripslashes($thevalue['content']);//删除转义
    }
} else {
    $thevalue = array(
        'id' => 0,
        'subject' => '',
        'url' => '',
        'catid' => '',
        'uid' => $_MGLOBAL['uid'],
        'username' => $_MGLOBAL['username'],
        'dateline' => $_MGLOBAL['timestamp'],
        'lastpost' => $_MGLOBAL['timestamp'],
        'viewnum' => 0,
        'replynum' => 0,
        'digest' => 0,
        'top' => 0,
        'good' => 0,
        'allowreply' => 1,
        'hash' => $hashstr,
        'tags' => '',
        'haveattach' => 0,
        'grade' => 0,
        'picid' => 0,
        'nid' => 0,
        'content' => '',
        'postip' => $_MGLOBAL['onlineip'],
        'pageorder' => 1
    );
}
$categorylistarr = array('0' => array('catid' => '', 'name' => '请选择文章分类'));
$query = $_MGLOBAL['db']->query('SELECT catid,name FROM ' . tname('categories'));
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $categorylistarr[] = $value;
}
$checkgrade = explode("\t", $_MCONFIG['checkgrade']);
$thevalue['thisdata'] = sgmdate($thevalue['lastpost']);
$imguserid = empty($id) ? 0 : $id;
$query = $_MGLOBAL['db']->query('SELECT aid,url FROM ' . tname('attachments') . ' WHERE id=' . $imguserid);//未被使用附件
$attunuse = array();
while ($att = $_MGLOBAL['db']->fetch_array($query)) {
    $attunuse[] = $att;
}
include template(TPLDIR . 'postarticle.htm', 1);