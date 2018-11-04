-- MyCMS INSTALL MAKE SQL DUMP V1.0

DROP TABLE IF EXISTS `{$pre}actions`;
CREATE TABLE `{$pre}actions` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `name` char(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url_model` tinyint(1) UNSIGNED NOT NULL,
  `cachetime` mediumint(6) UNSIGNED NOT NULL DEFAULT '0',
  `url_rewrite` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modify` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}actions`
--

TRUNCATE TABLE `{$pre}actions`;
--
-- 转存表中的数据 `{$pre}actions`
--

INSERT INTO `{$pre}actions` (`id`, `name`, `url_model`, `cachetime`, `url_rewrite`, `description`, `modify`) VALUES
(1, 'index', 2, 86400, '', '首页', 1),
(2, 'article', 2, 86400, '', '文章查看页', 1),
(3, 'category', 2, 3600, '', '分类列表页', 1),
(4, 'login', 0, 0, '', '登陆页', 1),
(5, 'html', 0, 0, '', 'HTML更新', 1),
(6, 'tag', 2, 86400, '', 'TAG标签', 1),
(7, 'sitemap', 0, 0, '', '网站地址生成/更新页', 1),
(8, 'validate', 0, 0, '', '验证程序', 1),
(9, 'register', 0, 0, '', '注册页', 1),
(10, 'ajax', 0, 0, '', 'AJAX程序', 1),
(11, 'topic', 2, 864000, '', '专题页', 1),
(12, 'user', 0, 0, '', '用户页', 1),
(13, 'comments', 0, 600, '', '文章评论页', 1),
(14, 'message', 0, 0, NULL, '用户留言信息', 1),
(15, 'friendlinks', 0, 0, NULL, '友情链接申请或者展示。', 1),
(16, 'search', 0, 0, NULL, '站内文章搜索程序。', 1);

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}action_logs`
--

DROP TABLE IF EXISTS `{$pre}action_logs`;
CREATE TABLE `{$pre}action_logs` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `acid` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}action_logs`
--

TRUNCATE TABLE `{$pre}action_logs`;
-- --------------------------------------------------------

--
-- 表的结构 `{$pre}adminsession`
--

DROP TABLE IF EXISTS `{$pre}adminsession`;
CREATE TABLE `{$pre}adminsession` (
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `ip` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `errorcount` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}adminsession`
--

TRUNCATE TABLE `{$pre}adminsession`;
--
-- 转存表中的数据 `{$pre}adminsession`
--

INSERT INTO `{$pre}adminsession` (`uid`, `ip`, `dateline`, `errorcount`) VALUES
(1, '127.0.0.1', 1541324570, -1);

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}admin_action`
--

DROP TABLE IF EXISTS `{$pre}admin_action`;
CREATE TABLE `{$pre}admin_action` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `upid` tinyint(3) NOT NULL,
  `action` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `displayorder` tinyint(3) NOT NULL,
  `type` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}admin_action`
--

TRUNCATE TABLE `{$pre}admin_action`;
--
-- 转存表中的数据 `{$pre}admin_action`
--

