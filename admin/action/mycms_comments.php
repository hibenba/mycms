<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_comments.php Mr.Kwok
 * Created Time:2018/10/26 19:19
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if ($_MGLOBAL['grouparr'][$_MGLOBAL['member']['groupid']]['allowcomment'] != 1) {
    //检查是否允许管理评论
    showmessage(2, '您没有管理评论的权限，请联系管理员处理！', CPURL);
}
if (submitcheck('modifycomment')) {
    //编辑评论
    $_POST['comment'] = strfilter(trim(strip_tags($_POST['comment'])));
    $_POST['subject'] = strfilter(trim(strip_tags($_POST['subject'])));
    if (!empty($_POST['commentid']) && !empty($_POST['comment'])) {
        $_MGLOBAL['db']->updatetable('comments', array('subject' => $_POST['subject'], 'hot' => intval($_POST['commenthot']), 'message' => $_POST['comment']), array('cid' => intval($_POST['commentid'])));
    }
    sheader($refer);//回到原页面
}
if (!empty($_MGET['status'])) {
    //单个审核
    $_MGLOBAL['db']->updatetable('comments', array('status' => 0), array('cid' => intval($_MGET['status'])));
    sheader($refer);//回到原页面
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $news = $_MGLOBAL['db']->fetch_first('SELECT id FROM ' . tname('comments') . ' WHERE cid=' . intval($_MGET['delete']));
    $_MGLOBAL['db']->deletetable('comments', array('cid' => intval($_MGET['delete'])));
    $_MGLOBAL['db']->query('UPDATE ' . tname('article') . ' SET replynum=replynum-1 WHERE id = ' . $news['id']);//评论数量减少
    sheader($refer);//回到原页面
}
if (submitcheck('opsubmit')) {
    //批量操作
    if (empty($_POST['commentarr'])) {
        showmessage(3, '请选择你要操作的评论!', $theurl);
    }
    if (empty($_POST['optag'])) {
        showmessage(3, '请选择要操作的类型!', $theurl);
    }
    if ($_POST['optag'] = 'delete') {
        foreach ($_POST['commentarr'] as $value) {
            $_MGLOBAL['db']->deletetable('comments', array('cid' => intval($value)));
        }
        showmessage(1, '您已经成功删除标签和文章关系表!', $theurl);
    }
}
$perpage = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示文章数
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $perpage;
$count = $_MGLOBAL['db']->getcount('comments', array('status' => 0));//正常评论数
$auditnum = $_MGLOBAL['db']->getcount('comments', array('status' => 1));//待审核评论数
$urlarr = array('action' => 'comments');//分页地址
if (empty($_MGET['auditnum'])) {
    $wheresqlstr = ' where status=0';
} else {
    $wheresqlstr = ' where status=1';
}
if (!empty($_MGET['aid'])) {
    $_MGET['aid'] = intval($_MGET['aid']);
    $count = $_MGLOBAL['db']->getcount('comments', array('id' => $_MGET['aid']));
    $wheresqlstr = ' where id=' . $_MGET['aid'];
    $urlarr['aid'] = $_MGET['aid'];
}
if (!empty($_MGET['uid'])) {
    $_MGET['uid'] = intval($_MGET['uid']);
    $count = $_MGLOBAL['db']->getcount('comments', array('uid' => $_MGET['uid']));
    $wheresqlstr = ' where uid=' . $_MGET['uid'];
    $urlarr['uid'] = $_MGET['uid'];
}
if (submitcheck('searchkeyword')) {
    //搜索标签
    $perpage = 999;
    $wheresqlstr = ' WHERE  `message` LIKE \'%' . strfilter($_POST['keyword']) . '%\'';
}
$order = '`dateline` DESC';
if (!empty($_MGET['hot'])) {
    $order = '`hot` DESC';
}
if (!empty($_MGET['dateline'])) {
    $order = '`dateline` ASC';
}
$multipage = '<div class="left count">共有' . $count . '个评论！</div>';
if ($count > $perpage) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage .= multipage($count, $perpage, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
}
$thecomments = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('comments') . $wheresqlstr . ' ORDER BY  ' . $order . '  LIMIT ' . $thispage . ',' . $perpage);
while ($co = $_MGLOBAL['db']->fetch_array($query)) {
    if (strlen($co['message']) > 180) {
        $co['content'] = cutstr(format_string($co['message']), 100, 1);
        $co['all'] = 1;
    } else {
        $co['content'] = trim(strip_tags($co['message']));
        $co['all'] = 0;
    }
    $co['num'] = $_MGLOBAL['db']->getcount('comments', array('id' => $co['id']));//文章评论数
    $thecomments[] = $co;
}
include_once template(TPLDIR . 'comments.htm', 1);