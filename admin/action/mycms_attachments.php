<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_attachments.php Mr.Kwok
 * Created Time:2018/10/26 13:09
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if ($_MGLOBAL['grouparr'][$_MGLOBAL['member']['groupid']]['allowpostattach'] != 1) {
    //检查是否允许管理附件
    showmessage(2, '您没有管理附件的权限，请联系管理员处理！', CPURL);
}
//删除附件
if (!empty($_MGET['delete'])) {
    $aid = intval($_MGET['delete']);
    $message = '';
    $att = $_MGLOBAL['db']->fetch_first('SELECT url FROM ' . tname('attachments') . ' WHERE aid=' . $aid);
    if ($att) {
        @unlink(M_ROOT . $att['url']);//附件删除
        $_MGLOBAL['db']->deletetable('attachments', array('aid' => $aid));//删除附件表
        $message = '删除成功';
    }
    if (empty($_MGET['go'])) {
        exit($message);
    } else {
        sheader($refer);
    }
}
if (submitcheck('modifyimg')) {
    $att = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('attachments') . ' WHERE aid=' . intval($_POST['aid']));
    if (!empty($att)) {
        $setsqlarr = array('summary' => strfilter(trim(strip_tags($_POST['summary']))));
        $id = intval($_POST['articleid']);
        if (!empty($id)) {
            $setsqlarr['id'] = $id;
        }
        $tmpvar = explode('/', $att['url']);
        $attname = end($tmpvar);
        if ($attname != $_POST['attname']) {
            $theattdir = M_ROOT . $att['url'];
            $newattdir = str_replace($attname, $_POST['attname'], $theattdir);
            if (@rename($theattdir, $newattdir)) {
                $setsqlarr['url'] = str_replace(M_ROOT, '', $newattdir);//修改附件名
                $_MGLOBAL['db']->query('UPDATE ' . tname('article_content') . ' SET content= REPLACE(`content`,"' . $attname . '","' . $_POST['attname'] . '");');//修改文章里面的名字
            }
        }
        $_MGLOBAL['db']->updatetable('attachments', $setsqlarr, array('aid' => $att['aid']));
    }
}
if (submitcheck('opsubmit')) {
    //批量操作
    if (empty($_POST['imgarr'])) {
        showmessage(3, '请选择你要操作的文件!', $theurl);
    }
    if (empty($_POST['opimg'])) {
        showmessage(3, '请选择要操作的类型!', $theurl);
    }
    if ($_POST['opimg'] = 'delete') {
        foreach ($_POST['imgarr'] as $value) {
            $_MGLOBAL['db']->deletetable('attachments', array('aid' => intval($value)));
        }
        showmessage(1, '您已经成功附件!', $theurl);
    }
}
$count = $_MGLOBAL['db']->getcount('attachments', '');//附件总数
$chackatt = $_MGLOBAL['db']->getcount('attachments', array('id' => ''));//无效附件数
$perpage = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示TAG数和文章数一样
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $perpage;
$wheresqlstr = '';
$order = 'dateline DESC';
if (!empty($_MGET['dateline'])) {
    $order = '`dateline` ASC';
}
if (!empty($_MGET['size'])) {
    $order = '`size` DESC';
}
if (!empty($_MGET['chackatt'])) {
    $wheresqlstr = " where id=''";
    $perpage = 999;
}
if (submitcheck('searchkeyword')) {
    //搜索附件名
    $perpage = 999;
    $wheresqlstr = ' WHERE  `url` LIKE \'%' . strfilter($_POST['keyword']) . '%\'';
}
$urlarr = array('action' => 'attachments');//分页地址
$images = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('attachments') . $wheresqlstr . ' ORDER BY  ' . $order . ' LIMIT ' . $thispage . ',' . $perpage);
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    if ($value['id']) {
        $value['article'] = $_MGLOBAL['db']->fetch_first('SELECT subject,username FROM ' . tname('article') . ' WHERE id=' . $value['id']);
    }
    $tmpvar = explode('/', $value['url']);
    $value['attname'] = end($tmpvar);
    $value['size_k'] = intval($value['size'] / 1024);
    $images[] = $value;
}
if ($count > $perpage) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage = multipage($count, $perpage, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
} else {
    $multipage = '';
}
include_once template(TPLDIR . 'attachments.htm', 1);