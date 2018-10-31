<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 admin.func.php Mr.Kwok
 * Created Time:2018/9/26 9:22
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//检查是否操作创始人
function ckfounder($uid)
{
    global $_MCONFIG;
    $founders = empty($_MCONFIG['founder']) ? array() : explode(',', $_MCONFIG['founder']);
    return in_array($uid, $founders);
}

//更新admin_action
function updateadmin_action()
{
    global $_MGLOBAL;
    $_MGLOBAL['admin_action'] = array();
    $query = $_MGLOBAL['db']->query("SELECT `id`, `upid`, `action`, `name`, `description`, `displayorder`, `type` FROM " . tname('admin_action') . " ORDER BY  `displayorder` ASC ");
    while ($action = $_MGLOBAL['db']->fetch_array($query)) {
        $_MGLOBAL['admin_action'][$action['action']] = $action;
    }
    $cachetext = '$_MGLOBAL[\'admin_action\']=json_decode(\'' . json_encode($_MGLOBAL['admin_action'], JSON_UNESCAPED_UNICODE) . '\',true);';
    writefile(M_ROOT . 'data/system/admin_action.cache.php', $cachetext, 'php');
}

//调试信息,显示进程处理时间
function debuginfo()
{
    global $_MGLOBAL, $_MCONFIG;
    $mtime = explode(' ', microtime());
    $totaltime = number_format(($mtime[1] + $mtime[0] - $_MGLOBAL['mycms_starttime']), 6);
    $memory_usage = ceil(memory_get_peak_usage(true) / 1024);
    if ($memory_usage > 1024) {
        $memory_usage = round($memory_usage / 1024, 2) . 'MB';
    } else {
        $memory_usage .= 'KB';
    }
    echo '执行时间共花了' . $totaltime . '秒,共进行了' . $_MGLOBAL['db']->querynum . '个数据库查询！内存峰值：' . $memory_usage . ($_MCONFIG['gzipcompress'] ? ', Gzip 已开启' : NULL);
}

//格式化大小函数,根据字节数自动显示成'KB','MB'等等
function formatsize($size, $prec = 3)
{
    $size = round(abs($size));
    $units = array(0 => " B ", 1 => " KB", 2 => " MB", 3 => " GB", 4 => " TB");
    if ($size == 0) return str_repeat(" ", $prec) . "0$units[0]";
    $unit = min(4, floor(log($size) / log(2) / 10));
    $size = $size * pow(2, -10 * $unit);
    $digi = $prec - 1 - floor(log($size) / log(10));
    $size = round($size * pow(10, $digi)) * pow(10, -$digi);
    return $size . $units[$unit];
}

//彻底删除文章
function delarticle($id)
{
    global $_MGLOBAL;
    $arr = gethtmlfile(parseparameter('action/article/id/' . $id));
    @unlink(str_replace('./', '', $arr['path']));//HTML删除
    @define('IN_WAP', TRUE);
    $arr = gethtmlfile(parseparameter('action/article/id/' . $id));
    @unlink(str_replace('./', '', $arr['path']));//WAP删除
    $_MGLOBAL['db']->deletetable('article', array('id' => $id));//删除标题
    $_MGLOBAL['db']->deletetable('article_content', array('id' => $id));//删除内容
    $_MGLOBAL['db']->deletetable('tags_map', array('articleid' => $id));//删除tag关联
    $query = $_MGLOBAL['db']->query('SELECT url FROM ' . tname('attachments') . ' WHERE id=' . $id);
    while ($attachment = $_MGLOBAL['db']->fetch_array($query)) {
        @unlink(M_ROOT . $attachment['url']);//附件删除
    }
    $_MGLOBAL['db']->deletetable('attachments', array('id' => $id));////删除附件
}

//TAG入库与关联
function tags_insert($tags, $id)
{
    global $_MGLOBAL;
    if (is_numeric($id)) { //当ID为空的时候只做增加
        $_MGLOBAL['db']->deletetable('tags_map', array('articleid' => $id));//删除tag关联
    }
    $tags = str_replace(array('，', ' ', '　', '   '), ',', trim($tags));
    if (!empty($tags)) {
        $tags = explode(',', $tags);
        foreach ($tags as $tag) {
            $tag = strfilter($tag);
            $newtagid = $_MGLOBAL['db']->fetch_first('SELECT tagid,close FROM ' . tname('tags') . ' WHERE tagname=\'' . $tag . '\'');
            if (empty($newtagid)) {
                $tagsqlarr = array(
                    'tagid' => '',
                    'tagname' => $tag,
                    'dateline' => $_MGLOBAL['timestamp'],
                    'close' => 0
                );
                $tagid = $_MGLOBAL['db']->inserttable('tags', $tagsqlarr, 1);
                $newtagid['close'] = 0;
            } else {
                $tagid = $newtagid['tagid'];
            }
            if ($tagid && $newtagid['close'] == 0 && is_numeric($id)) {
                $tagmap = $_MGLOBAL['db']->fetch_first('SELECT * FROM ' . tname('tags_map') . ' WHERE tagid=\'' . $tagid . '\' and articleid=\'' . $id . '\'');
                if (empty($tagmap['tagid'])) {
                    $_MGLOBAL['db']->inserttable('tags_map', array('tagid' => $tagid, 'articleid' => $id));
                }
            }
        }
    }
}

