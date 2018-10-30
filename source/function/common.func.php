<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 common.func.php Mr.Kwok
 * Created Time:2018/9/20 9:46
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//写错误日志函数
function errorlog($type, $message, $halt = false)
{
    @$fp = fopen(M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . sgmdate(time(), "Ymd") . '.' . $type, 'a');
    @fwrite($fp, $message . PHP_EOL);
    @fclose($fp);
    if ($halt) {
        exit($message);
    }
}

//人性化时间
function sgmdate($timestamp, $dateformat = '', $format = 0)
{
    global $_MCONFIG, $_MGLOBAL;

    if (empty($dateformat)) {
        $dateformat = 'Y-m-d H:i:s';
    }
    if (empty($timestamp)) {
        $timestamp = $_MGLOBAL['timestamp'];
    }
    if ($format) {
        $time = $_MGLOBAL['timestamp'] - $timestamp;
        if ($time > 31536000) {
            $result = intval($time / 31536000) . '年前';
        } elseif ($time > 2592000) {
            $result = intval($time / 2592000) . '个月前';
        } elseif ($time > 604800) {
            $result = intval($time / 604800) . '周前';
        } elseif ($time > 259200) {
            $result = intval($time / 86400) . '天前';
        } elseif ($time > 172800) {
            $result = '前天';
        } elseif ($time > 86400) {
            $result = '昨天';
        } elseif ($time > 3600) {
            $result = intval($time / 3600) . '小时前';
        } elseif ($time > 180) {
            $result = intval($time / 60) . '分钟前';
        } else {
            $result = '刚刚';
        }
    } else {
        $result = gmdate($dateformat, $timestamp + $_MCONFIG['timeoffset'] * 3600);
    }
    return $result;
}

//跟SGMDATE函数对应
function sdate($dateformat, $timestamp, $format = 0)
{
    echo sgmdate($timestamp, $dateformat, $format);
}

//用户输入转义
function maddslashes($string)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = maddslashes($val);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

//获取用户IP
function get_client_ip()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}

//格式化模块param字符串
function parseparameter($param)
{
    $paramarr = array();
    $sarr = explode('/', $param);
    if (empty($sarr)) return $paramarr;
    for ($i = 0; $i < count($sarr); $i = $i + 2) {
        if (!empty($sarr[$i + 1])) $paramarr[$sarr[$i]] = str_replace(array('/', '\\'), '', rawurldecode(stripslashes($sarr[$i + 1])));
    }
    return $paramarr;
}

//过滤特殊字符 换行 tab等
function strfilter($str)
{
    $rex = array('`', ' ', '·', '~', '!', '！', '@', '#', '$', '￥', '%', '^', '……', '&', '*', '(', ')', '（', '）', '—', '+', '=', '|', "\\", '[', ']', '【', '】', '{', '}', ';', '；', ':', '：', '\'', "\'", '"', '“', '”', ',', '，', '<', '>', '《', '》', '.', '。', '/', '、', '?', '？', PHP_EOL, "\t");
    return str_replace($rex, '', trim(strip_tags($str)));
}

//显示内容不存在的错误信息
function notfoundmessage($message = '您所访问的内容不存在！', $url = MURL)
{
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    showmessage(2, $message, $url);
}

//显示信息
function showmessage($result, $message, $url_forward = '', $second = 3)
{
    //$result 1、成功;2、失败;3、提示;
    obclean();
    if ($result == 1) {
        $result = '<h1>Success</h1>';
        $message = '<i id="ok"></i>' . $message;
    } elseif ($result == 2) {
        $result = '<h1>Error</h1>';
        $message = '<i id="error"></i>' . $message;
    } else {
        $result = '<h1>Note</h1>';
        $message = '<i id="note"></i>' . $message;
    }
    if (!empty($url_forward)) {
        $second = $second * 1000;
        $message .= "<script>setTimeout(\"window.location.href ='$url_forward';\", $second);</script>";
    }
    include template('showmessage');
    ob_out();
    exit();
}

