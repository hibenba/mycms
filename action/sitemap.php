<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 sitemap.php Mr.Kwok
 * Created Time:2018/9/20 16:54
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
echo '<?xml version="1.0" encoding="utf-8"?><sitemapindex>';
$sitemapfile = M_ROOT . 'data/sitemap/url.xml';
$str = '<?xml version="1.0" encoding="utf-8"?><urlset><url><loc>' . MURL . '/</loc><lastmod>' . sgmdate($_MGLOBAL['timestamp']) . '</lastmod><changefreq>daily</changefreq><priority>1</priority></url>';
foreach ($thecat as $cat) {
    $url = MURL . geturl('action/category/catid/' . $cat['catid']);
    $str .= '<url><loc>' . $url . '</loc><lastmod>' . sgmdate(time(), 'Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>0.9</priority></url>';
}
connectMysql();//连接数据库
$query = $_MGLOBAL['db']->query('SELECT `id`,`lastpost` FROM ' . tname('article') . ' WHERE `folder`=0');
while ($article = $_MGLOBAL['db']->fetch_array($query)) {
    $url = MURL . geturl('action/article/id/' . $article['id']);
    $str .= '<url><loc>' . $url . '</loc><lastmod>' . sgmdate($article['lastpost'], 'Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>';
}
$str .= '</urlset>';
writefile($sitemapfile, $str);
echo '<sitemap><loc>' . MURL . '/data/sitemap/url.xml</loc><lastmod>' . sgmdate(time(), 'Y-m-d') . '</lastmod></sitemap></sitemapindex>';
//神马Pattern
$sitemapfile = M_ROOT . 'data/sitemap/sm_pattern.xml';
$pattern = '<?xml version="1.0" encoding="UTF-8"?><urlset><url><loc>' . MURL . '/</loc><data><display><pc_url_pattern>' . MURL . '/(.*)</pc_url_pattern><xhtml_url_pattern >' . WAPURL . '/${1}</xhtml_url_pattern><html5_url_pattern >' . WAPURL . '/${1}</html5_url_pattern></display></data></url></urlset>';
writefile($sitemapfile, $pattern);
//百度自动提交网址
if (!empty($_MCONFIG['baidusendurl'])) {
    $urls = array();
    foreach ($thecat as $cat) {
        $urls[] = MURL . geturl('action/category/catid/' . $cat['catid']);
        $urls[] = WAPURL . geturl('action/category/catid/' . $cat['catid']);
    }
    $query = $_MGLOBAL['db']->query('SELECT `id` FROM ' . tname('article') . ' WHERE `folder`=0 ORDER BY `id` DESC LIMIT 20');
    while ($ver = $_MGLOBAL['db']->fetch_array($query)) {
        $urls[] = MURL . geturl('action/article/id/' . $ver['id']);
        $urls[] = WAPURL . geturl('action/article/id/' . $ver['id']);
    }
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $_MCONFIG['baidusendurl'],
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => implode("\n", $urls),
        CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    errorlog('baidu', str_replace('}', ',"time":"' . sgmdate(time(), 'Y-m-d H:i:s') . '"}', $result), 1);
}