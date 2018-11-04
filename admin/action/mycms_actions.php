<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_actions.php Mr.Kwok
 * Created Time:2018/10/31 10:58
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (!empty($_MGET['delete'])) {
    $delid = intval($_MGET['delete']);
    $actions = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('actions') . ' WHERE id=' . $delid);
    if (empty($actions)) {
        showmessage(2, '删除的模块不存在，请返回检查!');
    } elseif ($actions['modify'] != 0) {
        showmessage(2, '无法删除的模块，请返回检查!');
    } else {
        //单个删除
        $_MGLOBAL['db']->deletetable('actions', array('id' => intval($_MGET['delete'])));//删除
        sheader($refer);//回到原页面
    }
}
if (submitcheck('subactions')) {
    $id = empty($_POST['id']) ? 0 : intval($_POST['id']);
    if (strlen($_POST['name']) < 2 && strlen($_POST['name']) > 30) {
        showmessage(3, '您输入的模块名字长度不符合要求(2~30个字符)！');
    } else {
        preg_match("|\w*|", $_POST['name'], $matches);
        if (empty($matches[0])) {
            showmessage(3, '模块名字只能是拼音或者英文，并且会自动转为小写！');
        } else {
            $name = strtolower($matches[0]);//转为小写
        }
    }
    $url_model = $_POST['url_model'] < 0 || $_POST['url_model'] >= 3 ? 0 : intval($_POST['url_model']);
    $cachetime = $_POST['cachetime'] < 0 ? 0 : intval($_POST['cachetime']);
    $url_rewrite = strip_tags(trim($_POST['url_rewrite']));
    $description = cutstr(strip_tags(trim($_POST['description'])), 120);
    $sqlarr = array(
        'id' => $id,
        'name' => $name,
        'url_model' => $url_model,
        'cachetime' => $cachetime,
        'url_rewrite' => $url_rewrite,
        'description' => $description,
        'modify' => 0
    );
    if (empty($id)) {
        //新增
        $_MGLOBAL['db']->inserttable('actions', $sqlarr);
    } else {
        $actions = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('actions') . ' WHERE id=' . $id);
        if (empty($actions)) {
            showmessage(2, '编辑的模块不存在，请返回检查!');
        }
        unset($sqlarr['id']);
        unset($sqlarr['modify']);
        unset($sqlarr['name']);
        $_MGLOBAL['db']->updatetable('actions', $sqlarr, array('id' => $id));
    }
    sheader($theurl);//回到原页面

}
if (!empty($_MGET['edit'])) {
    if ($_MGET['edit'] == 'add') {
        $h2 = '<h2>增加一个模块</h2>';
        $sqlarr = array(
            'id' => 0,
            'name' => '',
            'url_model' => 0,
            'cachetime' => 0,
            'url_rewrite' => '',
            'description' => '',
            'modify' => 0
        );
    } else {
        $sqlarr = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('actions') . ' WHERE id=' . intval($_MGET['edit']));
        $h2 = '<h2>编辑模块：' . $sqlarr['name'] . '</h2>';
        if (empty($sqlarr)) {
            showmessage(2, '模块不存在，请返回检查!');
        }
    }
} else {
    $catarr = array();
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('actions'));
    while ($value = $_MGLOBAL['db']->fetch_array($query)) {
        $catarr[$value['id']] = $value;
    }
    $url_model = array('动态', '伪静态', '静态');
    $catjosn = json_encode($catarr);
}
include template(TPLDIR . 'actions.htm', 1);