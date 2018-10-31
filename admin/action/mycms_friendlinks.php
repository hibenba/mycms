<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_friendlinks.php Mr.Kwok
 * Created Time:2018/10/31 9:52
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $_MGLOBAL['db']->deletetable('friendlinks', array('id' => intval($_MGET['delete'])));
    sheader($theurl);//回到原页面
}
if (!empty($_MGET['status'])) {
    //审核通过
    $fid = intval($_MGET['status']);
    $_MGLOBAL['db']->updatetable('friendlinks', array('displayorder' => $fid + 1), array('id' => $fid));
    sheader($theurl);//回到原页面
}
if (submitcheck('batchsubmit')) {
    //批量操作
    if (!empty($_POST['displayorder'])) {
        //修改排序
        foreach ($_POST['displayorder'] as $item => $value) {
            $_MGLOBAL['db']->updatetable('friendlinks', array('displayorder' => $value), array('id' => intval($item)));
        }
    }
    if (!empty($_POST['linkarr'])) {
        //批量删除
        foreach ($_POST['linkarr'] as $value) {
            $_MGLOBAL['db']->deletetable('friendlinks', array('id' => intval($value)));//删除tag关联表
        }
    }
    sheader($theurl);//回到原页面
}
if (submitcheck('sublink')) {
    $name = trim(strfilter(strip_tags($_POST['name'])));
    if (strlen($_POST['name']) < 2 || strlen($_POST['name']) > 50) {
        showmessage(3, '分类的字数太少了，请大于2个汉字，不超过15个字');
    }
    if (empty($_POST['url']) || strrpos($_POST['url'], '://') === false) {
        showmessage(3, '网站地址不能为空，并且需要以http://开头');
    } else {
        $url = shtmlspecialchars($_POST['url']);
    }
    $id = empty($_POST['id']) ? '' : intval($_POST['id']);
    $displayorder = empty($_POST['displayorder']) ? 0 : intval($_POST['displayorder']);
    $thevalue = array('id' => $id, 'displayorder' => $displayorder, 'name' => $name, 'url' => $url, 'description' => filters_outcontent(trim(strip_tags($_POST['note']))), 'logo' => shtmlspecialchars($_POST['logo']));
    if ($id) {
        //编辑
        $_MGLOBAL['db']->updatetable('friendlinks', $thevalue, array('id' => $id));
    } else {
        //新增
        $_MGLOBAL['db']->inserttable('friendlinks', $thevalue);
    }
    sheader($theurl);//回到原页面
}
$friendlins = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('friendlinks') . ' ORDER BY displayorder ASC LIMIT 999');
while ($thelink = $_MGLOBAL['db']->fetch_array($query)) {
    $friendlins[$thelink['id']] = $thelink;
}
$linksjosn = json_encode($friendlins);
include_once template(TPLDIR . 'friendlinks.htm', 1);