var Cookieuser='MyCms_user';
function thismodified(maketime,action,id,page){document.writeln('<script type="text/javascript" src="/main.php?action-html-maketime-'+maketime+'-thisaction-'+action+'-id-'+id+'-page-'+page+'"></script>')}
var site_search='<form action="/main.php?action-search" method="post" target="_blank"><input type="text" name="q" placeholder="请输入关键词" autocomplete="off" class="search-input"><input type="submit" class="search-submit" value="搜索"></form>';
function comments(theid){

	
	
}
function topbar(){
username=getCookie(Cookieuser);
if (username!=null && username!=""){document.writeln('欢迎登陆，<a href="/main.php?action-user">'+username+'</a> ，<a href="/main.php?action-user">会员中心</a> <a href="/main.php?action-login-logout-1">退出登陆</a>');}else {document.writeln('欢迎光临，请 <a href="/main.php?action-login">登陆</a> 或者 <a href="/main.php?action-register">注册</a>');}}
function getCookie(c_name){if(document.cookie.length>0){c_start=document.cookie.indexOf(c_name+"=");if(c_start!=-1){c_start=c_start+c_name.length+1;c_end=document.cookie.indexOf(";",c_start);if(c_end==-1)c_end=document.cookie.length;return unescape(document.cookie.substring(c_start,c_end))}}return false}
function go_wap(url){var thisOS=navigator.platform;var os=new Array("iPhone","iPod","iPad","android","Nokia","SymbianOS","Symbian","Windows Phone","Phone","Linux armv71","MAUI","UNTRUSTED/1.0","Windows CE","BlackBerry","IEMobile");for(var i=0;i<os.length;i++){if(thisOS.match(os[i])){window.location=url}}if(navigator.platform.indexOf("iPad")!=-1){window.location=url}var check=navigator.appVersion;if(check.match(/linux/i)){if(check.match(/mobile/i)||check.match(/X11/i)){window.location=url}}Array.prototype.in_array=function(e){for(i=0;i<this.length;i++){if(this[i]==e){return true}}return false}}
function ajax(opt){opt=opt||{};opt.method=opt.method.toUpperCase()||'POST';opt.url=opt.url||'';opt.async=opt.async||true;opt.data=opt.data||null;opt.success=opt.success||function(){};var xmlHttp=null;if(XMLHttpRequest){xmlHttp=new XMLHttpRequest()}else{xmlHttp=new ActiveXObject('Microsoft.XMLHTTP')}var params=[];for(var key in opt.data){params.push(key+'='+opt.data[key])}var postData=params.join('&');if(opt.method.toUpperCase()==='POST'){xmlHttp.open(opt.method,opt.url,opt.async);xmlHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;charset=utf-8');xmlHttp.send(postData)}else if(opt.method.toUpperCase()==='GET'){xmlHttp.open(opt.method,opt.url,opt.async);xmlHttp.send(null)}xmlHttp.onreadystatechange=function(){if(xmlHttp.readyState==4&&xmlHttp.status==200){opt.success(xmlHttp.responseText)}}}
function reward(){document.getElementById('reward_show').style.display='block'}
function share(){var share=document.getElementById('bdshare');if(share.style.display=='block'){share.style.display='none'}else{share.style.display='block'}}
function bdshare(){document.writeln('<div class="bdsharebuttonbox" id="bdshare"><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_renren" data-cmd="renren" title="分享到人人网"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_more" data-cmd="more"></a></div>')}
function show(str){document.write(str);}