//载入模板缓存或者编译模板生成缓存再载入
function template($tplfilename, $fullpath = 0)
{
    global $_MCONFIG;
    if ($fullpath == 1) {
        $objfile = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . $tplfilename . '.php';
        $tplfile = M_ROOT . $tplfilename;
    } else {
        $dir = defined('IN_WAP') ? 'wap' : 'pc';//移动和PC分目录
        $tplfile = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $_MCONFIG['template'] . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $tplfilename . '.html.php';
        $objfile = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . $_MCONFIG['template'] . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $tplfilename . '.php';
    }
    $tpldir = dirname($objfile);
    if (!is_dir($tpldir)) {
        @mkdir($tpldir, 0777, true);
    }
    $tplrefresh = 1;
    if (file_exists($objfile)) {
        if (empty($_MCONFIG['tplrefresh'])) {
            //如果配置文件关闭刷新功能就不刷新
            $tplrefresh = 0;
        } elseif (@filemtime($tplfile) <= @filemtime($objfile)) {
            //如果发现模板没有修改就不刷新
            $tplrefresh = 0;
        }
    }
    if ($tplrefresh) {
        include_once(SOUREC_DIR . 'class' . DIRECTORY_SEPARATOR . 'template.class.php');
        $temp = new template;
        $temp->complie($tplfile, $objfile);//编译模板
    }
    return $objfile;
}

//清理缓冲区内容
function obclean()
{
    global $_MCONFIG;
    ob_end_clean();
    if ($_MCONFIG['gzipcompress'] && function_exists('ob_gzhandler')) {
        ob_start('ob_gzhandler');
    } else {
        ob_start();
    }
}

function ob_out()
{
    global $_MGLOBAL;
    $_MGLOBAL['content'] = ob_get_contents();
    obclean();//清除缓冲区内容
    echo $_MGLOBAL['content'];
}

//获得表前缀
function tname($name)
{
    global $_MCONFIG;
    $name = strfilter($name);
    $tablearr = array('adminsession', 'article', 'article_content', 'admin_action', 'attachments', 'actions', 'bad_words', 'categories', 'comments', 'crons', 'friendlinks', 'members', 'settings', 'tags', 'tags_map', 'usergroups', 'topic', 'recipes', 'recipes_baike', 'recipes_categories', 'recipes_catship', 'recipes_comment', 'recipes_step', 'recipes_imgshow', 'login_logs', 'action_logs', 'search');//防止跨表
    if (in_array($name, $tablearr)) {
        return $_MCONFIG['tablepre'] . $name;
    } else {
        showmessage(2, '您的数据表' . $name . '可能不存在，或者已被系统过滤！');
    }
}

//写文件
function writefile($filename, $writetext, $filemod = 'text', $openmod = 'w', $eixt = 1)
{
    if (!@$fp = fopen($filename, $openmod)) {
        if ($eixt) {
            exit('文件 :' . $filename . '未写入成功!');
        } else {
            return false;
        }
    } else {
        $text = '';
        if ($filemod == 'php') {
            $text = '<?php' . PHP_EOL . 'if(!defined(\'IN_MYCMS\')) exit(\'Access Denied\');' . PHP_EOL;
        }
        $text .= $writetext;
        flock($fp, 2);
        fwrite($fp, $text);
        fclose($fp);
        return true;
    }
}

