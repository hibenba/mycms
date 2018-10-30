$(document).ready(function(){	
	$("#menu").hover(function() { 
			$(this).find('ul').stop(true, true).slideDown(500); 
		}, function() { 
			$(this).find('ul').stop(true, true).slideUp(200); 
	});		
	settime();//倒计时
});
var v = 7200;
function settime() {
	var m = Math.floor(v/60);
	var s = v %60;
	$('#logintimeout').html(m+'分'+s+'秒后将会自动退出系统');
	v--;
	if(v<=0){
		$('#logintimeout').html('您的登陆信息已失效，请重新登陆系统！');
	}
	setTimeout(function(){settime()},980) 
}