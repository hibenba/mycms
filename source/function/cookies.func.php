<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 cookies.php Mr.Kwok
 * Created Time:2018/9/20 12:38
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//获取用户cookies
function getcookie()
{
    global $_MGLOBAL, $_MCONFIG, $_GET;
    $_MGLOBAL['member'] = array(
        'uid' => 0,
        'groupid' => 2,
        'username' => 'Guest',
        'password' => ''
    );
    $cookie = isset($_COOKIE[$_MCONFIG['cookiepre'] . 'auth']) ? $_COOKIE[$_MCONFIG['cookiepre'] . 'auth'] : '';
    if ($cookie) {
        @list($password, $uid) = explode("\t", authcode($cookie, 'DECODE'));
        $_MGLOBAL['uid'] = intval($uid);
        $_MGLOBAL['member'] = $_MGLOBAL['db']->fetch_first('SELECT `uid`, `groupid`, `username`, `password`, `email`, `experience`, `dateline`, `updatetime`, `lastlogin`, `regip`, `lastloginip`, `lastcommenttime`, `lastposttime`, `avatar` FROM ' . tname('members') . ' WHERE `uid`=\'' . $_MGLOBAL['uid'] . '\' AND `password`=\'' . $password . '\'');
    } else {
        $_MGLOBAL['uid'] = 0;
    }
    if (empty($_MGLOBAL['uid'])) sclearcookie();
    $_MGLOBAL['member']['timeoffset'] = empty($_MGLOBAL['member']['timeoffset']) ? $_MCONFIG['timeoffset'] : $_MGLOBAL['member']['timeoffset'];
    //用户组
    @include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'group.cache.php');
    $_MGLOBAL['group'] = $_MGLOBAL['grouparr'][$_MGLOBAL['member']['groupid']];
    //用户名处理
    $_MGLOBAL['username'] = isset($_MGLOBAL['member']['username']) ? $_MGLOBAL['member']['username'] : $_MGLOBAL['username'];
}

//删除cookie
function sclearcookie()
{
    ssetcookie('auth', '', -31536000);
    ssetcookie('user', '', -31536000);
}

//设置cookie
function ssetcookie($var, $value, $life = 0)
{
    global $_MGLOBAL, $_MCONFIG;
    setcookie($_MCONFIG['cookiepre'] . $var, $value, $life ? $_MGLOBAL['timestamp'] + $life : 0, $_MCONFIG['cookiepath'], $_MCONFIG['cookiedomain'], $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
}

function authcode($string, $operation, $key = '', $expiry = 0)
{
    global $_MGLOBAL;
    $ckey_length = 4;    // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $key = md5($key ? $key : $_MGLOBAL['authkey']);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}