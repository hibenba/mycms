<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_crons.php Mr.Kwok
 * Created Time:2018/10/31 9:44
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
include(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
if (submitcheck('cronsubmit')) {
    //编辑或者增加任务
    $_POST['cronid'] = intval($_POST['cronid']);
    $_POST['name'] = strfilter($_POST['name']);
    if (strlen($_POST['name']) < 2 || strlen($_POST['name']) > 30) {
        showmessage(3, '您输入的名称长度不符合要求(2~15个字符)！');
    }
    $_POST['available'] = empty($_POST['available']) ? 0 : 1;
    if (!is_readable(SOUREC_DIR . 'function/crons/' . $_POST['filename'])) {
        showmessage(2, '计划任务文件不可读');
    }
    if ($_POST['weekday'] != '-1') {
        $_POST['day'] = '-1';
    }
    if (is_array($_POST['minute']) && $_POST['minute']) {
        foreach ($_POST['minute'] as $key => $var) {
            if ($var < 0 || $var > 59) {
                unset($_POST['minute'][$key]);
            }
        }
        sort($_POST['minute']);
        $_POST['minute'] = sarray_unique($_POST['minute']);
    }
    $postminute = implode("\t", $_POST['minute']);
    if ($_POST['weekday'] == -1 && $_POST['day'] == -1 && $_POST['hour'] == -1 && $postminute == '') {
        showmessage(3, '任务未指定1个正确的时间，请返回修改！');
    }
    $sqlarr = array(
        'name' => $_POST['name'],
        'filename' => $_POST['filename'],
        'available' => $_POST['available'],
        'weekday' => $_POST['weekday'],
        'day' => $_POST['day'],
        'hour' => $_POST['hour'],
        'minute' => $postminute
    );
    if (empty($_POST['cronid'])) {
        //增加新的任务
        $sqlarr['nextrun'] = $_MGLOBAL['timestamp'] + 100;
        $_MGLOBAL['db']->inserttable('crons', $sqlarr);
    } else {
        //编辑任务
        $_MGLOBAL['db']->updatetable('crons', $sqlarr, array('cronid' => $_POST['cronid']));
    }
    //更新缓存
    updatecronscache();
    updatecroncache();
    sheader($theurl);
}
if (!empty($_MGET['delete'])) {
    $_MGLOBAL['db']->deletetable('crons', array('cronid' => intval($_MGET['delete'])));
    updatecronscache();
    showmessage(1, '计划任务已成功删除', $theurl);
}
if (!empty($_MGET['off'])) {
    //禁用
    $_MGLOBAL['db']->updatetable('crons', array('available' => 0), array('cronid' => intval($_MGET['off'])));
    updatecronscache();
    sheader($theurl);
}
if (!empty($_MGET['open'])) {
    //启用
    $_MGLOBAL['db']->updatetable('crons', array('available' => 1), array('cronid' => intval($_MGET['open'])));
    updatecronscache();
    sheader($theurl);
}
if (!empty($_MGET['edit'])) {
    if ($_MGET['edit'] == 'add') {
        $thevalue = array(
            'cronid' => '',
            'name' => '',
            'available' => 1,
            'filename' => '',
            'weekday' => '-1',
            'day' => '-1',
            'hour' => '-1',
            'minute' => ''
        );
    } else {
        $thevalue = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('crons') . ' WHERE cronid=' . intval($_MGET['edit']));
        if (empty($thevalue)) {
            showmessage(3, '您编辑的任务不存在，请返回检查!', $theurl);
        }
    }
}
if (!empty($_MGET['run'])) {
    //执行任务
    include_once(SOUREC_DIR . 'function/cron.func.php');
    runcron(intval($_MGET['run']));
    sheader($theurl);
}

if (!empty($thevalue)) {
    $weekdayarr = array(
        '-1' => '*',
        '0' => '星期日',
        '1' => '星期一',
        '2' => '星期二',
        '3' => '星期三',
        '4' => '星期四',
        '5' => '星期五',
        '6' => '星期六'
    );
    $weekarr = '<select name="weekday" class="select">';
    foreach ($weekdayarr as $i => $value) {
        $selected = '';
        if ($thevalue['weekday'] == $i) {
            $selected = ' selected';
        }
        $weekarr .= '<option value="' . $i . '"' . $selected . '>' . $value . '</option>';
    }
    $weekarr .= '</select>';
    $dayarr = '<select name="day" class="select"><option value="-1" selected="">*</option>';
    for ($i = 1; $i < 32; $i++) {
        $selected = '';
        if ($thevalue['day'] == $i) {
            $selected = ' selected';
        }
        $dayarr .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
    }
    $dayarr .= '</select>';
    $hourarr = '<select name="hour" class="select"><option value="-1" selected="">*</option>';
    for ($i = 0; $i < 24; $i++) {
        $selected = '';
        if ($thevalue['hour'] == $i) {
            $selected = ' selected';
        }
        $hourarr .= '<option value="' . $i . '"' . $selected . '>' . $i . '</option>';
    }
    $hourarr .= '</select>';
    $minuteselect = '';
    $cronminutearr = explode("\t", trim($thevalue['minute']));
    for ($i = 0; $i < 12; $i++) {
        $minuteselect .= '<select name="minute[]" class="select"><option value="-1">*</option>';
        for ($j = 0; $j <= 59; $j++) {
            $selected = '';
            if (isset($cronminutearr[$i]) && $cronminutearr[$i] == $j) {
                $selected = ' selected';
            }
            $minuteselect .= '<option value="' . $j . '"' . $selected . '>' . sprintf("%02d", $j) . '</option>';
        }
        $minuteselect .= '</select>';
    }
    $filearr = sreaddir(SOUREC_DIR . 'function/crons/', 'php');
    $filename = '';
    foreach ($filearr as $value) {
        if ($thevalue['filename'] == $value) {
            $filename .= '<option value="' . $value . '" selected>' . $value . '</option>';
        } else {
            $filename .= '<option value="' . $value . '">' . $value . '</option>';
        }
    }
    $available = $available1 = '';
    if ($thevalue['available'] == 0) {
        $available1 = ' checked="checked"';
    } else {
        $available = ' checked="checked"';
    }
}

$crons = array();
$crons = array();
$query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('crons') . ' LIMIT 100');
while ($value = $_MGLOBAL['db']->fetch_array($query)) {
    $crons[$value['cronid']] = $value;
}
$cronsjosn = json_encode($crons);
include template(TPLDIR . 'crons.htm', 1);