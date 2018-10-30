<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 update_article_viewnum.php Mr.Kwok
 * Created Time:2018/9/20 10:33
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
$logfile = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'viewcount.log';
if (@$viewlog = file($logfile)) {
    if (@$fp = fopen($logfile, 'w')) {
        fwrite($fp, '');
        fclose($fp);
        @chmod($logfile, 0777);
    } else {
        @unlink($logfile);
    }
    $itemidarray = $viewarray = array();
    foreach ($viewlog as $itemid) {
        $itemid = intval($itemid);
        if ($itemid) {
            if (empty($itemidarray[$itemid])) $itemidarray[$itemid] = 0;
            $itemidarray[$itemid]++;
        }
    }
    $comma = '';
    foreach ($itemidarray as $itemid => $views) {
        if (empty($viewarray[$views])) {
            $viewarray[$views] = '';
            $comma = '';
        }
        $viewarray[$views] .= $comma . $itemid;
        $comma = ',';
    }
    foreach ($viewarray as $views => $itemids) {
        $_MGLOBAL['db']->query('UPDATE ' . tname('article') . ' SET viewnum=viewnum+' . $views . ' WHERE id IN (' . $itemids . ')');
    }
}