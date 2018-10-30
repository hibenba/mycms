<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_topic.php Mr.Kwok
 * Created Time:2018/10/26 18:48
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (!empty($_MGET['closed'])) {
    //单个禁用
    $_MGLOBAL['db']->updatetable('topic', array('close' => 1), array('id' => intval($_MGET['closed'])));
    sheader($refer);//回到原页面
}
if (!empty($_MGET['open'])) {
    //单个启用
    $_MGLOBAL['db']->updatetable('topic', array('close' => 0), array('id' => intval($_MGET['open'])));
    sheader($refer);//回到原页面
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $_MGLOBAL['db']->deletetable('topic', array('id' => intval($_MGET['delete'])));//删除
    sheader($refer);//回到原页面
}
if (submitcheck('subtopic')) {
    $id = empty($_POST['id']) ? 0 : intval($_POST['id']);
    if (strlen($_POST['name']) < 2) {
        showmessage(3, '您输入的标题长度不符合要求(2~80个字符)！');
    } else {
        $name = cutstr(strip_tags(trim($_POST['name'])), 20);
    }
    $note = cutstr(strip_tags(trim($_POST['note'])), 120);
    $title = cutstr(strip_tags(trim($_POST['title'])), 120);
    $dateline = $_MGLOBAL['timestamp'];
    $lastpost = $_MGLOBAL['timestamp'];
    $htmlpath = trim(strfilter($_POST['html']));
    $perpage = empty($_POST['perpage']) ? 30 : intval($_POST['perpage']);
    if (empty($htmlpath)) {
        showmessage(3, '目录不能为空，请返回修改！');
    } else {
        $htmlpath = chickpath($htmlpath, 'topic', 'htmlpath', $id);
    }
    $sqlarr = array(
        'id' => $id,
        'name' => $name,
        'note' => $note,
        'title' => $title,
        'dateline' => $dateline,
        'lastpost' => $lastpost,
        'content' => filters_outcontent($_POST['content']),
        'tpl' => trim(strfilter($_POST['tpl'])),
        'htmlpath' => $htmlpath,
        'perpage' => $perpage
    );
    if (!empty($id)) {
        //编辑
        $topic = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('topic') . ' WHERE id=' . $id);
        if (empty($topic)) {
            showmessage(2, '编辑的专题不存在，请返回检查!');
        } elseif ($topic['htmlpath'] == $_POST['html']) {
            $sqlarr['htmlpath'] = $topic['htmlpath'];
        }
        $sqlarr['dateline'] = $topic['dateline'];
        $_MGLOBAL['db']->updatetable('topic', $sqlarr, array('id' => $id));
    } else {
        //新增
        $_MGLOBAL['db']->inserttable('topic', $sqlarr);
    }
    sheader($theurl);//回到原页面
}
if (submitcheck('opsubmit')) {
    //批量操作
    if (empty($_POST['topicarr'])) {
        showmessage(3, '请选择你要操作的标签!', $theurl);
    }
    switch ($_POST['optag']) {
        case 'close'://禁用
            $sqlarr = array('close' => 1);
            break;
        case 'open'://恢复
            $sqlarr = array('close' => 0);
            break;
        case 'delete'://删除
            foreach ($_POST['topicarr'] as $value) {
                $_MGLOBAL['db']->deletetable('topic', array('id' => intval($value)));//删除
            }
            showmessage(1, '您已经成功删除专题表!', $refer);
            break;
        default:
            showmessage(3, '请选择要操作的类型,如:批量禁用', $refer);
            break;
    }
    if ($sqlarr) {
        foreach ($_POST['topicarr'] as $value) {
            $id = intval($value);
            if ($id) {
                $_MGLOBAL['db']->updatetable('topic', $sqlarr, array('id' => $id));
            }
        }
    }
    sheader($refer);//回到原页面
}
if (!empty($_MGET['edit'])) {
    if ($_MGET['edit'] == 'add') {
        $h2 = '<h2>增加一个专题</h2>';
        $sqlarr = array(
            'id' => 0,
            'name' => '',
            'note' => '',
            'title' => '',
            'content' => '',
            'tpl' => '',
            'htmlpath' => '',
            'perpage' => 30
        );
    } else {
        $sqlarr = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('topic') . ' WHERE id=' . intval($_MGET['edit']));
        $h2 = '<h2>编辑专题：' . $sqlarr['name'] . '</h2>';
        if (empty($sqlarr)) {
            showmessage(2, '专题不存在，请返回检查!');
        }
        $sqlarr['content'] = stripslashes($sqlarr['content']);//删除转义
    }
}
$order = 'dateline DESC';
$_MGET['order'] = empty($_MGET['order']) ? '' : $_MGET['order'];
if (!empty($_MGET['order'])) {
    switch ($_MGET['order']) {
        case 'id':
            $order = 'id ASC';
            break;
        case 'close':
            $order = 'close DESC';
            break;
        case 'viewnum':
            $order = 'viewnum DESC';
            break;
        case 'dateline':
            $order = 'dateline ASC';
            break;
        case 'lastpost':
            $order = 'lastpost ASC';
            break;
        default:
            break;
    }
}
$wheresqlstr = '';
if (submitcheck('searchkeyword')) {
    //搜索专题
    $_POST['keyword'] = strfilter($_POST['keyword']);
    $perpage = 999;
    $wheresqlstr = ' WHERE  `name` LIKE \'%' . $_POST['keyword'] . '%\'';
}
$perpage = empty($_MCONFIG['setarticlenum']) ? 30 : intval($_MCONFIG['setarticlenum']);//每页显示TAG数和文章数一样
$page = empty($_MGET['page']) ? 1 : intval($_MGET['page']);//当前页
$thispage = ($page - 1) * $perpage;
$count = $_MGLOBAL['db']->getcount('topic', '');//正常专题数
$urlarr = array('action' => 'topic');//分页地址
$topicarr = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('topic') . $wheresqlstr . ' ORDER BY  ' . $order . ' LIMIT ' . $thispage . ',' . $perpage);
while ($thetopic = $_MGLOBAL['db']->fetch_array($query)) {
    $topicarr[] = $thetopic;
}
$multipage = '<div class="left count">共有' . $count . '个专题！</div>';
if ($count > $perpage) {
    $urlarr['page'] = $page;
    $_MCONFIG['htmlmode'] = 0;//关闭后台的HTML识别，兼容分页
    $multipage .= multipage($count, $perpage, $page, $urlarr);
    $multipage = str_replace('/main.php', CPURL, $multipage);
}
include_once template(TPLDIR . 'topic.htm', 1);