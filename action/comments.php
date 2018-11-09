<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 comments.php Mr.Kwok
 * Created Time:2018/9/20 14:01
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
if (!empty($_MGET['getcomment'])) {
    //获取评论框
    $formhash = formhash();
    if (empty($_MCONFIG['noseccode'])) {
        echo 'document.writeln("<p class=\"seccode_p\"><strong>验证码:</strong> <input size=\"30\" type=\"text\" value=\"\" onblur=\"chickseccode(this.value)\" id=\"sec_input\" name=\"seccode\"/> <span id=\"seccode\" class=\"chicksec\"></span> <img src=\"' . geturl('action/validate') . '\" id=\"validate\" title=\"点击刷新\" onClick=\"this.src=\'' . geturl('action/validate') . '-\'+Math.random();\"/></p>");';
    }
    echo 'document.writeln("<input type=\"hidden\" name=\"formhash\" value=\"' . $formhash . '\" />");';
    echo 'document.writeln("<script>function chickseccode(seccode){var seccodeHTML=document.getElementById(\"seccode\");if(seccode==\'\'){seccodeHTML.innerHTML=\'<i class=\"error\"></i>\'}else{ajax({method:\'POST\',url:\'' . geturl('action/ajax') . '\',data:{formhash:\'' . $formhash . '\',seccode:seccode,seccodechick:true},success:function(e){seccodeHTML.innerHTML=e}})}}</script>");';
    exit;
}
connectMysql();
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'check.func.php');
include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cookies.func.php');
getcookie();
if (submitcheck('commentssubmit') && !empty($_MGET['post'])) {
    //发表评论
    if ($_MGLOBAL['uid'] > 0 && !empty($_MGLOBAL['username'])) {
        if ($_MGLOBAL['timestamp'] - $_MGLOBAL['member']['lastcommenttime'] < 600) {
            showmessage(3, '您发表评论的速度太快了，每次评论需要间隔10分钟！');
        }
        $aid = intval($_MGET['post']);
        $refer = empty($_SERVER['HTTP_REFERER']) ? geturl('action/comments/id/' . $aid) : $_SERVER['HTTP_REFERER'];
        if (empty($_MCONFIG['noseccode'])) {
            session_start();
            if ($_MGLOBAL['timestamp'] - $_SESSION['seccodetime'] > 1200) {
                showmessage(2, '验证码已失效，请重新输入');
            }
            if ($_POST['seccode'] != $_SESSION['seccode']) {
                showmessage(2, '输入的验证码不符，请重新输入');
            }
        }
        $news = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('article') . ' WHERE id=' . $aid);
        if ($news) {
            $message = trim((strip_tags($_POST['message'])));
            if (strlen($message) < 10) {
                showmessage(3, '评论的字数太少了！');
            }
            $subject = empty($_POST['subject']) ? '' : cutstr(trim(strfilter(strip_tags($_POST['subject']))), 80);
            $setsqlarr = array(
                'cid' => 0,
                'id' => $news['id'],
                'uid' => $_MGLOBAL['uid'],
                'username' => $_MGLOBAL['username'],
                'ip' => $_MGLOBAL['onlineip'],
                'dateline' => $_MGLOBAL['timestamp'],
                'subject' => $subject,
                'message' => $message,
                'hot' => 0,
                'hideauthor' => 0,
                'status' => 1
            );
            $cid = $_MGLOBAL['db']->inserttable('comments', $setsqlarr, 1);//写入评论
            $_MGLOBAL['db']->query('UPDATE ' . tname('article') . ' SET replynum=replynum+1 WHERE id = ' . $news['id']);//评论数量增加
            $_MGLOBAL['db']->query('UPDATE ' . tname('members') . ' SET experience=experience+1,lastcommenttime=' . $_MGLOBAL['timestamp'] . ' WHERE uid = ' . $_MGLOBAL['uid']);//积分和最新评论时间
            unset($setsqlarr['message']);
            unset($setsqlarr['status']);
            unset($setsqlarr['hot']);
            $setsqlarr['cid'] = $cid;
            $setsqlarr['subject'] = $news['subject'];
            $_MGLOBAL['db']->inserttable('action_logs', array('name' => 'comments', 'acid' => $news['id'], 'action' => json_encode($setsqlarr, JSON_UNESCAPED_UNICODE)));//写入动态表
        } else {
            showmessage(2, '您要评论的文章不存在！');
        }
        showmessage(1, '您的评论发布成功，需要管理员审核后显示！', $refer);
    } else {
        showmessage(3, '您还没有登陆，请登陆后发表评论...', geturl('action/login'));
    }
}
$id = empty($_MGET['id']) ? 0 : intval($_MGET['id']);
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);
$setarticlenum = 100;
$thispage = ($page - 1) * $setarticlenum;
$news = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('article') . ' WHERE id=' . $id);
if ($news) {
    $count = $_MGLOBAL['db']->getcount('comments', array('id' => $id, 'status' => 0));//评论数
    if ($count > 0) {
        $query = $_MGLOBAL['db']->query('SELECT cid,uid,username,dateline,message,hot,hideauthor FROM ' . tname('comments') . ' WHERE id=' . $news['id'] . ' and status=0 ORDER BY `cid` DESC LIMIT ' . $thispage . ',' . $setarticlenum);
        $comments = array();
        $i = $count;
        while ($comment = $_MGLOBAL['db']->fetch_array($query)) {
            $comment['i'] = $i;
            $comments[] = $comment;
            $i--;
        }
    }
    if ($count > $setarticlenum) {
        $urlarr = array('action' => 'comments', 'page' => $page);//分页地址
        $multipage = multi($count, $setarticlenum, $page, $urlarr);
    } else {
        $multipage = '';
    }
    $title = $keywords = $description = '评论《' . strip_tags($news['subject']) . '》_' . $_MCONFIG['sitename'];
    include template('comment');
} else {
    notfoundmessage();//ID不正确
}