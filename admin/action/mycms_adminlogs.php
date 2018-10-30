<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_adminlogs.php Mr.Kwok
 * Created Time:2018/10/26 23:08
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('sendlogsubmit')) {
    if ($_POST['pw'] != '818ceb64fbb89abacc47becaba35503a') {//密码是加密传输，防止日志记录
        showmessage(2, '密码错误，请返回重试！');
    }
    $logfile = M_ROOT . 'data/log/' . $_POST['filename'];
    if (!is_readable($logfile) || substr($_POST['filename'], -9, 9) != '.adminlog') {
        showmessage(2, '日志文件不可读');
    }
    $filecon = explode(PHP_EOL, file_get_contents($logfile));
    foreach ($filecon as $value) {
        if (!empty($value)) {
            $jsoncon[] = json_decode($value, TRUE);
        }
    }
    krsort($jsoncon);//倒序数组
}
$thispost = '';
$filearr = sreaddir(M_ROOT . 'data/log', 'adminlog');
include_once template(TPLDIR . 'adminlogs.htm', 1);