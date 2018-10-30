<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 list.func.php Mr.Kwok
 * Created Time:2018/9/20 12:37
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
//分页函数（总数、显示数、当前页、链接集合
function multipage($num, $perpage, $curpage, $urlarr)
{
    if (($curpage - 1) * $perpage > $num) showmessage(2, '出错了，您要查看页数不存在');
    if ($num > $perpage) {
        $page = 10;
        $offset = 2;
        $pages = @ceil($num / $perpage);
        if ($page > $pages) {
            $from = 1;
            $to = $pages;
        } else {
            $from = $curpage - $offset;
            $to = $curpage + $page - $offset - 1;
            if ($from < 1) {
                $to = $curpage + 1 - $from;
                $from = 1;
                if (($to - $from) < $page && ($to - $from) < $pages) {
                    $to = $page;
                }
            } elseif ($to > $pages) {
                $from = $curpage - $pages + $to;
                $to = $pages;
                if (($to - $from) < $page && ($to - $from) < $pages) {
                    $from = $pages - $page + 1;
                }
            }
        }
        $urlarr['page'] = 1;
        $url = geturl(arraytostring($urlarr));
        $urlarr['page'] = $curpage - 1;
        $url2 = geturl(arraytostring($urlarr));
        $multipage = '<div class="pages"><div>' . ($curpage - $offset > 1 && $pages > $page ? '<a href="' . $url . '">1...</a>' : '') . ($curpage > 1 ? '<a class="prev" href="' . $url2 . '">上一页</a>' : '');
        for ($i = $from; $i <= $to; $i++) {
            $urlarr['page'] = $i;
            if ($urlarr['page'] == 1) unset($urlarr['page']);
            $url = geturl(arraytostring($urlarr));
            $multipage .= $i == $curpage ? '<strong>' . $i . '</strong>' : '<a href="' . $url . '">' . $i . '</a>';
        }
        $urlarr['page'] = $curpage + 1;
        if ($urlarr['page'] == 1) unset($urlarr['page']);
        $url = geturl(arraytostring($urlarr));
        $urlarr['page'] = $pages;
        if ($urlarr['page'] == 1) unset($urlarr['page']);
        $url2 = geturl(arraytostring($urlarr));
        $multipage .= ($to < $pages ? '<a href="' . $url2 . '" target="_self">...' . $pages . '</a>' : '') . ($curpage < $pages ? '<a class="next" href="' . $url . '">下一页</a>' : '');
        $multipage .= '</div></div>';
        return $multipage;
    }
}

//数组转为字符串
function arraytostring($array, $dot = '/')
{
    $result = $comma = '';
    foreach ($array as $key => $value) {
        $value = trim($value);
        if ($value != '') {
            $result .= $comma . $key . $dot . rawurlencode($value);
            $comma = $dot;
        }
    }
    return $result;
}