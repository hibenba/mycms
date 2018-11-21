<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mycms_database.php Mr.Kwok
 * Created Time:2018/10/31 9:47
 */
if (!defined('IN_MYCMS') || $_MGLOBAL['member']['groupid'] != 1) {
    exit('Access Denied');
}
ini_set('memory_limit', -1);
if (!empty($_MGET['optmize'])) {
    $_MGLOBAL['db']->query("OPTIMIZE TABLE `" . $_MGET['optmize'] . "`");
    sheader($theurl);//回到原页面
}
if (!empty($_MGET['repair'])) {
    $_MGLOBAL['db']->query("REPAIR TABLE `" . $_MGET['repair'] . "`");
    sheader($theurl);//回到原页面
}
$tablearr = array();
$query = $_MGLOBAL['db']->query("SHOW TABLE STATUS");
while ($table = $_MGLOBAL['db']->fetch_array($query)) {
    $table['size_k'] = round($table['Data_length'] / 1024, 2);
    if ($table['size_k'] > 1024) {
        $table['size_k'] = round($table['size_k'] / 1024, 2) . 'M';
    } else {
        $table['size_k'] = $table['size_k'] . 'K';
    }
    $table['free_size'] = round($table['Data_free'] / 1024, 2);
    if ($table['free_size'] > 1024) {
        $table['free_size'] = round($table['free_size'] / 1024, 2) . 'M';
    } elseif ($table['free_size'] > 0) {
        $table['free_size'] = $table['free_size'] . 'K';
    } else {
        $table['free_size'] = '-';
    }
    $tablearr[$table['Name']] = $table;
}
$datedir = DATA_DIR .'Mysql_backup' . DIRECTORY_SEPARATOR . sgmdate($_MGLOBAL['timestamp'], "Ymd") . DIRECTORY_SEPARATOR;
if (!empty($_MGET['backup'])) {
    //单个备份
    if (is_dir($datedir) || (!is_dir($datedir) && @mkdir($datedir, 0777, true))) {
        if (!empty($tablearr[$_MGET['backup']])) {
            $datefilename = $datedir . $_MGET['backup'] . '.sql';
            $sqlcon = backmysql($_MGET['backup']);
            writefile($datefilename, $sqlcon);
            showmessage(1, $_MGET['backup'] . '已成功备份！', $theurl);
        } else {
            showmessage(2, '数据表备份失败，当前表不存在请检查输入！', $theurl);
        }
    } else {
        showmessage(2, '文件写入失败，可能当前目录权限不可写，请检查！', $theurl);
    }
}
if (submitcheck('bakdatabasesubmit')) {
    //批量备份
    if (empty($_POST['tabarr'])) {
        showmessage(3, '请选择你要备份的数据表!', $theurl);
    }
    if (is_dir($datedir) || (!is_dir($datedir) && @mkdir($datedir, 0777, true))) {
        if (is_array($_POST['tabarr'])) {
            foreach ($_POST['tabarr'] as $value) {
                if (!empty($tablearr[$value])) {
                    $datefilename = $datedir . $value . '.sql';
                    $sqlcon = backmysql($value);
                    writefile($datefilename, $sqlcon);
                }
            }
            showmessage(1, '备份成功，请查看./data/Mysql_backup目录！', $theurl);
        }
    } else {
        showmessage(2, '文件写入失败，可能当前目录权限不可写，请检查！', $theurl);
    }
    sheader($theurl);
}
function backmysql($table)
{
    global $_MGLOBAL;
    $tabledump = '-- MyCms SQL Dump
-- version MyCms ' . M_VER . '(' . M_RELEASE . ')
-- 生成日期: ' . sgmdate($_MGLOBAL['timestamp']) . '
-- MYSQL版本: ' . $_MGLOBAL['db']->result($_MGLOBAL['db']->query("SELECT VERSION()"), 0) . '
-- PHP 版本: ' . PHP_VERSION . '
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";' . PHP_EOL;
    $tabledump .= 'DROP TABLE IF EXISTS `' . $table . '`;' . PHP_EOL;//如果表存在就删除，用于恢复时使用
    $createtable = $_MGLOBAL['db']->fetch_first('SHOW CREATE TABLE ' . $table);
    $tabledump .= $createtable['Create Table'] . ';' . PHP_EOL;//表结构
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . $table);
    while ($value = $_MGLOBAL['db']->fetch_array($query)) {
        $sqlstr = "INSERT INTO `" . $table . "` VALUES (";
        foreach ($value as $str) {
            $sqlstr .= "'" . $str . "', ";
        }
        $tabledump .= substr($sqlstr, 0, strlen($sqlstr) - 2) . ');' . PHP_EOL;//去掉最后的,和空格并结束
    }
    return $tabledump;
}

include_once template(TPLDIR . 'database.htm', 1);