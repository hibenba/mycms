<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_words.php Mr.Kwok
 * Created Time:2018/10/26 18:36
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('addcensorsubmit')) {
    $_MGLOBAL['db']->query('TRUNCATE TABLE ' . tname('bad_words'));
    $censorarr = explode(PHP_EOL, $_POST['addcensors']);
    $censorarr = array_unique($censorarr);//去重
    foreach ($censorarr as $value) {
        list($newfind, $newreplacement) = array_map('trim', explode('=', $value));
        $newfind = strfilter($newfind);
        $newreplacement = trim(strfilter($newreplacement));
        $newreplacement = empty($newreplacement) ? '**' : $newreplacement;
        if (strlen($newfind) > 2) {
            $setsqlarr = '';
            $setsqlarr = array(
                'id' => 0,
                'find' => $newfind,
                'replace' => $newreplacement
            );
            $_MGLOBAL['db']->inserttable('bad_words', $setsqlarr);
        }
    }
    include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updatecensorcache();
    showmessage(1, '修改敏感词成功', $theurl);
}
$wordsarr = '';
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('bad_words'));
while ($word = $_MGLOBAL['db']->fetch_array($query)) {
    $word['replace'] = empty($word['replace']) ? '**' : $word['replace'];
    $wordsarr .= $word['find'] . '=' . $word['replace'] . PHP_EOL;
}
include_once template(TPLDIR . 'words.htm', 1);