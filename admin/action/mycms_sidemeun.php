<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_sidemeun.php Mr.Kwok
 * Created Time:2018/10/31 10:38
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('batchsubmit')) {
    //批量修改
    if (is_array($_POST['displayorder'])) {
        foreach ($_POST['displayorder'] as $postcatid => $postdisplayorder) {
            $postdisplayorder = empty($postdisplayorder) ? 0 : intval($postdisplayorder);
            $_MGLOBAL['db']->updatetable('admin_action', array('displayorder' => $postdisplayorder), array('id' => intval($postcatid)));
        }
    }
    updateadmin_action();
    sheader($theurl);//回到原页面
}
if (submitcheck('subaction')) {
    $id = empty($_POST['id']) ? 0 : intval($_POST['id']);
    $upid = $_POST['upid'] ? intval($_POST['upid']) : 0;
    $_POST['name'] = trim(strfilter(strip_tags($_POST['name'])));
    if (strlen($_POST['name']) < 2 || strlen($_POST['name']) > 50) {
        showmessage(3, '名称的字数太少了，请大于2个汉字，不超过15个字');
    }
    $setsqlarr = array(
        'id' => $id,
        'upid' => $upid,
        'action' => trim(strfilter($_POST['action'])),
        'name' => $_POST['name'],
        'description' => filters_outcontent(trim(strip_tags($_POST['description']))),
        'displayorder' => intval($_POST['displayorder'])
    );
    if (!empty($id)) {
        $_MGLOBAL['db']->updatetable('admin_action', $setsqlarr, array('id' => $id));//修改
    } else {
        $setsqlarr['type'] = 0;
        $_MGLOBAL['db']->inserttable('admin_action', $setsqlarr);//新增
    }
    updateadmin_action();
    sheader($theurl);//回到原页面
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $type = $_MGLOBAL['db']->fetch_first('SELECT type FROM ' . tname('admin_action') . " WHERE upid=" . intval($_MGET['delete']));
    if ($type['type'] == 1) {
        showmessage(3, '系统内置动作，禁止删除！');
    } else {
        //删除分类
        $_MGLOBAL['db']->deletetable('admin_action', array('id' => intval($_MGET['delete'])));
        updateadmin_action();
        sheader($theurl);//回到原页面
    }
}
$catarr = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('admin_action') . ' where displayorder >= 0 ORDER BY displayorder ASC');
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $catarr[$value['id']] = $value;
}
$catjosn = json_encode($catarr);
include template(TPLDIR . 'sidemeun.htm', 1);