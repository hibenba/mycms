<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_tag.php Mr.Kwok
 * Created Time:2018/10/26 19:33
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
$wheresqlstr = '';
$perpage = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示TAG数和文章数一样
if (submitcheck('modifytag')) {
    //编辑TAG
    if (!empty($_POST['tagid'])) {
        $_MGLOBAL['db']->updatetable('tags', array('tagname' => strfilter($_POST['tagname'])), array('tagid' => intval($_POST['tagid'])));
    } else {
        if (!empty($_POST['tagname'])) {
            tags_insert($_POST['tagname'], '');
        }
    }
    sheader($refer);//回到原页面
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $id = intval($_MGET['delete']);
    $_MGLOBAL['db']->deletetable('tags_map', array('tagid' => $id));//删除tag关联表
    $_MGLOBAL['db']->deletetable('tags', array('tagid' => $id));//删除tag表
    sheader($refer);//回到原页面
}
if (!empty($_MGET['closed'])) {
    //单个禁用
    $_MGLOBAL['db']->updatetable('tags', array('close' => 1), array('tagid' => intval($_MGET['closed'])));
    sheader($refer);//回到原页面
}
if (!empty($_MGET['open'])) {
    //单个启用
    $_MGLOBAL['db']->updatetable('tags', array('close' => 0), array('tagid' => intval($_MGET['open'])));
    sheader($refer);//回到原页面
}
if (submitcheck('opsubmit')) {
    //批量操作
    if (empty($_POST['tagarr'])) {
        showmessage(3, '请选择你要操作的标签!', $theurl);
    }
    switch ($_POST['optag']) {
        case 'close'://禁用
            $sqlarr = array('close' => 1);
            break;
        case 'open'://恢复
            $sqlarr = array('close' => 0);
            break;
        case 'delete'://删除
            foreach ($_POST['tagarr'] as $value) {
                $id = intval($value);
                $_MGLOBAL['db']->deletetable('tags_map', array('tagid' => $id));//删除tag关联表
                $_MGLOBAL['db']->deletetable('tags', array('tagid' => $id));//删除tag表
            }
            showmessage(1, '您已经成功删除标签和文章关系表!', $theurl);
            break;
        default:
            showmessage(3, '请选择要操作的类型,如:批量禁用', $theurl);
            break;
    }
    if ($sqlarr) {
        foreach ($_POST['tagarr'] as $value) {
            $id = intval($value);
            if ($id) {
                $_MGLOBAL['db']->updatetable('tags', $sqlarr, array('tagid' => $id));
            }
        }
    }
    sheader($refer);//回到原页面
}
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $perpage;
$count = $_MGLOBAL['db']->getcount('tags', array('close' => 0));//正常标签数
$auditnum = $_MGLOBAL['db']->getcount('tags', array('close' => 1));//禁用标签数
$urlarr = array('action' => 'tag');//分页地址
$_MGET['closed'] = empty($_MGET['closed']) ? 0 : 1;
$_MGET['order'] = empty($_MGET['order']) ? '' : $_MGET['order'];
$order = '`dateline` DESC';
if ($_MGET['order'] == 'id') {
    $order = '`tagid` ASC';
    $urlarr['order'] = 'id';
}
if ($_MGET['closed'] == 1) {
    $count = $auditnum;
    $wheresqlstr = ' where close=1';
    $urlarr['closed'] = 1;
}
if (submitcheck('searchkeyword')) {
    //搜索标签
    $_POST['keyword'] = strfilter($_POST['keyword']);
    $perpage = 999;
    $wheresqlstr = ' WHERE  `tagname` LIKE \'%' . $_POST['keyword'] . '%\'';
}
$tags = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('tags') . $wheresqlstr . ' ORDER BY  ' . $order . ' LIMIT ' . $thispage . ',' . $perpage);
while ($thetag = $_MGLOBAL['db']->fetch_array($query)) {
    $thetag['tagnum'] = $_MGLOBAL['db']->getcount('tags_map', array('tagid' => $thetag['tagid']));//统计关联的文章数量
    $tags[] = $thetag;
}
$multipage = '<div class="left count">共有' . $count . '个标签！</div>';
if ($count > $perpage) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage .= multipage($count, $perpage, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
}
include template(TPLDIR . 'tag.htm', 1);