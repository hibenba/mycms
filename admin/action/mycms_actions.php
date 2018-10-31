<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_actions.php Mr.Kwok
 * Created Time:2018/10/31 10:58
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}


$catarr = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('actions'));
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $catarr[$value['id']] = $value;
}
$catjosn = json_encode($catarr);
include template(TPLDIR . 'actions.htm', 1);