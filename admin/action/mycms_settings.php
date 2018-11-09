<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_settings.php Mr.Kwok
 * Created Time:2018/10/31 10:11
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('settingsubmit')) {
    $replacearr = array();
    unset($_POST['settingsubmit']);
    $_POST = shtmlspecialchars($_POST);
    foreach ($_POST as $var => $value) {
        if ($var == 'checkgrade') {
            $value = implode("\t", $value);
        }
        $replacearr[] = '(\'' . $var . '\', \'' . $value . '\')';
    }
    $replacearr[] = '(\'formhash\', \'' . formhash(1) . '\')';
    $_MGLOBAL['db']->query('REPLACE INTO ' . tname('settings') . ' (variable, value) VALUES ' . implode(',', $replacearr));
    //更新设置cache
    include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updatesettingcache();
    sheader($theurl);//回到原页面
}
$thevalue = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('settings'));
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $thevalue[$value['variable']] = $value['value'];
}
$templatearr = sreaddir(M_ROOT . 'data/templates');
$attachmentdirtypearr = array(
    'all' => '不归类',
    'year' => '按年归类',
    'month' => '按月归类',
    'day' => '按天归类',
    'md5' => '随机归类'
);
$htmltimearr = array(
    '' => '------',
    '300' => '5分钟',
    '600' => '10分钟',
    '900' => '15分钟',
    '1200' => '20分钟',
    '1500' => '25分钟',
    '1800' => '30分钟',
    '3600' => '1小时',
    '7200' => '2小时',
    '10800' => '3小时',
    '14400' => '4小时',
    '18000' => '5小时',
    '21600' => '6小时',
    '43200' => '12小时',
    '86400' => '1天',
    '172800' => '2天',
    '259200' => '3天',
    '604800' => '1周',
    '1209600' => '2周',
    '1814400' => '3周',
    '2592000' => '1个月',
    '15520000' => '6个月',
    '31536000' => '1年'
);
$checkgrade = empty($thevalue['checkgrade']) ? array('', '', '', '', '') : explode("\t", $thevalue['checkgrade']);
$timeoffsetarr = array(
    '-12' => '(GMT -12:00) Eniwetok, Kwajalein',
    '-11' => '(GMT -11:00) Midway Island, Samoa',
    '-10' => '(GMT -10:00) Hawaii',
    '-9' => '(GMT -09:00) Alaska',
    '-8' => '(GMT -08:00) Pacific Time (US & Canada), Tijuana',
    '-7' => '(GMT -07:00) Mountain Time (US & Canada), Arizona',
    '-6' => '(GMT -06:00) Central Time (US & Canada), Mexico City',
    '-5' => '(GMT -05:00) Eastern Time (US & Canada), Bogota, Lima, Quito',
    '-4' => '(GMT -04:00) Atlantic Time (Canada), Caracas, La Paz',
    '-3.5' => '(GMT -03:30) Newfoundland',
    '-3' => '(GMT -03:00) Brassila, Buenos Aires, Georgetown, Falkland Is',
    '-2' => '(GMT -02:00) Mid-Atlantic, Ascension Is., St. Helena',
    '-1' => '(GMT -01:00) Azores, Cape Verde Islands',
    '0' => '(GMT) Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia',
    '1' => '(GMT +01:00) Amsterdam, Berlin, Brussels, Madrid, Paris, Rome',
    '2' => '(GMT +02:00) Cairo, Helsinki, Kaliningrad, South Africa',
    '3' => '(GMT +03:00) Baghdad, Riyadh, Moscow, Nairobi',
    '3.5' => '(GMT +03:30) Tehran',
    '4' => '(GMT +04:00) Abu Dhabi, Baku, Muscat, Tbilisi',
    '4.5' => '(GMT +04:30) Kabul',
    '5' => '(GMT +05:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
    '5.5' => '(GMT +05:30) Bombay, Calcutta, Madras, New Delhi',
    '5.75' => '(GMT +05:45) Katmandu',
    '6' => '(GMT +06:00) Almaty, Colombo, Dhaka, Novosibirsk',
    '6.5' => '(GMT +06:30) Rangoon',
    '7' => '(GMT +07:00) Bangkok, Hanoi, Jakarta',
    '8' => '(GMT +08:00) 中国、北京、重庆、上海、香港时区',
    '9' => '(GMT +09:00) Osaka, Sapporo, Seoul, Tokyo, Yakutsk',
    '9.5' => '(GMT +09:30) Adelaide, Darwin',
    '10' => '(GMT +10:00) Canberra, Guam, Melbourne, Sydney, Vladivostok',
    '11' => '(GMT +11:00) Magadan, New Caledonia, Solomon Islands',
    '12' => '(GMT +12:00) Auckland, Wellington, Fiji, Marshall Island'
);
include template(TPLDIR . 'settings.htm', 1);