//HTML生成的URL地址
function gethtmlfile($parray)
{
    $htmlarr = array();
    //如果分页小于2或者不存在
    if (empty($parray['page']) || $parray['page'] < 2) {
        unset($parray['page']);
    }
    //id=文章页 tagid=标签 catid=分类 name=单页面
    if (!empty($parray['id'])) {
        $id = $parray['id'];
    } elseif (!empty($parray['tagid'])) {
        $id = $parray['tagid'];
    } elseif (!empty($parray['catid'])) {
        $id = $parray['catid'];
    } elseif (!empty($parray['name'])) {
        $id = $parray['name'];
    } else {
        $id = 'index';
    }
    global $_MGLOBAL;
    if ($parray['action'] == 'index') {
        //首页
        $thedir = '';
        $htmlfilename = 'index';
    } elseif ($parray['action'] == 'article') {
        //文章页 需要判断分类前缀prehtml,文章自定义生成
        connectMysql();//连接数据库
        $news = $_MGLOBAL['db']->fetch_first('SELECT `catid`,`url` FROM ' . tname('article') . ' WHERE `id`=' . $id);
        if (!empty($news)) {
            $thedir = empty($_MGLOBAL['category'][$news['catid']]['htmlpath']) ? 'category' . $news['catid'] : strfilter($_MGLOBAL['category'][$news['catid']]['htmlpath']);
            $htmlfilename = strfilter($_MGLOBAL['category'][$news['catid']]['prehtml'] . $news['url'] . $id);
            if (!empty($parray['page'])) $htmlfilename .= '-' . $parray['page'];
        }
    } elseif ($parray['action'] == 'category') {
        if (!empty($_MGLOBAL['category'][$id])) {
            $thedir = empty($_MGLOBAL['category'][$id]['htmlpath']) ? 'category' . $id : strfilter($_MGLOBAL['category'][$id]['htmlpath']);
            $htmlfilename = 'index';
            if (!empty($parray['page'])) $htmlfilename = 'list-' . $parray['page'];
        }
    } else {
        $thedir = $parray['action'];//默认以动作为目录，以ID为页面（tag、topic等适用）
        $htmlfilename = $id;
        if (!empty($parray['page'])) $htmlfilename = $id . '-page-' . $parray['page'];
    }
    $makedir = H_DIR . $thedir;//兼容手机生成
    if (is_dir($makedir) || (!is_dir($makedir) && @mkdir($makedir, 0777, true))) {
        $htmlarr['path'] = $makedir . DIRECTORY_SEPARATOR . $htmlfilename . '.html';
        $htmlarr['url'] = H_URL . $thedir . '/' . $htmlfilename . '.html';
        if ($parray['action'] == 'index') {
            //首页强制生成到根目录
            $htmlarr['path'] = defined('IN_WAP') ? M_ROOT . 'wap' . DIRECTORY_SEPARATOR . 'index.html' : M_ROOT . 'index.html';
            $htmlarr['url'] = defined('IN_WAP') ? WAPURL : MURL;
        }
    }
    return $htmlarr;
}

//HTML生成
function ehtml($type)
{
    global $_MGLOBAL, $_MGET, $_MHTML;
    if ($type == 'get') {//检测页面是否生成如果生成在非强制模式下(php-1)跳转
        if (empty($_MGET['php']) && !empty($_MGLOBAL['htmlfile']['path']) && file_exists($_MGLOBAL['htmlfile']['path'])) {
            sheader(str_replace('index.html', '', $_MGLOBAL['htmlfile']['url']));
        }
    } else {
        if (!empty($_MGLOBAL['htmlfile']['path'])) {
            $content = str_replace('</head>', '<script>thismodified(' . $_MGLOBAL['timestamp'] . ',"' . $_MHTML['action'] . '","' . $_MHTML['id'] . '",' . $_MHTML['page'] . ');</script></head>', $_MGLOBAL['content']);
            writefile($_MGLOBAL['htmlfile']['path'], $content);
        }
    }
}

//301跳转
function sheader($url)
{
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $url");
    exit();
}

//生成缓存
function makecache($cacle_content, $_MHTML)
{
    global $_MCONFIG;
    if ($_MCONFIG['allowcache'] && $_MCONFIG['actions'][$_MHTML['action']]['cachetime'] > 0) {
        $cachedir = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $_MHTML['action'] . DIRECTORY_SEPARATOR;
        if ($_MHTML['id'] > 10000) {
            $cachedir .= intval($_MHTML['id'] / 1000) . DIRECTORY_SEPARATOR;
        }
        if (is_dir($cachedir) || mkdir($cachedir, 0777, true)) {
            writefile($cachedir . $_MHTML['id'] . '_' . $_MHTML['page'] . '.htm', $cacle_content);
        }
    }
}

//获取缓存
function getcache($_MHTML)
{
    global $_MGLOBAL, $_MCONFIG;
    if ($_MCONFIG['allowcache']) {
        $cachedir = M_ROOT . 'data' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $_MHTML['action'] . DIRECTORY_SEPARATOR;
        if ($_MHTML['id'] > 10000) {
            $cachedir .= intval($_MHTML['id'] / 1000) . DIRECTORY_SEPARATOR;
        }
        $cachefile = $cachedir . $_MHTML['id'] . '_' . $_MHTML['page'] . '.htm';
        if (file_exists($cachefile)) {
            if ($_MCONFIG['actions'][$_MHTML['action']]['cachetime'] > 0 && $_MGLOBAL['timestamp'] - filemtime($cachefile) < $_MCONFIG['actions'][$_MHTML['action']]['cachetime']) {
                echo file_get_contents($cachefile);
                exit();
            }
        }
    }
}

//格式化字符串
function format_string($string)
{
    return str_replace(array(PHP_EOL, '&nbsp;', "\r", "\n", "\t", '\'', '"', ' ', '　'), '', trim(strip_tags($string)));
}

