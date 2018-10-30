<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_cache.php Mr.Kwok
 * Created Time:2018/10/30 13:46
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('cachesubmit') || !empty($_MGET['update'])) {
    $_POST['cache'] = empty($_POST['cache']) ? array() : $_POST['cache'];
    $_MGET['update'] = empty($_MGET['update']) ? '' : $_MGET['update'];
    if (in_array('tpl', $_POST['cache']) || $_MGET['update'] == 'tpl') {
        //模板缓存
        removeDir(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'tpl');
    }
    if (in_array('blocks', $_POST['cache']) || $_MGET['update'] == 'blocks') {
        //调用数据模板缓存
        removeDir(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'block');
    }
    include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    if (in_array('categorys', $_POST['cache']) || $_MGET['update'] == 'categorys') {
        //分类缓存
        updatecategorycache();
    }
    if (in_array('settings', $_POST['cache']) || $_MGET['update'] == 'settings') {
        //系统设置
        updatesettingcache();
    }
    if (in_array('crons', $_POST['cache']) || $_MGET['update'] == 'crons') {
        //计划任务
        updatecronscache();
        updatecroncache();
    }
    if (in_array('group', $_POST['cache']) || $_MGET['update'] == 'group') {
        //用户组
        updategroupcache();
    }
    if (in_array('censor', $_POST['cache']) || $_MGET['update'] == 'censor') {
        //词语屏蔽
        updatecensorcache();
    }
    if (in_array('action', $_POST['cache']) || $_MGET['update'] == 'action') {
        //词语屏蔽
        updateadmin_action();
    }
    showmessage(1, '缓存已经成功清理并更新！', $theurl);
}
$cachearr = array(
    array('ac' => 'group', 'name' => '用户组缓存', 'dir' => './data/system/group.cache.php'),
    array('ac' => 'categorys', 'name' => '分类菜单缓存', 'dir' => './data/system/category.cache.php'),
    array('ac' => 'settings', 'name' => '系统设置缓存', 'dir' => './data/system/config.cache.php'),
    array('ac' => 'crons', 'name' => '计划任务缓存', 'dir' => './data/system/crons.cache.php'),
    array('ac' => 'tpl', 'name' => '模板缓存', 'dir' => './data/cache/tpl/'),
    array('ac' => 'censor', 'name' => '词语屏蔽', 'dir' => './data/system/censor.cache.php'),
    array('ac' => 'action', 'name' => '系统菜单', 'dir' => './data/system/admin_action.cache.php'),
);
foreach ($cachearr as $item => $value) {
    if (@file_exists(M_ROOT . $value['dir'])) {
        $cachearr[$item]['available'] = '<strong>正常</strong>';
        $cachearr[$item]['update'] = sgmdate(@filemtime(M_ROOT . $value['dir']));
    } else {
        $cachearr[$item]['available'] = '<em>未缓存</em>';
        $cachearr[$item]['update'] = '-';
    }
}
include_once template(TPLDIR . 'cache.htm', 1);