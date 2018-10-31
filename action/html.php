<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 html.php Mr.Kwok
 * Created Time:2018/9/20 15:43
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
$maketime = intval($_MGET['maketime']);
$id = empty($_MGET['id']) ? 0 : strfilter($_MGET['id']);
$needupdate = $update = false;
if (!empty($_MCONFIG['htmltime']) && $maketime < $_MCONFIG['htmltime'] && $_MCONFIG['htmltime'] < $_MGLOBAL['timestamp']) {
    $update = true;//后台强制更新
}
if (empty($_MGET['thisaction'])) {
    exit();
} else {
    $page = empty($_MGET['page']) ? 0 : intval($_MGET['page']);
    $url = '/main.php?action-' . $_MGET['thisaction'] . '-';
    switch ($_MGET['thisaction']) {
        case 'index':
            $url = '/main.php?php-1';
            break;
        case 'article':
            @include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'news.func.php');
            @include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');
            if (freshcookie($id)) updateviewnum($id);//点击数
            $url .= 'id-' . $id;
            break;
        case 'category':
            $url .= 'catid-' . $id;
            break;
        case 'tag':
            $url .= 'tagid-' . $id;
            break;
        default:
            $url .= 'name-' . $id;
            break;
    }
    $updatetime = empty($_MCONFIG['actions'][$_MGET['thisaction']]['cachetime']) ? 86400 : intval($_MCONFIG['actions'][$_MGET['thisaction']]['cachetime']);
    if (($_MGLOBAL['timestamp'] - $maketime) > $updatetime) {
        $needupdate = true;
    }
}
//JS Ajax请求更新
if ($needupdate || $update) {
    if ($_MGET['thisaction'] != 'index') {
        $url .= '-page-' . $page . '-php-1';
    }
    echo 'var a' . $id . ';a' . $id . '=new XMLHttpRequest();a' . $id . '.open("GET", "' . $url . '", true);a' . $id . '.send(null);';
}