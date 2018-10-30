<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_categorys.php Mr.Kwok
 * Created Time:2018/10/26 19:23
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
if (submitcheck('batchsubmit')) {
    //批量修改
    if (is_array($_POST['displayorder'])) {
        foreach ($_POST['displayorder'] as $postcatid => $postdisplayorder) {
            $postdisplayorder = empty($postdisplayorder) ? 0 : intval($postdisplayorder);
            $postcatid = intval($postcatid);
            $prehtml = trim(strfilter($_POST['prehtml'][$postcatid]));
            $_MGLOBAL['db']->updatetable('categories', array('displayorder' => $postdisplayorder, 'prehtml' => $prehtml), array('catid' => $postcatid));
        }
    }
    updatecategorycache();
    sheader($theurl);//回到原页面
}
if (submitcheck('subcat')) {
    $_POST['catid'] = intval($_POST['catid']);
    $_POST['name'] = trim(strfilter(strip_tags($_POST['name'])));
    if (strlen($_POST['name']) < 2 || strlen($_POST['name']) > 50) {
        showmessage(3,'分类的字数太少了，请大于2个汉字，不超过15个字');
    }
    $htmlpath = trim(strfilter($_POST['htmlpath']));
    if (empty($htmlpath)) {
        showmessage(3,'目录不能为空，请返回修改！');
    } else {
        $htmlpath = chickpath($htmlpath, 'categories', 'htmlpath', $_POST['catid']);
    }
    $_POST['upid'] = empty($_POST['upid']) ? 0 : intval($_POST['upid']);
    $setsqlarr = array(
        'upid' => $_POST['upid'],
        'name' => $_POST['name'],
        'note' => filters_outcontent(trim(strip_tags($_POST['note']))),
        'title' => filters_outcontent(trim($_POST['title'])),
        'displayorder' => intval($_POST['displayorder']),
        'tpl' => trim(strfilter($_POST['tpl'])),
        'viewtpl' => trim(strfilter($_POST['viewtpl'])),
        'htmlpath' => $htmlpath,
        'perpage' => empty($_POST['perpage']) ? 30 : intval($_POST['perpage']),
        'prehtml' => trim(strfilter($_POST['prehtml']))
    );
    if ($_POST['catid']) {
        //修改分类
        $cat = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('categories') . ' WHERE catid=' . $_POST['catid']);
        if (empty($cat)) {
            showmessage(3,'编辑的分类不存在，请返回检查!');
        } elseif ($cat['htmlpath'] == $_POST['htmlpath']) {
            $setsqlarr['htmlpath'] = $cat['htmlpath'];
        }
        $_MGLOBAL['db']->updatetable('categories', $setsqlarr, array('catid' => $_POST['catid']));
    } else {
        //增加分类
        $_MGLOBAL['db']->inserttable('categories', $setsqlarr);
    }
    updatecategorycache();
    sheader($theurl);//回到原页面
}
if (!empty($_MGET['delete'])) {
    //单个删除
    $catid = intval($_MGET['delete']);
    if ($_MGLOBAL['db']->fetch_first('SELECT catid FROM ' . tname('categories') . " WHERE upid=" . $catid)) {
        showmessage(2,'当前分类下面还有子分类，请先删除子分类后再进行本操作');
    } else {
        //移动待删除分类下的文章到垃圾箱
        $_MGLOBAL['db']->updatetable('article', array('folder' => 2), array('catid' => $catid));
        //删除分类
        $_MGLOBAL['db']->deletetable('categories', array('catid' => $catid));
        updatecategorycache();
        sheader($theurl);//回到原页面
    }
}
$catarr = array();
$i = 0;
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('categories') . ' where upid = 0 ORDER BY displayorder');
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $i++;
    $catarr[$i] = $value;
    //子分类
    $query2 = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('categories') . ' where upid = ' . $value['catid'] . ' ORDER BY displayorder');
    while ($value2 = $_MGLOBAL['db']->fetch_array($query2)) {
        $i++;
        $catarr[$i] = $value2;
    }
}
$catjosn = json_encode($_MGLOBAL['category']);
include template(TPLDIR . 'categorys.htm', 1);