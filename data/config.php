<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 config.php Mr.Kwok
 * Created Time:2018/9/20 11:14
 * --------------- MyCMS 数据库设置开始 ---------------
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}
$_MCONFIG = array(
    'dbhost' => '127.0.0.1',                //MyCMS数据库服务器(一般为本地localhost)
    'dbuser' => 'root',                    //MyCMS数据库用户名
    'dbpw' => 'root',                      //MyCMS数据库密码
    'dbname' => 'mycms',                    //MyCMS数据库名
    'port' => 3306,                          //数据库连接端口
    'tablepre' => 'mycms_',                 //MyCMS表名前缀
    'pconnect' => 0,                        //MyCMS数据库持久连接 0=>关闭, 1=>打开后会加速php运行速度，但是会增加服务器压力并且容易锁死，生成HTML的情况下请设置关闭(0)
    'dbcharset' => 'utf8',                 //MyCMS数据库字符集
    'siteurl' => 'http://127.0.0.1',        //MyCMS程序URL访问地址。可以填写以 http:// 开头的完整URL，也可以填写相对URL。末尾不要加 /。
    'tplrefresh' => 1,                        //风格模板自动刷新开关。关闭后可提升一点性能但修改模板页面需要手工进入管理员后台=>>缓存更新 进行一下模板文件缓存清空，才能看到修改的效果。
    'charset' => 'utf-8',                  //网页强制编码时使用
//--------------- MyCMS 设置结束 | 以下是安全相关---------------
    'founder' => '1',                        //创始人 UID, 可以支持多个创始人，之间使用 “,” 分隔。部分管理功能只有创始人才可操作。
    'LocoySpider' => 'mycms123',            //火车头发布接口密码，必须修改为用户自己的密码
//--------------- COOKIE设置 ---------------
    'cookiepre' => 'MyCms_',                    //Cookie前缀
    'cookiedomain' => '',                    //cookie 作用域。请设置为 .yourdomain.com 形式
    'cookiepath' => '/'                    //cookie 作用路径
);