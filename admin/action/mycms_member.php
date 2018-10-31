<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_member.php Mr.Kwok
 * Created Time:2018/10/31 10:28
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
function deleteuser($id)
{
    global $_MGLOBAL;
    $user = $_MGLOBAL['db']->fetch_first('SELECT groupid,uid FROM ' . tname('members') . ' WHERE uid=' . intval($id));
    if ($user['groupid'] == 1) {
        showmessage(2, '对不起，您不能直接删除管理员用户，请先把管理员用户修改为普通用户再进行本操作!');
    }
    if ($user['uid']) {
        $_MGLOBAL['db']->deletetable('members', array('uid' => $user['uid']));//删除用户表
        $query = $_MGLOBAL['db']->query('SELECT id FROM ' . tname('article') . ' where uid=' . $user['uid']);//删除文章
        while ($article = $_MGLOBAL['db']->fetch_array($query)) {
            delarticle($article['id']);
        }
        $_MGLOBAL['db']->deletetable('comments', array('uid' => $user['uid']));//删除用户评论
    }
}

if (!empty($_MGET['delete'])) {
    //单个删除
    deleteuser($_MGET['delete']);
    sheader($refer);//回到原页面
}
if (submitcheck('opsubmit')) {
    //批量删除
    if (empty($_POST['userarr'])) {
        showmessage(3, '请选择你要操作的用户!', $theurl);
    }
    foreach ($_POST['userarr'] as $value) {
        deleteuser($value);
    }
    sheader($refer);//回到原页面
}
if (submitcheck('addusersubmit')) {
    if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 20) {
        showmessage(3, '您输入的用户名长度不符合要求，请返回修改!');
    }
    $_POST['password'] = empty($_POST['password']) ? '' : $_POST['password'];
    $uid = empty($_POST['uid']) ? 0 : intval($_POST['uid']);
    $xpw = '';
    $username = strfilter(strtolower($_POST['username']));
    if (!empty($uid)) {
        $thisuser = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('members') . ' WHERE uid=' . $uid);
        extract($thisuser);//分解数组
        if (empty($uid)) {
            showmessage(2, '你所编辑的用户不存在，请返回检查!');
        }
        $pword = empty($_POST['password']) ? $password : getpw($_POST['password'], $username);
    } else {
        $lastloginip = '127.0.0.1';
        $pwmake = $formhash . rand(0, 9999);//生成1个随机密码
        $xpw = getpw($pwmake, $username);
        $pword = empty($_POST['password']) ? $xpw : getpw($_POST['password'], $username);
    }
    if (empty($_POST['email'])) {
        showmessage(3, '请输入Email地址，请返回修改!');
    } else {
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,6}(\\.[a-z]{2})?)$/i";
        preg_match($pattern, strtolower($_POST['email']), $matches);
        if (empty($matches[0])) {
            showmessage(3, '输入的Email地址不合法，请返回修改!');
        } else {
            $email = $matches[0];
        }
    }
    $dateline = empty($dateline) ? $_MGLOBAL['timestamp'] : intval($dateline);
    $lastlogin = empty($lastlogin) ? $_MGLOBAL['timestamp'] : intval($lastlogin);
    $regip = empty($regip) ? $_MGLOBAL['onlineip'] : $regip;
    $lastloginip = empty($lastloginip) ? $_MGLOBAL['onlineip'] : $lastloginip;
    $lastcommenttime = empty($lastcommenttime) ? 0 : intval($lastcommenttime);
    $lastposttime = empty($lastposttime) ? 0 : intval($lastposttime);
    $avatar = empty($avatar) ? 0 : 1;
    $experience = empty($_POST['experience']) ? 0 : intval($_POST['experience']);
    $groupid = empty($_POST['groupid']) ? 11 : intval($_POST['groupid']);
    $setsqlarr = array(
        'uid' => $uid,
        'groupid' => $groupid,
        'username' => $username,
        'password' => $pword,
        'email' => $email,
        'experience' => $experience,
        'dateline' => $dateline,
        'updatetime' => $_MGLOBAL['timestamp'],
        'lastlogin' => $lastlogin,
        'regip' => $regip,
        'lastloginip' => $lastloginip,
        'lastcommenttime' => $lastcommenttime,
        'lastposttime' => $lastposttime,
        'avatar' => $avatar
    );
    if (empty($thisuser)) {
        //增加用户
        $getuser = $_MGLOBAL['db']->fetch_first("SELECT username FROM " . tname('members') . " WHERE username='$username'");
        if (empty($getuser['username'])) {
            $_MGLOBAL['db']->inserttable('members', $setsqlarr);
        } else {
            showmessage(2, '你要增加的用户已经存在了，请返回修改!');
        }
    } else {
        //编辑用户
        $_MGLOBAL['db']->updatetable('members', $setsqlarr, array('uid' => $uid));
    }
    if ($pword == $xpw) {
        showmessage(1, '您的操作已成功完成!并随机生成了1个密码为：' . $pwmake, $theurl, 6);
    } else {
        showmessage(1, '您的操作已成功完成!', $theurl);
    }
}
if (!empty($_MGET['edit'])) {
    if ($_MGET['edit'] == 'add') {
        $h2 = '<h2>添加一个用户</h2>';
        $setsqlarr = array(
            'uid' => 0,
            'username' => '',
            'groupid' => 11,
            'email' => '',
            'experience' => ''
        );
        $pwnotice = '请输入一个6-18位的密码！如果留空系统将会随机生成1个密码。';
    } else {
        $setsqlarr = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('members') . ' WHERE uid=' . intval($_MGET['edit']));
        if (empty($setsqlarr)) {
            showmessage(2, '用户不存在，请返回检查!');
        }
        $h2 = '<h2>编辑用户：' . $setsqlarr['username'] . '</h2>';
        $pwnotice = '请输入密码，留空表示不修改密码！';
    }
}
$order = 'dateline DESC';
if (!empty($_MGET['order'])) {
    switch ($_MGET['order']) {
        case 'id':
            $order = 'uid DESC';
            break;
        case 'group':
            $order = 'groupid ASC';
            break;
        case 'exp':
            $order = 'experience DESC';
            break;
        case 'reg':
            $order = 'dateline ASC';
            break;
        case 'login':
            $order = 'lastlogin ASC';
            break;
        case 'update':
            $order = 'updatetime ASC';
            break;
        default:
            break;
    }
}
$perpage = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示TAG数和文章数一样
$count = $_MGLOBAL['db']->getcount('members', '');//用户数
$wheresqlstr = '';
if (submitcheck('searchkeyword')) {
    //搜索标签
    $_POST['username'] = strfilter($_POST['username']);
    $perpage = 999;
    $wheresqlstr = ' WHERE  `username` LIKE \'%' . $_POST['username'] . '%\'';
    $count = 1;
}
$_MGET['order'] = empty($_MGET['order']) ? '' : $_MGET['order'];
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $perpage;
$urlarr = array('action' => 'member');//分页地址
$usersarr = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('members') . $wheresqlstr . ' ORDER BY  ' . $order . ' LIMIT ' . $thispage . ',' . $perpage);
while ($theuser = $_MGLOBAL['db']->fetch_array($query)) {
    $usersarr[] = $theuser;
}
$multipage = '<div class="left count">共有' . $count . '个用户！</div>';
if ($count > $perpage) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage .= multipage($count, $perpage, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
}
include template(TPLDIR . 'member.htm', 1);