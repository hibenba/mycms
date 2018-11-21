<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_company.php Mr.Kwok
 * Created Time:2018/10/31 9:39
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('companysubmit')) {
    $replacearr = array();
    unset($_POST['companysubmit']);
    $_POST = shtmlspecialchars($_POST);
    foreach ($_POST as $var => $value) {
        $replacearr[] = '(\'' . $var . '\', \'' . $value . '\')';
    }
    $_MGLOBAL['db']->query('REPLACE INTO ' . tname('settings') . ' (variable, value) VALUES ' . implode(',', $replacearr));
    include_once(SOUREC_DIR . 'function/cache.func.php');
    updatesettingcache();
    sheader($theurl);//回到原页面
}
$thevalue = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('settings'));
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $thevalue[$value['variable']] = $value['value'];
}
include template(TPLDIR . 'company.htm', 1);