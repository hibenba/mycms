<?php
if(!defined('IN_MYCMS')) exit('Access Denied');
$_MGLOBAL['grouparr']=array(1 => array('groupid' => 1,'grouptitle' => '管理员','system' => -1,'explower' => 0,'allowpost' => 1,'allowcomment' => 1,'allowpostattach' => 1,'allowvote' => 1),6 => array('groupid' => 6,'grouptitle' => '网站编辑','system' => -1,'explower' => 0,'allowpost' => 1,'allowcomment' => 1,'allowpostattach' => 1,'allowvote' => 1),4 => array('groupid' => 4,'grouptitle' => '禁止发言','system' => -1,'explower' => 0,'allowpost' => 0,'allowcomment' => 0,'allowpostattach' => 0,'allowvote' => 0),3 => array('groupid' => 3,'grouptitle' => '禁止访问','system' => -1,'explower' => 0,'allowpost' => 0,'allowcomment' => 0,'allowpostattach' => 0,'allowvote' => 0),2 => array('groupid' => 2,'grouptitle' => '游客组','system' => -1,'explower' => 0,'allowpost' => 0,'allowcomment' => 0,'allowpostattach' => 0,'allowvote' => 1),5 => array('groupid' => 5,'grouptitle' => '受限制会员','system' => -1,'explower' => -999999999,'allowpost' => 0,'allowcomment' => 0,'allowpostattach' => 0,'allowvote' => 0),13 => array('groupid' => 13,'grouptitle' => '高级会员','system' => 0,'explower' => 800,'allowpost' => 0,'allowcomment' => 1,'allowpostattach' => 1,'allowvote' => 1,'exphigher' => 999999999),12 => array('groupid' => 12,'grouptitle' => '中级会员','system' => 0,'explower' => 300,'allowpost' => 0,'allowcomment' => 1,'allowpostattach' => 0,'allowvote' => 1,'exphigher' => 799),11 => array('groupid' => 11,'grouptitle' => '初级会员','system' => 0,'explower' => 0,'allowpost' => 0,'allowcomment' => 1,'allowpostattach' => 0,'allowvote' => 1,'exphigher' => 299),10 => array('groupid' => 10,'grouptitle' => '贵宾VIP','system' => 1,'explower' => 0,'allowpost' => 0,'allowcomment' => 1,'allowpostattach' => 1,'allowvote' => 1));