<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 cache.func.php Mr.Kwok
 * Created Time:2018/9/20 10:30
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//数组转换成字串
function arrayeval($array, $level = 0)
{
    $comma = '';
    $evaluate = "array(";
    foreach ($array as $key => $val) {
        $key = is_string($key) ? '\'' . addcslashes($key, '\'\\') . '\'' : $key;
        $val = !is_array($val) && (!preg_match("/^\-?\d+$/", $val) || strlen($val) > 12) ? '\'' . addcslashes($val, '\'\\') . '\'' : $val;
        if (is_array($val)) {
            $evaluate .= "$comma$key => " . arrayeval($val, $level + 1);
        } else {
            $evaluate .= "$comma$key => $val";
        }
        $comma = ",";
    }
    $evaluate .= ")";
    return $evaluate;
}

//更新用户组CACHE
function updategroupcache()
{
    global $_MGLOBAL;
    $_MGLOBAL['grouparr'] = array();
    $highest = true;
    $lower = '';
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('usergroups') . ' ORDER BY system ASC, explower DESC ');
    while ($group = $_MGLOBAL['db']->fetch_array($query)) {
        if ($group['system'] == 0) {
            //是否是最高上限
            if ($highest) {
                $group['exphigher'] = 999999999;
                $highest = false;
                $lower = $group['explower'];
            } else {
                $group['exphigher'] = $lower - 1;
                $lower = $group['explower'];
            }
        }
        $_MGLOBAL['grouparr'][$group['groupid']] = $group;
    }

    $cachefile = M_ROOT . 'data/system/group.cache.php';
    $cachetext = '$_MGLOBAL[\'grouparr\']=' . arrayeval($_MGLOBAL['grouparr']) . ';';
    writefile($cachefile, $cachetext, 'php');
}

//更新基本配置CACHE
function updatesettingcache()
{
    global $_MGLOBAL;
    @include(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'config.php');
    $query = $_MGLOBAL['db']->query('SELECT `variable`,`value` FROM ' . tname('settings'));
    while ($set = $_MGLOBAL['db']->fetch_array($query)) {
        $_MCONFIG[$set['variable']] = $set['value'];
    }
    $query = $_MGLOBAL['db']->query('SELECT `name`,`url_model`,`cachetime`,`url_rewrite` FROM ' . tname('actions'));
    while ($value = $_MGLOBAL['db']->fetch_array($query)) {
        $_MCONFIG['actions'][$value['name']] = $value;
    }
    $cachetext = '$_MCONFIG=' . arrayeval($_MCONFIG) . ';';
    writefile(M_ROOT . 'data/system/config.cache.php', $cachetext, 'php');
}

//更新cron列表
function updatecronscache()
{
    global $_MGLOBAL;
    $carr = array();
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('crons') . ' WHERE available>0');
    while ($cron = $_MGLOBAL['db']->fetch_array($query)) {
        $cron['filename'] = str_replace(array('..', '/', '\\'), array('', '', ''), $cron['filename']);
        $cron['minute'] = explode("\t", $cron['minute']);
        $carr[$cron['cronid']] = $cron;
    }
    $cachefile = M_ROOT . 'data/system/crons.cache.php';
    $cachetext = '$_MGLOBAL[\'crons\']=' . arrayeval($carr) . ';';
    writefile($cachefile, $cachetext, 'php');
}

//更新计划任务的CACHE
function updatecroncache($cronnextrun = 0)
{
    global $_MGLOBAL;

    if (empty($cronnextrun)) {
        $cronnext = $_MGLOBAL['db']->fetch_first('SELECT nextrun FROM ' . tname('crons') . ' WHERE available>0 AND nextrun>\'' . $_MGLOBAL['timestamp'] . '\' ORDER BY nextrun LIMIT 1');
        $cronnextrun = $cronnext['nextrun'];
    }
    if (empty($cronnextrun)) {
        $cronnextrun = $_MGLOBAL['timestamp'] + 7200;
    }
    $_MGLOBAL['db']->query('UPDATE ' . tname('settings') . ' SET `value` = ' . $cronnextrun . ' WHERE `variable` = \'cronnextrun\'');
    updatesettingcache();
}

//缓存语言屏蔽
function updatecensorcache()
{
    global $_MGLOBAL;
    $_MGLOBAL['censor'] = array();
    $banned = $mod = array();
    $_MGLOBAL['censor'] = array('filter' => array(), 'banned' => '', 'mod' => '');
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('bad_words'));
    while ($censor = $_MGLOBAL['db']->fetch_array($query)) {
        $censor['find'] = preg_replace("/\\\{(\d+)\\\}/", ".{0,\\1}", preg_quote($censor['find'], '/'));
        switch ($censor['replace']) {
            case '{BANNED}':
                $banned[] = $censor['find'];
                break;
            case '{MOD}':
                $mod[] = $censor['find'];
                break;
            default:
                $_MGLOBAL['censor']['filter']['find'][] = '/' . $censor['find'] . '/i';
                $_MGLOBAL['censor']['filter']['replace'][] = $censor['replace'];
                break;
        }
    }
    if ($banned) {
        $_MGLOBAL['censor']['banned'] = '/(' . implode('|', $banned) . ')/i';
    }
    if ($mod) {
        $_MGLOBAL['censor']['mod'] = '/(' . implode('|', $mod) . ')/i';
    }
    //make cache
    $cachefile = M_ROOT . 'data/system/censor.cache.php';
    $cachetext = '$_MGLOBAL[\'censor\']=' . arrayeval($_MGLOBAL['censor']) . ';';
    writefile($cachefile, $cachetext, 'php');
}