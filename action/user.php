<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 user.php Mr.Kwok
 * Created Time:2018/9/20 16:07
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//识别用户登陆是否成功


$_MGET['ac'] = empty($_MGET['ac']) ? '' : $_MGET['ac'];
switch ($_MGET['ac']) {
    case 'friends':
        $title = $keywords = $description = '我的朋友';
        $tpl = 'friends';
        break;
    case 'comments':
        $title = $keywords = $description = '我的评论';
        $tpl = 'comments';
        break;
    case 'myarticle':
        $title = $keywords = $description = '我的文章';
        $tpl = 'myarticle';
        break;
    default:
        $title = $keywords = $description = '我的资料';
        $tpl = 'index';
        break;
}
$title .= '_' . $_MCONFIG['sitename'];
include template('user_' . $tpl);