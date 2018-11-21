<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 index.php Mr.Kwok
 * Created Time:2018/9/26 8:56
 */
$mtime = explode(' ', microtime());//执行时间计算
@header("Content-Type:text/html;charset=utf-8");
@header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
@header("Pragma: no-cache");
@header("Expires:-1");
define('ADMIN', dirname(__FILE__));//后台目录
define('ADMIN_SOUREC_DIR', ADMIN . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR);//定义后台功能源码目录
include_once(dirname(ADMIN) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'core.php');
$_MGLOBAL['mycms_starttime'] = $mtime[1] + $mtime[0];
define('ADMIN_DIR', str_replace(M_ROOT, '', ADMIN));//从配置时拿到后台管理地址
define('CPURL', '/' . ADMIN_DIR . '/index.php');//后台主要调用的文件地址
define('TPLDIR', DIRECTORY_SEPARATOR . ADMIN_DIR . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR);//模板文件目录
connectMysql();//连接数据库
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'list.func.php');//列表页处理函数
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');//COOKIES处理
getcookie();
$parsegetvar = empty($_SERVER['QUERY_STRING']) ? '' : maddslashes(strfilter($_SERVER['QUERY_STRING']));
if (!empty($parsegetvar)) {
    $_MGET = parseparameter(str_replace('-', '/', $parsegetvar));
}
$action = empty($_MGET['action']) ? 'index' : strfilter($_MGET['action']);
$theurl = CPURL . '?action-' . $action;
$refer = empty($_SERVER['HTTP_REFERER']) ? $theurl : $_SERVER['HTTP_REFERER'];
if ($action == 'logout') {
    obclean();
    sclearcookie();
    $_MGLOBAL['db']->deletetable('adminsession', array('uid' => $_MGLOBAL['uid']));
    showmessage(1, '您已经成功退出管理系统！', MURL);
}
@include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'category.cache.php');
$thisarticle = $thisnewarticle = $thistag = $thiscomments = $thiscategory = $thisnewcategory = $thismember = $thisusergroups = '';
$debug = '没有操作权限';
if (submitcheck('adminlogin')) {
    if (empty($_POST['admin_username'])) {
        $thisarticle = '<p id="message"><strong>发生错误：</strong> 用户名不能为空！</p>';
        goto login;
    }
    if (empty($_POST['admin_password'])) {
        $thisarticle = '<p id="message"><strong>发生错误：</strong> 密码不能为空！</p>';
        goto login;
    }
    $session = $_MGLOBAL['db']->fetch_first("SELECT `uid`,`ip`,`dateline`,`errorcount` FROM " . tname('adminsession') . " WHERE ip='$_MGLOBAL[onlineip]'");
    if (empty($session)) {
        $_MGLOBAL['db']->inserttable('adminsession', array('uid' => $_MGLOBAL['uid'], 'ip' => $_MGLOBAL['onlineip'], 'dateline' => $_MGLOBAL['timestamp'], 'errorcount' => '0'));
    } else {
        if (($_MGLOBAL['timestamp'] - $session['dateline']) > 1800) {
            $_MGLOBAL['db']->updatetable('adminsession', array('errorcount' => 0), array('ip' => $_MGLOBAL['onlineip']));
            $session['errorcount'] = 0;
        }
        if ($session['errorcount'] > 3) {
            $thisarticle = '<p id="message" style="background:red;color:yellow"><strong>警告信息：</strong> 您30分钟内尝试登录管理平台的次数超过了3次，为了网站的安全，请稍候再试!</p>';
            goto login;
        }
    }
    $username = strfilter($_POST['admin_username']);
    $getuser = $_MGLOBAL['db']->fetch_first("SELECT username,dateline FROM " . tname('members') . " WHERE username='$username'");
    if ($getuser['username'] == $username) {//如果用户存在就对比密码
        $password = getpw($_POST['admin_password'], $getuser['username']);
        $members = $_MGLOBAL['db']->fetch_first("SELECT `uid`, `groupid`, `username`, `password`, `email`, `experience`, `dateline`, `updatetime`, `lastlogin`, `regip`, `lastloginip`, `lastcommenttime`, `lastposttime`, `avatar` FROM " . tname('members') . " WHERE username='$username' and password='$password'");
    } else {
        $_MGLOBAL['db']->query("UPDATE " . tname('adminsession') . " SET errorcount=errorcount+1,dateline=$_MGLOBAL[timestamp] WHERE ip='$_MGLOBAL[onlineip]'");
        $thisarticle = '<p id="message"><strong>发生错误：</strong> 您输入的用户不存在！</p>';
        goto login;
    }
    if (empty($members['uid'])) {
        $_MGLOBAL['db']->query("UPDATE " . tname('adminsession') . " SET errorcount=errorcount+1,dateline=" . $_MGLOBAL['timestamp'] . " WHERE ip='" . $_MGLOBAL['onlineip'] . "'");
        $thisarticle = '<p id="message"><strong>发生错误：</strong> 您输入的密码不正确！</p>';
        goto login;
    } elseif ($members['groupid'] == 1) {
        //登录成功
        $uid = $_MGLOBAL['uid'] = $members['uid'];
        $_MGLOBAL['member'] = $members;
        $_MGLOBAL['db']->query("UPDATE " . tname('members') . " SET `experience`= experience+1 ,`lastlogin` = '" . $_MGLOBAL['timestamp'] . "', `lastloginip` = '" . $_MGLOBAL['onlineip'] . "' WHERE `uid` = " . $uid);//更新登陆信息
        $_MGLOBAL['db']->deletetable('adminsession', array('ip' => $_MGLOBAL['onlineip']));//删除同IP下的用户session
        $_MGLOBAL['db']->inserttable('adminsession', array('uid' => $_MGLOBAL['uid'], 'ip' => $_MGLOBAL['onlineip'], 'dateline' => $_MGLOBAL['timestamp'], 'errorcount' => '-1'));
        $cookievalue = authcode("$password\t$uid", 'ENCODE');
        ssetcookie('auth', $cookievalue, 0);
        ssetcookie('user', $username, 0);
        showmessage(1, '恭喜你，已成功登陆系统!', $refer, 0);
    } else {
        $thisarticle = '<p id="message"><strong>发生错误：</strong> 您不是管理员，请更换帐号重试！</p>';
        goto login;
    }
}
$formhash = formhash();
//没有登录
if (empty($_MGLOBAL['uid']) || empty($_MGLOBAL['member']['password'])) {
    login:
    include_once template(TPLDIR . 'login.htm', 1);
    exit();
}
include_once(ADMIN_SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'admin.func.php');
$session = $_MGLOBAL['db']->fetch_first("SELECT `uid`,`ip`,`dateline`,`errorcount` FROM " . tname('adminsession') . " WHERE uid=" . $_MGLOBAL['uid']);
//检查是否是创始人或者2小时未操作将强制退出
if (($_MGLOBAL['timestamp'] - $session['dateline']) < 7200 && ckfounder($_MGLOBAL['uid']) != false) {
    $debug = 'Founder:(' . $_MCONFIG['founder'] . '),Time:' . ($_MGLOBAL['timestamp'] - $session['dateline']) . 's';
    $_MGLOBAL['db']->updatetable('adminsession', array('dateline' => $_MGLOBAL['timestamp']), array('uid' => $_MGLOBAL['uid']));
} else {
    obclean();
    sclearcookie();
    $_MGLOBAL['db']->deletetable('adminsession', array('uid' => $_MGLOBAL['uid']));//删除用户session
    showmessage(2, '管理权限验证失败，请重新登陆!', CPURL, 3);
}
//后台访问记录log
$adminlog = array(
    'time' => $_MGLOBAL['timestamp'],
    'user' => $_MGLOBAL['username'],
    'uid' => $_MGLOBAL['uid'],
    'ip' => $_MGLOBAL['onlineip'],
    'url' => $_SERVER['QUERY_STRING'],
    'refer' => $refer,
    'info' => $_SERVER['HTTP_USER_AGENT'],
    'debug' => $debug
);
if ($_POST) $adminlog['post'] = serialize($_POST);
@$fp = fopen(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . sgmdate($_MGLOBAL['timestamp'], "Ymd") . '.adminlog', 'a');
@flock($fp, 2);
@fwrite($fp, json_encode($adminlog) . PHP_EOL);
@fclose($fp);
unset($adminlog);//记录log结束
if (@!include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'admin_action.cache.php')) updateadmin_action();//载入后台动作
$_MGLOBAL['admin_action']['upload'] = Array('id' => 99, 'upid' => 1, 'action' => 'upload', 'name' => '文件上传', 'description' => '手工增加的后台动作，用于处理上传安全操作过滤', 'displayorder' => -1, 'type' => 1);
if (!array_key_exists($action, $_MGLOBAL['admin_action'])) showmessage(2, '您请求的地址不在允许范围内，请检查后台设置或者反馈给网站技术人员!');
$title = $_MGLOBAL['admin_action'][$action]['name'] . ' - ' . $_MCONFIG['sitename'] . '后台管理系统';
include(ADMIN . DIRECTORY_SEPARATOR . 'action' . DIRECTORY_SEPARATOR . 'mycms_' . $action . '.php');