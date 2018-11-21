<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 cron.func.php Mr.Kwok
 * Created Time:2018/9/20 10:24
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
include(DATA_DIR . 'system' . DIRECTORY_SEPARATOR . 'crons.cache.php');
//执行计划任务，并更新计划任务CACHE
function runcron($cronid = 0)
{
    global $_MGLOBAL;
    //锁定
    $lockfile = DATA_DIR . 'log' . DIRECTORY_SEPARATOR . 'cron.lock.log';
    if (file_exists($lockfile)) {
        if ($_MGLOBAL['timestamp'] - filemtime($lockfile) < 300) {//5分钟
            return;
        }
    }
    if (@$fp = fopen($lockfile, 'w')) {
        fwrite($fp, PHP_EOL);
        fclose($fp);
    }
    //读取cron列表缓存
    if (empty($_MGLOBAL['crons'])) return;
    @set_time_limit(1000);
    @ignore_user_abort(TRUE);
    $cronids = array();
    $crons = $cronid ? array($cronid => $_MGLOBAL['crons'][$cronid]) : $_MGLOBAL['crons'];
    if (empty($crons) || !is_array($crons)) return;
    foreach ($crons as $id => $cron) {
        if ($cron['nextrun'] <= $_MGLOBAL['timestamp'] || $id == $cronid) {
            $cronids[] = $id;
            if (!@include($cronfile = SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'crons' . DIRECTORY_SEPARATOR . $cron['filename'])) {
                errorlog('cron', json_encode(array('name' => $cron['name'], 'cronfile' => $cronfile, 'message' => '文件不存在或者语法错误')), 0);
            }
        }
    }
    cronnextrun($cronids);
    @unlink($lockfile);
}

//下次执行的时间
function cronnextrun($cronids)
{
    global $_MGLOBAL, $_MCONFIG;
    if (!is_array($cronids) || !$cronids) {
        return false;
    }
    $timestamp = $_MGLOBAL['timestamp'];
    $minutenow = gmdate('i', $timestamp + $_MCONFIG['timeoffset'] * 3600);
    $hournow = gmdate('H', $timestamp + $_MCONFIG['timeoffset'] * 3600);
    $daynow = gmdate('d', $timestamp + $_MCONFIG['timeoffset'] * 3600);
    $monthnow = gmdate('m', $timestamp + $_MCONFIG['timeoffset'] * 3600);
    $yearnow = gmdate('Y', $timestamp + $_MCONFIG['timeoffset'] * 3600);
    $weekdaynow = gmdate('w', $timestamp + $_MCONFIG['timeoffset'] * 3600);

    foreach ($cronids as $cronid) {
        if (!$cron = $_MGLOBAL['crons'][$cronid]) {
            continue;
        }
        if ($cron['weekday'] == -1) {
            if ($cron['day'] == -1) {
                $firstday = $daynow;
                $secondday = $daynow + 1;
            } else {
                $firstday = $cron['day'];
                $secondday = $cron['day'] + gmdate('t', $timestamp + $_MCONFIG['timeoffset'] * 3600);
            }
        } else {
            $firstday = $daynow + ($cron['weekday'] - $weekdaynow);
            $secondday = $firstday + 7;
        }
        if ($firstday < $daynow) {
            $firstday = $secondday;
        }
        if ($firstday == $daynow) {
            $todaytime = crontodaynextrun($cron);
            if ($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
                $cron['day'] = $secondday;
                $nexttime = crontodaynextrun($cron, 0, -1);
                $cron['hour'] = $nexttime['hour'];
                $cron['minute'] = $nexttime['minute'];
            } else {
                $cron['day'] = $firstday;
                $cron['hour'] = $todaytime['hour'];
                $cron['minute'] = $todaytime['minute'];
            }
        } else {
            $cron['day'] = $firstday;
            $nexttime = crontodaynextrun($cron, 0, -1);
            $cron['hour'] = $nexttime['hour'];
            $cron['minute'] = $nexttime['minute'];
        }
        $nextrun = gmmktime($cron['hour'], $cron['minute'], 0, $monthnow, $cron['day'], $yearnow) - $_MCONFIG['timeoffset'] * 3600;
        $_MGLOBAL['db']->query("UPDATE " . tname('crons') . " SET lastrun='" . $timestamp . "', nextrun='$nextrun' WHERE cronid='$cronid'");
    }
    include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'cache.func.php');
    updatecronscache();
    updatecroncache();
}

// gets next run time today after $hour, $minute
// returns -1,-1 if not again today
function crontodaynextrun($cron, $hour = -2, $minute = -2)
{
    global $_MGLOBAL, $_MCONFIG;

    $timestamp = $_MGLOBAL['timestamp'];
    $hour = $hour == -2 ? gmdate('H', $timestamp + $_MCONFIG['timeoffset'] * 3600) : $hour;
    $minute = $minute == -2 ? gmdate('i', $timestamp + $_MCONFIG['timeoffset'] * 3600) : $minute;

    $nexttime = array();
    if ($cron['hour'] == -1 && !$cron['minute']) {
        $nexttime['hour'] = $hour;
        $nexttime['minute'] = $minute + 1;
    } elseif ($cron['hour'] == -1 && $cron['minute'] != '') {
        $nexttime['hour'] = $hour;
        if (($nextminute = cronnextminute($cron['minute'], $minute)) === false) {
            ++$nexttime['hour'];
            $nextminute = $cron['minute'][0];
        }
        $nexttime['minute'] = $nextminute;
    } elseif ($cron['hour'] != -1 && $cron['minute'] == '') {
        if ($cron['hour'] < $hour) {
            $nexttime['hour'] = $nexttime['minute'] = -1;
        } else if ($cron['hour'] == $hour) {
            $nexttime['hour'] = $cron['hour'];
            $nexttime['minute'] = $minute + 1;
        } else {
            $nexttime['hour'] = $cron['hour'];
            $nexttime['minute'] = 0;
        }
    } elseif ($cron['hour'] != -1 && $cron['minute'] != '') {
        $nextminute = cronnextminute($cron['minute'], $minute);
        if ($cron['hour'] < $hour || ($cron['hour'] == $hour && $nextminute === false)) {
            $nexttime['hour'] = -1;
            $nexttime['minute'] = -1;
        } else {
            $nexttime['hour'] = $cron['hour'];
            $nexttime['minute'] = $nextminute;
        }
    }
    if (empty($nexttime['minute'])) $nexttime['minute'] = 0;
    return $nexttime;
}

//一小时内下次执行的分钟
function cronnextminute($nextminutes, $minutenow)
{
    foreach ($nextminutes as $nextminute) {
        if ($nextminute > $minutenow) {
            return $nextminute;
        }
    }
    return false;
}