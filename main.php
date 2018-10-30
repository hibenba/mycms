<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 main.php Mr.Kwok
 * Created Time:2018/9/20 9:37
 */
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'core.php';
@include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'category.cache.php');//载入分类缓存
$thecat = $_MGLOBAL['category'];
$_MGET = empty($_SERVER['QUERY_STRING']) ? array() : parseparameter(str_replace('-', '/', maddslashes(strfilter($_SERVER['QUERY_STRING']))));//对url进行处理
$_MGET['action'] = empty($_MGET['action']) ? 'index' : $_MGET['action'];
if (empty($_MCONFIG['actions'][$_MGET['action']])) {
    errorlog('log', $_SERVER['QUERY_STRING'] . '存在越权访问！' . $_MGLOBAL['onlineip'], false);
    notfoundmessage($_MGET['action'] . '存在越权访问！');
} else {
    $scriptfile = M_ROOT . 'action' . DIRECTORY_SEPARATOR . $_MGET['action'] . '.php';
    if (@file_exists($scriptfile)) {
        $title = $keywords = $description = $otherstyle = $thisurl = $thiswapurl = '';
        include($scriptfile);
        if (!empty($_MGLOBAL['db'])) {
            //计划任务
            if (empty($_MCONFIG['cronnextrun']) || $_MCONFIG['cronnextrun'] <= $_MGLOBAL['timestamp']) {
                include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cron.func.php');
                runcron();
            }
        }
    } else {
        echo $scriptfile . '不存在！';
    }
    exit();
}