//截取字符串
function cutstr($string, $length, $havedot = 0)
{
    global $_MCONFIG;
    //判断长度
    if (strlen($string) <= $length) {
        return $string;
    }
    $wordscut = '';
    if (strtolower($_MCONFIG['charset']) == 'utf-8') {
        //utf8编码
        $n = 0;
        $tn = 0;
        $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $wordscut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length - 3; $i++) {
            if (ord($string[$i]) > 127) {
                $wordscut .= $string[$i] . $string[$i + 1];
                $i++;
            } else {
                $wordscut .= $string[$i];
            }
        }
    }
    //省略号
    if ($havedot) {
        return $wordscut . '...';
    } else {
        return $wordscut;
    }
}

//获取URL
function geturl($pstring)
{
    global $_MGLOBAL, $_MCONFIG;
    //URL缓存
    if (!empty($_MGLOBAL['url_cache'][$pstring])) {
        return $_MGLOBAL['url_cache'][$pstring];
    }
    $temparr = explode('/', $pstring);
    //把网址处理成对应的数组
    $urlarr = $rewriturl = array();
    foreach ($temparr as $item => $value) {
        if ($item % 2 == 0) {
            $urlarr[$temparr[$item]] = $rewriturl[] = $temparr[$item + 1];
        }
    }
    $theurl = '';
    $theaction = empty($_MCONFIG['actions'][$urlarr['action']]) ? null : $_MCONFIG['actions'][$urlarr['action']];
    //url_model：0为动态、1为重写网址、2、为生成HTML
    $theaction['url_model'] = empty($theaction['url_model']) ? 0 : $theaction['url_model'];
    if ($theaction['url_model'] == 1) {
        if (empty($theaction['url_rewrite'])) {
            $theurl = '/' . implode("/", $rewriturl) . '.html';
        } else {
            $reurl = array('cid', 'tagid', 'bid', 'name', 'id', 'page');
            $theurl = $theaction['url_rewrite'];
            foreach ($reurl as $value) {
                if (!empty($urlarr[$value])) {
                    $theurl = str_replace('$' . $value, $urlarr[$value], $theurl);
                }
            }
        }
        $theurl = str_replace('$page', '', $theurl);
    } elseif ($theaction['url_model'] == 2 && $_MCONFIG['htmlmode']) {
        $qarr = parseparameter($pstring);
        //获取HTML路并检测是否生成
        $thehtmlurl = gethtmlfile($qarr);
        if (file_exists($thehtmlurl['path'])) {
            $theurl = str_replace('index.html', '', $thehtmlurl['url']);
        }
    }
    if (empty($theurl)) {
        $para = str_replace('/', '-', $pstring);
        $theurl = empty($para) ? '/' : '/main.php?' . $para;
    }
    $_MGLOBAL['url_cache'][$pstring] = $theurl;//url缓存后在函数顶部调用
    return $theurl;
}

//数据调用
function block($thekey, $param)
{
    global $_MBLOCK;
    $cachekey = smd5($thekey . $param);
    if (empty($_MBLOCK[$cachekey])) {
        $paramarr = parseparameter($param);
        include_once(SOUREC_DIR . 'function' . DIRECTORY_SEPARATOR . 'block.func.php');
        $block_func = 'block_' . $thekey;
        $_MBLOCK[$cachekey] = $block_func($paramarr);//php变量函数，函数名为上面的赋值
    }
    $_MBLOCK[$paramarr['cachename']] = $_MBLOCK[$cachekey];
}

function smd5($str)
{
    return substr(md5($str), 8, 16);
}

//将数组中相同的值去掉,同时将后面的键名也忽略掉
function sarray_unique($array)
{
    $newarray = array();
    if (!empty($array) && is_array($array)) {
        $array = array_unique($array);
        foreach ($array as $value) {
            $newarray[] = $value;
        }
    }
    return $newarray;
}

//生成form防伪码，每个用户和区域时间不同，生成不同的码
function formhash()
{
    global $_MGLOBAL, $_MCONFIG;
    if (empty($_MGLOBAL['formhash'])) {
        $_MGLOBAL['formhash'] = smd5(substr($_MGLOBAL['timestamp'], 0, -5) . '|' . $_MGLOBAL['uid'] . '|' . md5($_MCONFIG['sitekey']) . '|' . MURL);
    }
    return $_MGLOBAL['formhash'];
}