<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 friendlinks.php Mr.Kwok
 * Created Time:2018/9/20 15:41
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
connectMysql();
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
if (submitcheck('firendsubmit')) {
    if (empty($_MCONFIG['noseccode'])) {
        session_start();
        if ($_MGLOBAL['timestamp'] - $_SESSION['seccodetime'] > 1200) {
            showmessage(2, '验证码已失效，请重新输入');
        }
        if ($_POST['seccode'] != $_SESSION['seccode']) {
            showmessage(2, '输入的验证码不符，请重新输入');
        }
    }
    $name = trim(strfilter(strip_tags($_POST['txt'])));
    if (strlen($name) < 2 || strlen($name) > 28) {
        showmessage(3, '网站名字长度不对，请大于2个汉字并不超过20个字节！');
    }
    if (preg_match('/(http|https):\/\/([\w\d\-_]+[\.\w\d\-_]+)/i', $_POST['url'])) {
        $url = trim(strip_tags($_POST['url']));
    } else {
        showmessage(3, '网站地址格式错误，需要以http://开头');
    }
    $description = trim(strfilter(strip_tags($_POST['description'])));
    $setsqlarr = array(
        'id' => 0,
        'displayorder' => -1,
        'name' => $name,
        'url' => $url,
        'description' => $description,
        'logo' => ''
    );
    $_MGLOBAL['db']->inserttable('friendlinks', $setsqlarr);
    showmessage(1, '我们已收到您的链接申请，站长将在24小时内完成审核！', MURL);
}
$query = $_MGLOBAL['db']->query('SELECT `name`,`url`,`description`,`logo` FROM ' . tname('friendlinks') . ' WHERE `displayorder` >=0 ORDER BY  `displayorder` DESC LIMIT 100');
while ($link = $_MGLOBAL['db']->fetch_array($query)) {
    $friendlinks[] = $link;
}
$keywords = $description = $title = '友情链接申请_' . $_MCONFIG['sitename'];
$formhash = formhash();
include template('friendlinks');
ob_out();