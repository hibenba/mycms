<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 block.func.php Mr.Kwok
 * Created Time:2018/9/20 11:10
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//范围查询条件SQL处理
function getscopequery($pre, $var, $paramarr, $isdate = 0)
{
    global $_MGLOBAL;
    $wheresql = '';
    if (!empty($pre)) $pre = $pre . '.';
    if (!empty($paramarr[$var])) {
        if ($isdate) {
            $paramarr[$var] = intval($paramarr[$var]);
            if ($paramarr[$var]) $wheresql = $pre . $var . '>=' . ($_MGLOBAL['timestamp'] - $paramarr[$var]);
        } else {
            $tarr = explode(',', $paramarr[$var]);
            if (count($tarr) == 2) {
                $tarr[0] = intval(trim($tarr[0]));
                $tarr[1] = intval(trim($tarr[1]));
                if ($tarr[1] > $tarr[0]) {
                    $wheresql = '(' . $pre . $var . '>=' . $tarr[0] . ' AND ' . $pre . $var . '<=' . $tarr[1] . ')';
                }
            }
        }
    }
    return $wheresql;
}

//分类数据调用
function block_category($paramarr)
{
    global $_MGLOBAL;
    $sql = $theblockarr = array();
    $sql['select'] = 'SELECT c.*';
    $sql['from'] = 'FROM ' . tname('categories') . ' c';
    $wherearr = array();
    //where
    if (!empty($paramarr['catid'])) {
        $paramarr['catid'] = getdotstring($paramarr['catid'], 'int');
        if ($paramarr['catid']) $wherearr[] = 'c.catid IN (' . $paramarr['catid'] . ')';
    } else {
        if (!empty($paramarr['isroot'])) {
            $paramarr['isroot'] = intval($paramarr['isroot']);
            if ($paramarr['isroot'] == 1) {
                $wherearr[] = 'c.upid = 0';
            } elseif ($paramarr['isroot'] == 2) {
                if (!empty($paramarr['upid'])) {
                    $paramarr['upid'] = getdotstring($paramarr['upid'], 'int');
                    if ($paramarr['upid']) $wherearr[] = 'c.upid IN (' . $paramarr['upid'] . ')';
                } else {
                    $wherearr[] = 'c.upid > 0';
                }
            }
        } else {
            if (!empty($paramarr['upid'])) {
                $paramarr['upid'] = getdotstring($paramarr['upid'], 'int');
                if ($paramarr['upid']) $wherearr[] = 'c.upid IN (' . $paramarr['upid'] . ')';
            }
        }
    }
    $sql['where'] = '';
    if (!empty($wherearr)) $sql['where'] = 'WHERE ' . implode(' AND ', $wherearr);

    //order
    if (!empty($paramarr['order'])) {
        $sql['order'] = 'ORDER BY ' . $paramarr['order'];
    }
    if (empty($paramarr['limit'])) {
        $sql['limit'] = 'LIMIT 0,1';
    } else {
        $paramarr['limit'] = getdotstring($paramarr['limit'], 'int', true, array(), 1, false);
        if ($paramarr['limit']) {
            $sql['limit'] = 'LIMIT ' . $paramarr['limit'];
        } else {
            $sql['limit'] = 'LIMIT 0,1';
        }
    }
    //生成查询串
    $sqlstring = implode(' ', $sql);
    //查询
    $query = $_MGLOBAL['db']->query($sqlstring);
    while ($value = $_MGLOBAL['db']->fetch_array($query)) {
        //链接
        $value['url'] = geturl('action/category/catid/' . $value['catid']);
        $theblockarr[] = $value;

    }
    return $theblockarr;
}

