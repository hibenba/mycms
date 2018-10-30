<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 login.php Mr.Kwok
 * Created Time:2018/9/21 11:28
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
@header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
@header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
@header("Pragma: no-cache");
@header("Expires:-1");
connectMysql();
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');
getcookie();
if (isset($_MGET['logout'])) {
    obclean();
    sclearcookie();
    showmessage(1, '您已经成功注销！', MURL);
}
if (!empty($_MGLOBAL['uid'])) {
    showmessage(1, '您已经成功登陆了，不需要再次操作！', MURL);
}
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
if (submitcheck('loginsubmit')) {
    $message = '';
    $loginok = false;
    if (empty($_MCONFIG['noseccode'])) {
        session_start();
        if ($_MGLOBAL['timestamp'] - $_SESSION['seccodetime'] > 1200) {
            showmessage(2, '验证码已失效，请重新输入');
        }
        if ($_POST['seccode'] != $_SESSION['seccode']) {
            showmessage(2, '输入的验证码不符，请重新输入');
        }
    }
    $cookietime = empty($_POST['cookietime']) ? 0 : intval($_POST['cookietime']);
    $username = strfilter(strtolower($_POST['username']));
    $getuser = $_MGLOBAL['db']->fetch_first("SELECT username,dateline FROM " . tname('members') . " WHERE username='$username'");
    if ($getuser['username'] == $username) {//如果用户存在就对比密码
        $password = getpw($_POST['password'], $getuser['username']);
        $members = $_MGLOBAL['db']->fetch_first("SELECT `uid`, `groupid`, `username`, `password`, `email`, `experience`, `dateline`, `updatetime`, `lastlogin`, `regip`, `lastloginip`, `lastcommenttime`, `lastposttime`, `avatar` FROM " . tname('members') . " WHERE username='$username' and password='$password'");
    } else {
        $message = '用户名不存在，请检查您的输入重试!';
    }
    if ($members['uid'] <= 0) {
        $message = '输入的密码有误，请重新输入';
    } else {
        $loginok = true;
    }
    if ($loginok) {
        //登录成功
        $_MGLOBAL['db']->query("UPDATE " . tname('members') . " SET `experience`= experience+1 ,`lastlogin` = '" . $_MGLOBAL['timestamp'] . "', `lastloginip` = '" . $_MGLOBAL['onlineip'] . "' WHERE `uid` = " . $members['uid']);//更新登陆信息
        $_MGLOBAL['db']->inserttable('login_logs', array('id' => 0, 'dateline' => $_MGLOBAL['timestamp'], 'ip' => $_MGLOBAL['onlineip'], 'state' => 1, 'uid' => $members['uid'], 'username' => $members['username'], 'password' => '-'));//登陆日志
        $cookievalue = authcode("$password\t" . $members['uid'], 'ENCODE');
        ssetcookie('auth', $cookievalue, $cookietime);
        ssetcookie('user', $username, $cookietime);
        $reurl = empty($_POST['referer']) ? MURL : $_POST['referer'];
        sheader($reurl);
    } else {
        $_MGLOBAL['db']->inserttable('login_logs', array('id' => 0, 'dateline' => $_MGLOBAL['timestamp'], 'ip' => $_MGLOBAL['onlineip'], 'state' => 0, 'uid' => '', 'username' => $username, 'password' => $_POST['password']));//登陆日志
        showmessage(2, $message, geturl('action/login'));
    }
}
$title = $keywords = $description = '用户登陆_' . $_MCONFIG['sitename'];
$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
$formhash = formhash();
include template('site_login');
ob_out();