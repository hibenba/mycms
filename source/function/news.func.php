<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 news.func.php Mr.Kwok
 * Created Time:2018/9/20 13:46
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
function freshcookie($itemid)
{
    global $_MCONFIG;
    $isupdate = true;
    $old = empty($_COOKIE[$_MCONFIG['cookiepre'] . 'refresh_article_id']) ? 0 : trim($_COOKIE[$_MCONFIG['cookiepre'] . 'refresh_article_id']);
    $itemidarr = explode('_', $old);
    if (in_array($itemid, $itemidarr)) {
        $isupdate = false;
    } else {
        $itemidarr[] = trim($itemid);
        ssetcookie('refresh_article_id', implode('_', $itemidarr));
    }
    if (empty($_COOKIE)) $isupdate = false;
    return $isupdate;
}

function updateviewnum($itemid)
{
    global $_MGLOBAL;
    $logfile = M_ROOT . 'data/log/viewcount.log';//写入日志里后计划任务每日定时更新
    if (@$fp = fopen($logfile, 'a+')) {
        fwrite($fp, $itemid . PHP_EOL);
        fclose($fp);
        @chmod($logfile, 0777);
    } else {
        $_MGLOBAL['db']->query('UPDATE ' . tname('article') . ' SET `viewnum`=`viewnum`+1 WHERE id=' . $itemid);
    }
}