function block_article($paramarr)
{
    global $_MGLOBAL;
    $sql = array();
    $sql['select'] = 'SELECT *';
    $sql['from'] = 'FROM ' . tname('article') . ' as i';
    if (!empty($paramarr['showdetail'])) {
        $sql['join'] = ' LEFT JOIN ' . tname('article_content') . ' as ii ON (i.id=ii.id)';
    }
    $wherearr = array('i.folder=0');
    if (!empty($paramarr['id'])) {
        $paramarr['id'] = getdotstring($paramarr['id'], 'int');
        if ($paramarr['id']) $wherearr[] = 'i.id IN (' . $paramarr['id'] . ')';
    } else {
        //作者
        if (!empty($paramarr['uid'])) {
            $paramarr['uid'] = getdotstring($paramarr['uid'], 'int');
            if ($paramarr['uid']) $wherearr[] = 'i.uid IN (' . $paramarr['uid'] . ')';
        }
        //分类
        if (!empty($paramarr['catid'])) {
            $paramarr['catid'] = getdotstring($paramarr['catid'], 'int');
            if ($paramarr['catid']) $wherearr[] = 'i.catid IN (' . $paramarr['catid'] . ')';
        }
        //文章等级1-5
        if (!empty($paramarr['grade'])) {
            $paramarr['grade'] = getdotstring($paramarr['grade'], 'int');
            if (!empty($paramarr['grade'])) $wherearr[] = 'i.grade IN (' . $paramarr['grade'] . ')';
        }
        if (!empty($paramarr['digest'])) {
            $paramarr['digest'] = getdotstring($paramarr['digest'], 'int');
            if ($paramarr['digest']) $wherearr[] = 'i.digest IN (' . $paramarr['digest'] . ')';
        }
        if (!empty($paramarr['top'])) {
            $paramarr['top'] = getdotstring($paramarr['top'], 'int');
            if ($paramarr['top']) $wherearr[] = 'i.top IN (' . $paramarr['top'] . ')';
        }
        if (!empty($paramarr['dateline'])) {
            $paramarr['dateline'] = intval($paramarr['dateline']);
            if ($paramarr['dateline']) $wherearr[] = 'i.dateline >= ' . ($_MGLOBAL['timestamp'] - $paramarr['dateline']);
        }
        //图片调用
        if (!empty($paramarr['cover'])) {
            $wherearr[] = 'i.cover != 0';
        }
        if (!empty($paramarr['lastpost'])) {
            $paramarr['lastpost'] = intval($paramarr['lastpost']);
            if ($paramarr['lastpost']) $wherearr[] = 'i.lastpost >= ' . ($_MGLOBAL['timestamp'] - $paramarr['lastpost']);
        }
        $scopequery = getscopequery('i', 'viewnum', $paramarr);
        if (!empty($scopequery)) $wherearr[] = $scopequery;
        $scopequery = getscopequery('i', 'replynum', $paramarr);
        if (!empty($scopequery)) $wherearr[] = $scopequery;
    }
    if (!empty($wherearr)) $sql['where'] = 'WHERE ' . implode(' AND ', $wherearr);
    //order
    if (!empty($paramarr['order'])) {
        $sql['order'] = 'ORDER BY ' . $paramarr['order'];
    }
    //limit
    if (empty($paramarr['limit'])) {
        $sql['limit'] = 'LIMIT 1';
    } else {
        $paramarr['limit'] = getdotstring($paramarr['limit'], 'int', true, array(), 1, false);
        if ($paramarr['limit']) {
            $sql['limit'] = 'LIMIT ' . $paramarr['limit'];
        } else {
            $sql['limit'] = 'LIMIT 1';
        }
    }
    $sqlstring = implode(' ', $sql);//合并查询语句
    //预处理
    if (empty($paramarr['subjectdot'])) $paramarr['subjectdot'] = 0;
    if (empty($paramarr['messagedot'])) $paramarr['messagedot'] = 0;
    if (!empty($paramarr['showcategory'])) {
        include(DATA_DIR .'system/category.cache.php');
    }
    $query = $_MGLOBAL['db']->query($sqlstring);
    $theblockarr = array();
    while ($value = $_MGLOBAL['db']->fetch_array($query)) {
        //处理
        $value['subjectall'] = $value['subject'];
        if (!empty($value['subject']) && !empty($paramarr['subjectlen'])) {
            $value['subject'] = cutstr($value['subject'], $paramarr['subjectlen'], $paramarr['subjectdot']);
        }
        //链接
        $value['url'] = geturl('action/article/id/' . $value['id']);
        //分类名
        if (!empty($_MGLOBAL['category'][$value['catid']])) $value['catname'] = $_MGLOBAL['category'][$value['catid']];
        //读取文章内容
        if (!empty($value['content']) && !empty($paramarr['messagelen'])) {
            $value['content'] = cutstr(str_replace(array(PHP_EOL, '&nbsp;', "\r", "\n", "\r", '\'', '"', ' ', '　'), '', trim(strip_tags(stripslashes($value['content'])))), $paramarr['messagelen'], $paramarr['messagedot']);
        }
        //图片调用
        if (!empty($paramarr['cover'])) {
            $tmpvar = $_MGLOBAL['db']->fetch_array($_MGLOBAL['db']->query('SELECT url FROM ' . tname('attachments') . '  WHERE aid = \'' . $value['cover'] . '\' and isimage=1 ORDER BY id LIMIT 1'));
            $value['images'] = $tmpvar['url'];
        }
        $theblockarr[$value['id']] = $value;
    }
    return $theblockarr;
}

function getdotstring($string, $vartype, $allownull = false, $varscope = array(), $sqlmode = 1, $unique = true)
{

    if (is_array($string)) {
        $stringarr = $string;
    } else {
        if (substr($string, 0, 1) == '$') {
            return $string;
        }
        $string = str_replace('，', ',', $string);
        $string = str_replace(' ', ',', $string);
        $stringarr = explode(',', $string);
    }
    $newarr = array();
    foreach ($stringarr as $value) {
        $value = trim($value);
        if ($vartype == 'int') {
            $value = intval($value);
        }
        if (!empty($varscope)) {
            if (in_array($value, $varscope)) {
                $newarr[] = $value;
            }
        } else {
            if ($allownull) {
                $newarr[] = $value;
            } else {
                if (!empty($value)) $newarr[] = $value;
            }
        }
    }
    if ($unique) $newarr = sarray_unique($newarr);

    if ($vartype == 'int') {
        $string = implode(',', $newarr);
    } else {
        if ($sqlmode) {
            $string = '\'' . implode('\',\'', $newarr) . '\'';
        } else {
            $string = implode(',', $newarr);
        }
    }
    return $string;
}