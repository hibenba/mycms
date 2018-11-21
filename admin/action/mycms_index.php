<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_index.php Mr.Kwok
 * Created Time:2018/9/26 9:40
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
//统计
$statistics = getstatistics();
$os = PHP_OS . ' / PHP v' . $statistics['php'] . (@ini_get('safe_mode') ? ' Safe Mode' : NULL);
if (@ini_get('file_uploads')) {
    $fileupload = ini_get('upload_max_filesize');
} else {
    $fileupload = '<strong style="color:red">禁止上传</strong>';
}
$dbsize = $statistics['dbsize'] ? formatsize($statistics['dbsize']) : 'unknown';
$makedate = sgmdate($_MCONFIG['makedate'], '', 1);
$runday = intval((time() - $_MCONFIG['makedate']) / 86400);
if (isset($_GET['attachsize'])) {
    $attachsize = $_MGLOBAL['db']->result($_MGLOBAL['db']->query("SELECT SUM(size) FROM " . tname('attachments')), 0);
    $attachsize = is_numeric($attachsize) ? formatsize($attachsize) : 0;
} else {
    $attachsize = '<a href="' . CPURL . '?attachsize">------</a>';
}
$message = '';
if ($statistics['friendlinkverify'] > 0) {
    $message .= '<dd><a href="' . CPURL . '?action-friendlinks">有' . $statistics['friendlinkverify'] . '条友情链接申请需要审核</a></dd>';
}
if ($statistics['commentsnumverify'] > 0) {
    $message .= '<dd><a href="' . CPURL . '?action-comments">有' . $statistics['commentsnumverify'] . '条网友评论需要审核</a></dd>';
}
include template(TPLDIR . 'index.htm', 1);
//统计数据
function getstatistics()
{
    global $_MGLOBAL, $_MC, $_MCONFIG;
    $dbsize = 0;
    $query = $_MGLOBAL['db']->query("SHOW TABLE STATUS");
    while ($table = $_MGLOBAL['db']->fetch_array($query)) {
        $dbsize += $table['Data_length'] + $table['Index_length'];
    }
    $sitekey = trim($_MCONFIG['sitekey']);
    if (empty($sitekey)) {
        $sitekey = mksitekey();
        $_MGLOBAL['db']->query("REPLACE INTO " . tname('settings') . " (variable, value) VALUES ('sitekey', '$sitekey')");
        include_once(SOUREC_DIR . 'function/cache.func.php');
        updatesettingcache();
    }
    $statistics = array(
        'sitekey' => $sitekey,
        'version' => M_VER,
        'release' => M_RELEASE,
        'php' => PHP_VERSION,
        'mysql' => $_MGLOBAL['db']->result($_MGLOBAL['db']->query("SELECT VERSION()"), 0),
        'dbsize' => $dbsize,
        'articlenum' => $_MGLOBAL['db']->getcount('article', array()),
        'topicnum' => $_MGLOBAL['db']->getcount('topic', array()),
        'commentsnum' => $_MGLOBAL['db']->getcount('comments', array()),
        'commentsnumverify' => $_MGLOBAL['db']->getcount('comments', array('status' => 1)),
        'categorynum' => $_MGLOBAL['db']->getcount('categories', array()),
        'friendlinknum' => $_MGLOBAL['db']->getcount('friendlinks', array()),
        'friendlinkverify' => $_MGLOBAL['db']->getcount('friendlinks', array('displayorder' => -1)),
        'membernum' => $_MGLOBAL['db']->getcount('members', array()),
        'attachmentnum' => $_MGLOBAL['db']->getcount('attachments', array()),
        'usergroupnum' => $_MGLOBAL['db']->getcount('usergroups', array()),
        'tagsnum' => $_MGLOBAL['db']->getcount('tags', array())

    );
    return $statistics;
}