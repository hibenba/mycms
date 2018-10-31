<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_usergroups.php Mr.Kwok
 * Created Time:2018/10/31 10:43
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $id = intval($_MGET['delete']);
    $group = $_MGLOBAL['db']->fetch_first('SELECT groupid,system FROM ' . tname('usergroups') . ' WHERE groupid=' . $id);
    if ($group['system'] == -1) {
        showmessage(2, '对不起，您不能删除系统内置用户组!');
    } else {
        $_MGLOBAL['db']->updatetable('members', array('groupid' => 5), array('groupid' => $id));//移动到受限组
        $_MGLOBAL['db']->deletetable('usergroups', array('groupid' => $id));//删除用户组
    }
    sheader($refer);//回到原页面
}
if (submitcheck('usergroupsubmit')) {
    $groupid = empty($_POST['groupid']) ? 0 : intval($_POST['groupid']);
    if (strlen($_POST['grouptitle']) < 4 || strlen($_POST['grouptitle']) > 20) {
        showmessage(3, '您输入的用户组名长度不符合要求，请返回修改!');
    } else {
        $grouptitle = strfilter($_POST['grouptitle']);
    }
    $systemtype = empty($_POST['system']) ? 0 : 1;
    $explower = is_numeric($_POST['explower']) ? intval($_POST['explower']) : 0;
    $allowpost = empty($_POST['allowpost']) ? 0 : 1;
    $allowcomment = empty($_POST['allowcomment']) ? 0 : 1;
    $allowpostattach = empty($_POST['allowpostattach']) ? 0 : 1;
    $allowvote = empty($_POST['allowvote']) ? 0 : 1;
    $setsqlarr = array(
        'groupid' => $groupid,
        'grouptitle' => $grouptitle,
        'system' => $systemtype,
        'explower' => $explower,
        'allowpost' => $allowpost,
        'allowcomment' => $allowcomment,
        'allowpostattach' => $allowpostattach,
        'allowvote' => $allowvote
    );
    if (!empty($groupid)) {
        $group = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('usergroups') . ' WHERE groupid=' . $groupid);
        if (empty($group)) {
            showmessage(2, '编辑的用户组不存在，请返回检查!');
        }
        if ($group['system'] == -1) {
            $setsqlarr['system'] = -1;//系统内置不可改
        }
        $_MGLOBAL['db']->updatetable('usergroups', $setsqlarr, array('groupid' => $groupid));
    } else {
        $_MGLOBAL['db']->inserttable('usergroups', $setsqlarr);
    }
    include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updategroupcache();
    sheader($theurl);//回到原页面
}
if (!empty($_MGET['edit'])) {
    if ($_MGET['edit'] == 'add') {
        $h2 = '<h2>增加一个用户组</h2>';
        $setsqlarr = array(
            'groupid' => 0,
            'grouptitle' => '',
            'system' => '',
            'explower' => '',
            'allowpost' => '',
            'allowcomment' => '',
            'allowpostattach' => '',
            'allowvote' => ''
        );
    } else {
        $setsqlarr = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('usergroups') . ' WHERE groupid=' . intval($_MGET['edit']));
        $h2 = '<h2>编辑用户组：' . $setsqlarr['grouptitle'] . '</h2>';
        if (empty($setsqlarr)) {
            showmessage(2, '用户组不存在，请返回检查!');
        }
    }
}
$groupsarr = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('usergroups'));
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $groupsarr [] = $value;
}
include template(TPLDIR . 'usergroups.htm', 1);