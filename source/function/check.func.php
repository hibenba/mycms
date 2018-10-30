<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 check.func.php Mr.Kwok
 * Created Time:2018/9/20 12:36
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//检测用户名是否合法
function is_username($username)
{
    $strlen = strlen($username);
    if (!preg_match("/^[a-zA-Z0-9\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)
    ) {
        return false;
    } elseif (15 < $strlen || $strlen < 4) {
        return false;
    }
    return true;
}

//Emain检测
function is_Email($email)
{
    $pattern = "/^([A-Za-z0-9_\\-\\.])+\\@([A-Za-z0-9_\\-\\.])+\\.([A-Za-z]{2,5})$/i";
    preg_match($pattern, strtolower($email), $matches);
    if (empty($matches[0])) {
        return false;
    } else {
        return $matches[0];
    }
}

//返回密码算法
function getpw($pw, $user)
{
    return md5($pw . '|' . $user);
}
//对提交的表单进行安全检测
function submitcheck($var, $checksec = 0)
{
    if (!empty($_POST[$var]) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        if ((empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])) && $_POST['formhash'] == formhash()) {
            return true;
        } else {
            showmessage(2, '您请求来路不正确或表单验证串不符，无法提交。请尝试使用标准的web浏览器进行操作。');
        }
    } else {
        return false;
    }
}