INSERT INTO `{$pre}admin_action` (`id`, `upid`, `action`, `name`, `description`, `displayorder`, `type`) VALUES
(1, 0, 'settings', '系统设置', '您通过本页面可以对站点基本信息、附件目录、html目录、图片水印、图片缩略图等进行参数设定。', 20, 1),
(2, 1, 'html', 'HTML管理', '本操作可以批量生成全站的HTML静态,可以对HTML文件删除、HTML立即更新等操作!', 23, 1),
(3, 0, 'article', '文章管理', '你可以对文章进行修改、删除、移动、加精华、置顶等操作。', 10, 1),
(4, 3, 'postarticle', '发布文章', '定期发布新的原创文章有助于网站排名与优化，提升用户体验，推荐每日至少发布1篇。', 11, 1),
(5, 3, 'tag', '标签管理', '可以删除和禁用标签,当标签被删除,相关的文章并不会删除,但是会删除标签链接表,当标签被禁用后,会删除标签链接表但相关文章也不会被删除,当有新文章使用禁用标签时无效。', 15, 1),
(6, 3, 'comments', '评论管理', '可以删除和修改评论,可以对评论进行置顶、加精华等操作。如果用户名是灰色，表示匿名。', 13, 1),
(7, 3, 'categorys', '文章分类', '你可以对网站分类进行管理操作，如增加、修改、删除、增加子分类等。', 14, 1),
(8, 0, 'attachments', '附件管理', '您可以对附件进行删除、修改、注释等操作。', 30, 1),
(9, 8, 'blocks', '数据调用', '本操作可以根据要求生成1个数据调用的模块代码，放入模板后就可以调用相关的内容!', 32, 1),
(10, 8, 'sitemap', '网站地图', '您可以生成本站的地图提交给搜索引擎，提升网站的索引，还可以进行移动URL对应PC的操作。', 31, 1),
(11, 0, 'member', '用户管理', '可以修改会员的密码、用户组，可以增加、禁用、删除等。', 40, 1),
(12, 11, 'usergroups', '用户组管理', '可以修改用户组的名字，设置升级需要的经验值等信息。', 41, 1),
(13, 1, 'friendlinks', '友情连接', '友情链接是具有一定资源互补优势的网站之间的简单合作形式，可以提升网站流量、完善用户体验、增加网站外链、提升PR、提高关键字排名和知名度等好处。但是同时也要注意对方网站内容质量，链接数量等，推荐本站链接数量不要超过50个友情链接！', 26, 1),
(14, 1, 'cache', '缓存更新', '系统使用特有的数据缓存机制，提升网站访问速度，降低数据库负载。您可以通过本操作将对网站的分类、计划任务、系统配置等缓存进行更新操作。', 22, 1),
(15, 1, 'database', '数据维护', '通过本工具，您可以对数据表进行优化、修复、备份等操作。', 25, 1),
(16, 1, 'words', '词语过滤', '网站要过滤不良词语一般分以下几类：1、国家机关领导人等各类名字；2、国家明令禁止行为词语；3、保护未成年人的词语；4、不良名称类词语（含黄、赌、毒、反动等）；5、其他敏感类词语，与法律规范违背的、与人文道德违背的、与大众积极意识形态违背的等。', 27, 1),
(17, 1, 'crons', '计划任务', '根据设置会自动执行所需要的程序，计划任务是通过./function/core.php核心文件加载，使用不当会严重影响系统所以请严格配置程序', 24, 1),
(18, 1, 'company', '公司信息', '设置公司相关的信息，如：电话、手机、地址、邮箱等信息，系统将会自动生成一个二维码名片及更新联系页面。', 21, 1),
(19, 1, 'adminlogs', '系统日志', '这是一个隐藏的管理功能，可以查看后台访问和操作记录。', -1, 1),
(20, 1, 'index', '数据统计', '通过站点统计，您可以整体把握站点的发展状况。', -1, 1),
(21, 1, 'sidemeun', '系统菜单', '可以按自己的习惯修改后台系统左侧菜单。如果你要护系统管理功能请在这里增加。', 28, 1),
(22, 3, 'topic', '专题管理', '通过专题可以新建单页面，通过指定不同的模板可以实现公司简介、联系页、版权说明等，也可以聚合内容，做成真正的专题以扩展与丰富网站内容。', 16, 1),
(23, 1, 'login_logs', '登陆日志', '可以查询用户登陆成功或者失败的信息！', 28, 1),
(24, 1, 'action_logs', '用户动态', '用户评论、点赞、发送消息的动态管理。', 29, 1),
(25, 1, 'actions', '模块管理', '管理系统所有的功能模板，如文章、评论、专题、标签的前台显示功能模块。', 30, 1);

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}article`
--

DROP TABLE IF EXISTS `{$pre}article`;
CREATE TABLE `{$pre}article` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `subject` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catid` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `username` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `viewnum` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `replynum` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `digest` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `top` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `good` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `allowreply` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `hash` char(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cover` mediumint(10) UNSIGNED NOT NULL DEFAULT '0',
  `grade` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `folder` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- 表的结构 `{$pre}article_content`
--

DROP TABLE IF EXISTS `{$pre}article_content`;
CREATE TABLE `{$pre}article_content` (
  `nid` mediumint(8) UNSIGNED NOT NULL,
  `id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pageorder` smallint(6) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}attachments`
--

DROP TABLE IF EXISTS `{$pre}attachments`;
CREATE TABLE `{$pre}attachments` (
  `aid` mediumint(8) UNSIGNED NOT NULL,
  `id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `attachtype` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `isimage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `size` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `hash` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- 表的结构 `{$pre}bad_words`
--

DROP TABLE IF EXISTS `{$pre}bad_words`;
CREATE TABLE `{$pre}bad_words` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `find` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `replace` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}bad_words`
--

TRUNCATE TABLE `{$pre}bad_words`;
-- --------------------------------------------------------

--
-- 表的结构 `{$pre}categories`
--

DROP TABLE IF EXISTS `{$pre}categories`;
CREATE TABLE `{$pre}categories` (
  `catid` smallint(6) UNSIGNED NOT NULL,
  `upid` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `displayorder` mediumint(6) UNSIGNED NOT NULL DEFAULT '0',
  `tpl` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `viewtpl` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `htmlpath` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `perpage` smallint(3) UNSIGNED NOT NULL DEFAULT '30',
  `prehtml` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}comments`
--

DROP TABLE IF EXISTS `{$pre}comments`;
CREATE TABLE `{$pre}comments` (
  `cid` int(10) UNSIGNED NOT NULL,
  `id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `username` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `subject` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hot` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `hideauthor` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- 表的结构 `{$pre}crons`
--

DROP TABLE IF EXISTS `{$pre}crons`;
CREATE TABLE `{$pre}crons` (
  `cronid` smallint(6) UNSIGNED NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `name` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `filename` char(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastrun` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `nextrun` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `weekday` tinyint(1) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `hour` tinyint(2) NOT NULL DEFAULT '0',
  `minute` char(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}crons`
--

TRUNCATE TABLE `{$pre}crons`;
--
-- 转存表中的数据 `{$pre}crons`
--

INSERT INTO `{$pre}crons` (`cronid`, `available`, `name`, `filename`, `lastrun`, `nextrun`, `weekday`, `day`, `hour`, `minute`) VALUES
(0, 1, '文章查看数更新', 'update_article_viewnum.php', 1541314615, 1541314860, -1, -1, -1, '1');

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}friendlinks`
--

DROP TABLE IF EXISTS `{$pre}friendlinks`;
CREATE TABLE `{$pre}friendlinks` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `displayorder` tinyint(3) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `logo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}login_logs`
--

DROP TABLE IF EXISTS `{$pre}login_logs`;
CREATE TABLE `{$pre}login_logs` (
  `id` mediumint(8) NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL,
  `ip` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` tinyint(1) NOT NULL,
  `uid` mediumint(8) NOT NULL,
  `username` char(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- 表的结构 `{$pre}members`
--

DROP TABLE IF EXISTS `{$pre}members`;
CREATE TABLE `{$pre}members` (
  `uid` mediumint(8) UNSIGNED NOT NULL,
  `groupid` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `username` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` char(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `experience` int(10) NOT NULL DEFAULT '0',
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updatetime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastlogin` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `regip` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastloginip` char(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastcommenttime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastposttime` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `avatar` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}search`
--

DROP TABLE IF EXISTS `{$pre}search`;
CREATE TABLE `{$pre}search` (
  `sid` int(10) UNSIGNED NOT NULL,
  `keywords` char(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL,
  `updatetime` int(10) UNSIGNED NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}settings`
--

DROP TABLE IF EXISTS `{$pre}settings`;
CREATE TABLE `{$pre}settings` (
  `variable` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(9999) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}settings`
--

TRUNCATE TABLE `{$pre}settings`;
--
-- 转存表中的数据 `{$pre}settings`
--

INSERT INTO `{$pre}settings` (`variable`, `value`) VALUES
('allowcache', '0'),
('allowregister', '1'),
('attachmentdir', 'data/attachment'),
('attachmentdirtype', 'month'),
('attachmenturl', '/data/attachment/'),
('baidusendurl', ''),
('checkgrade', '一等级	二等级	三等级	四等级	五等级'),
('companyabout', ''),
('companyaddress', ''),
('companycell', ''),
('companyceo', ''),
('companyemail', ''),
('companyname', ''),
('companytel', ''),
('cronnextrun', ''),
('formhash', ''),
('gzipcompress', '0'),
('htmldir', 'html'),
('htmlmode', '0'),
('htmlurl', '/'),
('makedate', '0'),
('miibeian', ''),
('mobileurl', ''),
('noseccode', '0'),
('registerrule', ''),
('seodescription', 'MyCMS'),
('seokeywords', 'MyCMS'),
('seotitle', 'MyCMS'),
('setarticlenum', '30'),
('sitekey', ''),
('sitename', 'MyCMS'),
('static', '/data/templates/default/static/'),
('tagshow', '0'),
('template', 'default'),
('thumbbgcolor', '#C0C0C0'),
('timeoffset', '8');

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}tags`
--

DROP TABLE IF EXISTS `{$pre}tags`;
CREATE TABLE `{$pre}tags` (
  `tagid` mediumint(8) UNSIGNED NOT NULL,
  `tagname` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `close` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}tags_map`
--

DROP TABLE IF EXISTS `{$pre}tags_map`;
CREATE TABLE `{$pre}tags_map` (
  `tagid` mediumint(8) UNSIGNED NOT NULL,
  `articleid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `recipeid` mediumint(8) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}topic`
--

DROP TABLE IF EXISTS `{$pre}topic`;
CREATE TABLE `{$pre}topic` (
  `id` smallint(6) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateline` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lastpost` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tpl` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `htmlpath` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `perpage` smallint(3) UNSIGNED NOT NULL DEFAULT '30',
  `viewnum` int(8) UNSIGNED NOT NULL DEFAULT '0',
  `close` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `{$pre}usergroups`
--

DROP TABLE IF EXISTS `{$pre}usergroups`;
CREATE TABLE `{$pre}usergroups` (
  `groupid` smallint(6) UNSIGNED NOT NULL,
  `grouptitle` char(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `system` tinyint(1) NOT NULL DEFAULT '0',
  `explower` int(10) NOT NULL DEFAULT '0',
  `allowpost` tinyint(1) NOT NULL DEFAULT '0',
  `allowcomment` tinyint(1) NOT NULL DEFAULT '0',
  `allowpostattach` tinyint(1) NOT NULL DEFAULT '0',
  `allowvote` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 插入之前先把表清空（truncate） `{$pre}usergroups`
--

TRUNCATE TABLE `{$pre}usergroups`;
--
-- 转存表中的数据 `{$pre}usergroups`
--

INSERT INTO `{$pre}usergroups` (`groupid`, `grouptitle`, `system`, `explower`, `allowpost`, `allowcomment`, `allowpostattach`, `allowvote`) VALUES
(1, '管理员', -1, 0, 1, 1, 1, 1),
(2, '游客组', -1, 0, 0, 0, 0, 1),
(3, '禁止访问', -1, 0, 0, 0, 0, 0),
(4, '禁止发言', -1, 0, 0, 0, 0, 0),
(5, '受限制会员', -1, -999999999, 0, 0, 0, 0),
(6, '网站编辑', -1, 0, 1, 1, 1, 1),
(10, '贵宾VIP', 1, 0, 0, 1, 1, 1),
(11, '初级会员', 0, 0, 0, 1, 0, 1),
(12, '中级会员', 0, 300, 0, 1, 0, 1),
(13, '高级会员', 0, 800, 0, 1, 1, 1);

--
-- 转储表的索引
--

--
-- 表的索引 `{$pre}actions`
--
ALTER TABLE `{$pre}actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- 表的索引 `{$pre}action_logs`
--
ALTER TABLE `{$pre}action_logs`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `{$pre}admin_action`
--
ALTER TABLE `{$pre}admin_action`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `action_2` (`action`),
  ADD KEY `action` (`action`);

--
-- 表的索引 `{$pre}article`
--
ALTER TABLE `{$pre}article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catid` (`catid`),
  ADD KEY `cover` (`cover`),
  ADD KEY `viewnum` (`viewnum`),
  ADD KEY `dateline` (`dateline`),
  ADD KEY `top` (`top`),
  ADD KEY `digest` (`digest`),
  ADD KEY `replynum` (`replynum`),
  ADD KEY `lastpost` (`lastpost`),
  ADD KEY `uid` (`uid`),
  ADD KEY `list` (`id`,`dateline`),
  ADD KEY `url` (`url`(191)),
  ADD KEY `folder` (`folder`);

--
-- 表的索引 `{$pre}article_content`
--
ALTER TABLE `{$pre}article_content`
  ADD PRIMARY KEY (`nid`),
  ADD KEY `id` (`id`),
  ADD KEY `pageorder` (`pageorder`);

--
-- 表的索引 `{$pre}attachments`
--
ALTER TABLE `{$pre}attachments`
  ADD PRIMARY KEY (`aid`),
  ADD KEY `hash` (`hash`),
  ADD KEY `id` (`id`),
  ADD KEY `url` (`url`(191)),
  ADD KEY `isimage` (`isimage`);

--
-- 表的索引 `{$pre}bad_words`
--
ALTER TABLE `{$pre}bad_words`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `{$pre}categories`
--
ALTER TABLE `{$pre}categories`
  ADD PRIMARY KEY (`catid`),
  ADD UNIQUE KEY `htmlpath` (`htmlpath`),
  ADD KEY `upid` (`upid`),
  ADD KEY `displayorder` (`displayorder`);

--
-- 表的索引 `{$pre}comments`
--
ALTER TABLE `{$pre}comments`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `id` (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `dateline` (`dateline`),
  ADD KEY `hot` (`hot`);

--
-- 表的索引 `{$pre}crons`
--
ALTER TABLE `{$pre}crons`
  ADD PRIMARY KEY (`cronid`),
  ADD KEY `nextrun` (`available`,`nextrun`);

--
-- 表的索引 `{$pre}friendlinks`
--
ALTER TABLE `{$pre}friendlinks`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `{$pre}login_logs`
--
ALTER TABLE `{$pre}login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- 表的索引 `{$pre}members`
--
ALTER TABLE `{$pre}members`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `groupid` (`groupid`),
  ADD KEY `username` (`username`),
  ADD KEY `password` (`password`),
  ADD KEY `dateline` (`dateline`),
  ADD KEY `updatetime` (`updatetime`),
  ADD KEY `lastlogin` (`lastlogin`),
  ADD KEY `lastcommenttime` (`lastcommenttime`),
  ADD KEY `lastposttime` (`lastposttime`);

--
-- 表的索引 `{$pre}search`
--
ALTER TABLE `{$pre}search`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `keywords` (`keywords`),
  ADD KEY `count` (`count`);

--
-- 表的索引 `{$pre}settings`
--
ALTER TABLE `{$pre}settings`
  ADD PRIMARY KEY (`variable`);

--
-- 表的索引 `{$pre}tags`
--
ALTER TABLE `{$pre}tags`
  ADD PRIMARY KEY (`tagid`),
  ADD UNIQUE KEY `tagname` (`tagname`),
  ADD KEY `tagid` (`tagid`,`dateline`);

--
-- 表的索引 `{$pre}tags_map`
--
ALTER TABLE `{$pre}tags_map`
  ADD KEY `tagid` (`tagid`,`articleid`);

--
-- 表的索引 `{$pre}topic`
--
ALTER TABLE `{$pre}topic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `htmlpath` (`htmlpath`);

--
-- 表的索引 `{$pre}usergroups`
--
ALTER TABLE `{$pre}usergroups`
  ADD PRIMARY KEY (`groupid`);

COMMIT;