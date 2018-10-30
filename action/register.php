<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 register.php Mr.Kwok
 * Created Time:2018/9/21 11:19
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
connectMysql();
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');
getcookie();
$title = $keywords = $description = '用户注册_' . $_MCONFIG['sitename'];
if ($_MCONFIG['allowregister'] == 0) {
    showmessage(2, '本站未开放注册，请联系管理员处理!');
}
if (!empty($_MGLOBAL['uid'])) {
    showmessage(1, '您已经成功登陆了，不需要注册！', MURL);
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
if (submitcheck('regsubmit')) {
    if (empty($_MCONFIG['noseccode'])) {
        session_start();
        if ($_MGLOBAL['timestamp'] - $_SESSION['seccodetime'] > 1200) {
            showmessage(2, '验证码已失效，请重新输入');
        }
        if ($_POST['seccode'] != $_SESSION['seccode']) {
            showmessage(2, '输入的验证码不符，请重新输入');
        }
    }
    $username = strtolower($_POST['username']);
    if (!is_username($username)) {
        showmessage(2, '您输入的用户名不符合要求，请返回修改!');
    }
    if (!$_POST['password'] || $_POST['password'] != $_POST['password']) {
        showmessage(2, '您输入的密码不符合要求，请返回修改!');
    }
    if ($_POST['password'] != $_POST['confirm_password']) {
        showmessage(2, '2次输入的密码不一致！');
    }
    $getuser = $_MGLOBAL['db']->fetch_first("SELECT username,dateline FROM " . tname('members') . " WHERE username='$username'");
    if ($getuser['username'] == $username) {
        showmessage(2, '用户已存在，请重新输入');
    } else {
        $pword = getpw($_POST['password'], $username);
    }
    $email = is_Email($_POST['email']);
    if ($email == false) {
        showmessage(2, '输入的Email地址不合法，请返回修改!');
    }
    $setsqlarr = array(
        'uid' => 0,
        'groupid' => 11,
        'username' => $username,
        'password' => $pword,
        'email' => $email,
        'experience' => 0,
        'dateline' => $_MGLOBAL['timestamp'],
        'updatetime' => $_MGLOBAL['timestamp'],
        'lastlogin' => $_MGLOBAL['timestamp'],
        'regip' => $_MGLOBAL['onlineip'],
        'lastloginip' => $_MGLOBAL['onlineip'],
        'lastcommenttime' => 0,
        'lastposttime' => 0,
        'avatar' => 0
    );
    $uid = $_MGLOBAL['db']->inserttable('members', $setsqlarr, 1);
    $cookievalue = authcode("$pword\t" . $uid, 'ENCODE');
    ssetcookie('auth', $cookievalue, 0);
    sheader(MURL);
}
$formhash = formhash();
include template('site_register');
ob_out();