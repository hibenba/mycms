<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 template.class.php Mr.Kwok
 * Created Time:2018/9/20 10:15
 * 读模板页进行替换后写入到cache页里
 * param string $tplfile ：模板文件名
 * param string $objfile ：cache文件名
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}

class template
{
    var $var_regexp = "\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*";
    var $vtag_regexp = "\<\?=(\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)\?\>";
    var $const_regexp = "\{([\w]+)\}";

    //编译模板文件
    function complie($tplfile, $objfile)
    {
        //读取模板文件
        if (!@$fp = fopen($tplfile, 'r')) {
            exit('模板文件 :' . $tplfile . '无法读取,可能不存在,请检查路径!');
        }
        $template = fread($fp, filesize($tplfile));
        fclose($fp);
        $template = str_replace('<?php exit()?>', '', $template);
        //开始解析模板文件内容

        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);//删除注释只留{}
        $template = preg_replace("/\{($this->var_regexp)\}/", "<?=\\1?>", $template);//替换带{}的变量
        $template = preg_replace("/\{($this->const_regexp)\}/", "<?=\\1?>", $template);//解析变量
        $template = preg_replace("/(?<!\<\?\=|\\\\)$this->var_regexp/", "<?=\\0?>", $template);//解析带＝的变量
        $template = preg_replace_callback("/\<\?=(\@?\\\$[a-zA-Z_]\w*)((\[[\\$\[\]\w]+\])+)\?\>/is", array($this, 'getarray'), $template);//数组变量加上单引号
        $template = preg_replace_callback("/\{eval (.*?)\}/is", array($this, 'geteval'), $template);//模板自定义变量解析
        $template = preg_replace_callback("/\{for (.*?)\}/is", array($this, 'getfor'), $template);//支持for循环
        $template = preg_replace_callback("/\{elseif\s+(.+?)\}/is", array($this, 'getelseif'), $template);//解析elseif
        for ($i = 0; $i < 2; $i++) {
            $template = preg_replace_callback("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/is", array($this, 'getloopsection_have'), $template);//解析带索引的数组
            $template = preg_replace_callback("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/is", array($this, 'getloopsection_no'), $template);//解析无索引数组
        }
        $template = preg_replace_callback("/\{if\s+(.+?)\}/is", array($this, 'getif'), $template);//解析if
        $template = preg_replace("/\{template\s+(\w+?)\}/is", "<? include template('\\1');?>", $template);//解析模板函数
        $template = preg_replace_callback("/\{template\s+(.+?)\}/is", array($this, 'gettemplate'), $template);//解析模板函数
        $template = preg_replace_callback('/[\n\r\t]*\{block\s+name="(.+?)"\s+parameter="(.+?)"\}[\n\r\t]*/is', array($this, 'getblock'), $template);//替换调用模块数据
        $template = preg_replace_callback('/\#(action\/.+?)\#/i', array($this, 'addgeturl'), $template);//链接格式化
        $template = preg_replace_callback("/\#date\((.+?)\)\#/is", array($this, 'getsdata'), $template);//时间格式化
        $template = preg_replace("/\{else\}/is", "<? } else { ?>", $template);//解析else
        $template = preg_replace("/\{\/if\}/is", "<? } ?>", $template);//解析结束if
        $template = preg_replace("/\{\/for\}/is", "<? } ?>", $template);//解析结束for
        $template = preg_replace("/$this->const_regexp/", "<?=\\1?>", $template);//再次替换变量
        $template = "<? if(!defined('IN_MYCMS')) exit('Access Denied');?>\r\n$template";//加安全验证
        $template = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $template);//数组变量加单引号
        $template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);//加上PHP
        $template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?php echo \\1;?>", $template);//支持高版本php
        $template = trim($template);//移除字符串两侧的空白字符
        //写入缓存文件
        if (!empty($template)) {
            $needwrite = false;
            if (@unlink($objfile)) {
                writefile($objfile . '.tmp', $template, 'text', 'w', 0);
                if (@rename($objfile . '.tmp', $objfile)) {
                    $needwrite = false;
                } else {
                    $needwrite = true;
                }
            } else {
                $needwrite = true;
            }
            //再次写入
            if ($needwrite) writefile($objfile, $template, 'text', 'w', 0);
        }
    }

    //连接格式化回调
    function addgeturl($matches)
    {
        $str = '<?php echo geturl("' . $this->stripvtag($matches[1]) . '")?>';
        return preg_replace("/($this->var_regexp)/", "{\\1}", $str);//给数组加上大括号防止解析错误
    }

    //模块格式化回调
    function getblock($matches)
    {
        $str = '<?php block("' . $matches[1] . '", "' . $this->stripvtag($matches[2]) . '");?>';
        return preg_replace("/($this->var_regexp)/", "{\\1}", $str);//给数组加上大括号防止解析错误
    }

    //时间格式化回调
    function getsdata($matches)
    {
        return $this->stripvtag('<?php sdate(' . $matches[1] . ')?>');
    }

    function getarray($matches)
    {
        return $this->arrayindex($matches[1], $matches[2]);
    }

    function geteval($matches)
    {
        return $this->stripvtag('<? ' . $matches[1] . '?>');
    }

    function arrayindex($name, $items)
    {
        $items = preg_replace("/\[([a-zA-Z_]\w*)\]/is", "['\\1']", $items);
        return "<?=$name$items?>";
    }

    function stripvtag($s)
    {
        return preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $s));
    }

    function getfor($matches)
    {
        return $this->stripvtag('<? for(' . $matches[1] . ') {?>');
    }

    function getelseif($matches)
    {
        return $this->stripvtag('<? } elseif(' . $matches[1] . ') { ?>');
    }

    function getloopsection_have($matches)
    {
        return $this->loopsection($matches[1], $matches[2], $matches[3], $matches[4]);
    }

    function getloopsection_no($matches)
    {
        return $this->loopsection($matches[1], '', $matches[2], $matches[3]);
    }

    function getif($matches)
    {
        return $this->stripvtag('<? if(' . $matches[1] . ') { ?>');
    }

    function gettemplate($matches)
    {
        return $this->stripvtag('<? include template(' . $matches[1] . '); ?>');
    }

    function loopsection($arr, $k, $v, $statement)
    {
        $arr = $this->stripvtag($arr);
        $k = $this->stripvtag($k);
        $v = $this->stripvtag($v);
        $statement = str_replace("\\\"", '"', $statement);
        return $k ? "<? foreach((array)$arr as $k => $v) {?>$statement<? }?>" : "<? foreach((array)$arr as $v) {?>$statement<? } ?>";
    }
}