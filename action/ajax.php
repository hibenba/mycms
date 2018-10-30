<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 ajax.php Mr.Kwok
 * Created Time:2018/9/20 15:35
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
@header("Content-Type:text/html;charset=utf-8");
@header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
@header("Pragma: no-cache");
@header("Expires:-1");
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
connectMysql();
if (submitcheck('chickusername')) {
    //用户名检测
    $username = strfilter(strtolower($_POST['username']));
    if ($username) {
        $getuser = $_MGLOBAL['db']->fetch_first("SELECT `username` FROM " . tname('members') . " WHERE `username`='$username'");
        if ($getuser) {
            echo '<i class="error">用户名已存在，请重新输入！</i>';
        } else {
            echo '<i class="ok">您可以使用这个用户名！</i>';
        }
    }
    exit;
}
if (submitcheck('mailchick')) {
    //email检测
    $email = strtolower($_POST['mail']);
    if ($email) {
        $getmail = $_MGLOBAL['db']->fetch_first("SELECT `email` FROM " . tname('members') . " WHERE `email`='$email'");
        if ($getmail) {
            echo '<i class="error">E-mail 地址已存在，请重新输入！</i>';
        } else {
            echo '<i class="ok">E-mail 格式输入正确！</i>';
        }
    }
    exit;
}
if (submitcheck('seccodechick')) {
    //验证码检测
    session_start();
    if ($_POST['seccode'] != $_SESSION['seccode']) {
        echo '<i class="error"></i>';
    } else {
        echo '<i class="ok"></i>';
    }
    exit;
}
function chickaction($id, $name)
{
    global $_MGLOBAL;
    $getaction = $_MGLOBAL['db']->fetch_first('SELECT `acid`,`action` FROM  ' . tname('action_logs') . ' where `name`=\'' . $name . '\' and `acid`=' . $id . ' ORDER BY `id` DESC LIMIT 1');//先拿到最新的动态
    if ($getaction) {
        $action = json_decode($getaction['action'], true);
        if ($action['ip'] == $_MGLOBAL['onlineip'] && $_MGLOBAL['timestamp'] - $action['dateline'] < 86400 && $getaction['acid'] == $id) {
            return false;//同IP在24小时内对同文章点赞无效
        }
    }
    return true;
}

if (!empty($_MGET['good'])) {
    //用户对文章点赞
    $id = intval($_MGET['good']);
    if (chickaction($id, 'good')) {
        $_MGLOBAL['db']->query('UPDATE ' . tname('article') . ' SET `good`=`good`+1 WHERE `id` = ' . $id);
        $setsqlarr = array('ip' => $_MGLOBAL['onlineip'], 'dateline' => $_MGLOBAL['timestamp']);//动作
        if ($_MGLOBAL['uid'] > 0) {
            $setsqlarr['uid'] = $_MGLOBAL['uid'];
            $setsqlarr['username'] = $_MGLOBAL['username'];
        }
        $_MGLOBAL['db']->inserttable('action_logs', array('acid' => $id, 'name' => 'good', 'action' => json_encode($setsqlarr, JSON_UNESCAPED_UNICODE)));//写入动态表
        echo 'goodid.innerHTML=num;';//让原来的数+1
    } else {
        echo 'alert("您已经点过赞了，感谢您的参与！");';
    }
    exit;
}
if (!empty($_MGET['hot'])) {
    //用户对评论点赞
    $id = intval($_MGET['hot']);
    if (chickaction($id, 'hot')) {
        $_MGLOBAL['db']->query('UPDATE ' . tname('comments') . ' SET `hot`=`hot`+1 WHERE `cid` = ' . $id);
        $setsqlarr = array('ip' => $_MGLOBAL['onlineip'], 'dateline' => $_MGLOBAL['timestamp']);//动作
        if ($_MGLOBAL['uid'] > 0) {
            $setsqlarr['uid'] = $_MGLOBAL['uid'];
            $setsqlarr['username'] = $_MGLOBAL['username'];
        }
        $_MGLOBAL['db']->inserttable('action_logs', array('acid' => $id, 'name' => 'hot', 'action' => json_encode($setsqlarr, JSON_UNESCAPED_UNICODE)));//写入动态表
        echo 'hotid.innerHTML=num;';//让原来的数+1
    } else {
        echo 'alert("您已经点过赞了，感谢您的参与！");';
    }
    exit;
}