//返回hash码
function random($length, $numeric = 0)
{
    PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
    $seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

//返回标准零时区时间戳
function sstrtotime($timestamp)
{
    global $_MCONFIG;
    $timestamp = trim($timestamp);    //过滤首尾空格
    if (empty($timestamp)) return 0;
    $hour = $minute = $second = $month = $day = $year = 0;
    $exparr = $timearr = array();
    if (strpos($timestamp, ' ') !== false && strpos($timestamp, '-') !== false) {
        $timearr = explode(' ', $timestamp);
        $exparr = explode('-', $timearr[0]);
        $year = empty($exparr[0]) ? 0 : intval($exparr[0]);
        $month = empty($exparr[1]) ? 0 : intval($exparr[1]);
        $day = empty($exparr[2]) ? 0 : intval($exparr[2]);
        $exparr = explode(':', $timearr[1]);
        $hour = empty($exparr[0]) ? 0 : intval($exparr[0]);
        $minute = empty($exparr[1]) ? 0 : intval($exparr[1]);
        $second = empty($exparr[2]) ? 0 : intval($exparr[2]);
    } elseif (strpos($timestamp, '-') !== false && strpos($timestamp, ' ') === false) {
        $exparr = explode('-', $timestamp);
        $year = empty($exparr[0]) ? 0 : intval($exparr[0]);
        $month = empty($exparr[1]) ? 0 : intval($exparr[1]);
        $day = empty($exparr[2]) ? 0 : intval($exparr[2]);
    } elseif (!strpos($timestamp, '-') === false && strpos($timestamp, ' ') !== false) {
        $exparr = explode(':', $timestamp);
        $hour = empty($exparr[0]) ? 0 : intval($exparr[0]);
        $minute = empty($exparr[1]) ? 0 : intval($exparr[1]);
        $second = empty($exparr[2]) ? 0 : intval($exparr[2]);
    } else {
        return 0;
    }
    return gmmktime($hour, $minute, $second, $month, $day, $year) - $_MCONFIG['timeoffset'] * 3600;
}

//设置附件保存的方式
function getattachdir()
{
    global $_MCONFIG, $_MGLOBAL;
    switch ($_MCONFIG['attachmentdirtype']) {
        case 'year':
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'Y');
            break;
        case 'month':
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'Y');
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'm');
            break;
        case 'day':
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'Y');
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'm');
            $dirpatharr[] = sgmdate($_MGLOBAL['timestamp'], 'd');
            break;
        case 'md5':
            $md5string = md5($_MGLOBAL['uid'] . '-' . $_MGLOBAL['timestamp'] . '-' . $_MGLOBAL['_num']);
            $dirpatharr[] = substr($md5string, 0, 1);
            $dirpatharr[] = substr($md5string, 1, 1);
            break;
        default:
            break;
    }
    $dirs = A_DIR;
    $subarr = array();
    foreach ($dirpatharr as $value) {
        $dirs .= DIRECTORY_SEPARATOR . $value;
        if (smkdir($dirs)) {
            $subarr[] = $value;
        } else {
            break;
        }
    }
    return A_DIR . implode(DIRECTORY_SEPARATOR, $subarr) . DIRECTORY_SEPARATOR;
}

function smkdir($dirname, $ismkindex = 1)
{
    $mkdir = false;
    if (!is_dir($dirname)) {
        if (@mkdir($dirname, 0755, true)) {
            if ($ismkindex) {
                @fclose(@fopen($dirname . '/index.htm', 'w'));
            }
            $mkdir = true;
        }
    } else {
        $mkdir = true;
    }
    return $mkdir;
}

//对于文章内容不需要的HTML代码进行过滤
function filters_outcontent($str)
{
    $pattern = '/\<(html|body|input|script|form|iframe|textarea|\/textarea)/is';
    $str = preg_replace($pattern, "&lt;\\1", $str);
    return $str;
}

//读取指定目录下的文件
function sreaddir($dir, $ext = '')
{
    $filearr = array();
    if (is_dir($dir)) {
        $filedir = dir($dir);
        while (false !== ($entry = $filedir->read())) {
            if (!empty($ext)) {
                if (strtolower(fileext($entry)) == strtolower($ext)) {
                    $filearr[$entry] = $entry;
                }
            } else {
                if ($entry != '.' && $entry != '..') {
                    $filearr[$entry] = $entry;
                }
            }
        }
        $filedir->close();
    }
    return $filearr;
}

//更新站点分类
function updatecategorycache()
{
    global $_MGLOBAL;
    $carr = array();
    $query = $_MGLOBAL['db']->query('SELECT * FROM ' . tname('categories') . ' ORDER BY displayorder');
    while ($cat = $_MGLOBAL['db']->fetch_array($query)) {
        $carr[$cat['catid']] = $cat;
    }
    $cachetext = '$_MGLOBAL[\'category\']=json_decode(\'' . json_encode($carr, JSON_UNESCAPED_UNICODE) . '\',true);';
    writefile(M_ROOT . 'data/system/category.cache.php', $cachetext, 'php');
}

//获取文件名后缀
function fileext($filename)
{
    return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}

function chickpath($htmlpath, $tablename, $field, $id)
{
    //检查设置的目录名字是否重复，如果重复返回目录+ID。
    global $_MGLOBAL;
    $id = empty($id) ? rand(1, 999) : intval($id);
    $value = $_MGLOBAL['db']->fetch_first('SELECT ' . $field . ' FROM ' . tname($tablename) . ' where `' . $field . '`=\'' . $htmlpath . '\'');
    if (!empty($value)) {
        return $htmlpath . $id;
    } else {
        return $htmlpath;
    }
}

//替换html里的特殊字符
function shtmlspecialchars($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = shtmlspecialchars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
            str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

//删除一个目录下所有的文件
function removeDir($dirname, $rmdir = false)
{
    foreach (glob($dirname . DIRECTORY_SEPARATOR . '*') as $file) {
        echo $file;
        if (is_dir($file)) {
            removeDir($file);
        } else {
            unlink($file);
        }
    }
    if ($rmdir) {
        rmdir($dirname);
    }
}
