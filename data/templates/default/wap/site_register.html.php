<?php exit()?><!--{template 'header'}--><style type="text/css">#register{margin:10px auto;width:90%}#message{padding:100px 0;text-align:center}</style><div id="register">
<!--{if $_MCONFIG['allowregister']==1}--><h1><a href="#action/login#" id="log">登陆</a><a href="#action/register#" id="reg">注册</a></h1><style type="text/css">
#register{margin:100px auto;width:880px;border:1px solid #dadada;padding-bottom:30px;background-color:#fff;box-shadow:0 0 8px #dadada;border-radius:3px}
#register h1{margin:0;padding:0}#register h1 a{width:440px;display:inline-block;text-align:center;height:69px;font:24px/69px "Microsoft YaHei", Verdana, Arial, sans-serif, simsun}
#log{background-image:url("data:image/gif;base64,R0lGODlhAQBFALMAAAAAAP///+7u7u3t7ezs7Ovr6+rq6unp6ejo6Ofn5+bm5uXl5eTk5P///wAAAAAAACH5BAEAAA0ALAAAAAABAEUAAAQcUMhJxbiX6L2LL0YoGkd5IGiarGyivPC7zDTDRAA7");color:#333}#reg{color:#FFF;background-image:url("data:image/gif;base64,R0lGODlhAQBFANUAAAAAAP///95yEOJwEOFzEt9xEuJ0E+ByE99yE+Z0FOV0FON1FOFzFOZ1Feh2Fud2Fuh3F+p4GOl4GOt5Gex6Gux8Gut6Gu99HO58G+19G+x7G/B+HfOBHvF/HvSCH/KAH/eDIPWAIPWDIPOBIPmFIviEIfeBIfaBIfqGI////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAACkALAAAAAABAEUAAAY2QJQQRSoaiSBUSeQ5mUKc0ehD7Vg3m4sWg8lUNBTLZBKRSCCQh+PRaCQUC4OcQD8wDojCQBAEADs=")}#register p{margin:10px 0}#register p strong{display:inline-block;width:150px;text-align:right}#register input{margin-right:10px;padding:2px;width:260px;height:35px}#register span i{padding-left:20px;font-style:normal}.input_search{margin:20px 150px;height:45px;font:18px 'Microsoft YaHei UI',Microsoft YaHei}#seccode{display:inline-block;width:30px}#registerrule{position:fixed;top:20%;left:20%;z-index:401;width:60%;height:60%;min-width:500px;border:1px solid #ccc;background:#fff}#registerrule h2{padding:10px 20px}#registerrule .rule{overflow-y:auto;padding:0 20px;height:78%}#registerrule .agree{position:absolute;right:1px;bottom:-9px;left:1px;padding:10px;border-top:1px solid #ccc;background:#f2f2f2;text-align:center}.agree a{display:inline-block;margin-right:30px;padding:3px 10px;height:23px;border:1px solid #999;background-color:#06f;color:#fff}#register .ok{background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAIAAADZF8uwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAa9JREFUeNo0kc9LFGEYx995d+adnRk3Z/2BrSG60MFM8LJdxIvdCyPPRUQdK7rVJTz0L3gQlJBATxbSuYLqoGgRSQjLQlvalLvOrrPzzvvOvL96txKe0/P58OXh+RpUKACAVKxG1mrxy4BsC6EGrEq558qkfyNnIE0NLVHx+214u8fsdyyowBHh7ZBGTdyBfHS+vF5AJagzXh/f7EOjZ90hAb6F2W6YfgUq67N9Ir6v7s8LlcFq8tzN+X7e+pW+CtO9hCUkY21y0KT1S0N3T1h96+czs9rZGHTdnOrtsCOapZTJlAM998Zb+po3P1Z2gw0Y4I8CHE+4C3ODTcIkZYB2DayNxzsFy7RrrR3IhQbR2qGvt7fGsDbu/zUebXtKmVwhJS1YNKdCgpUYWKx6mj248N+QIGdb54BwSt5FeP7MXCOOufQYR08/e6eGCY2ib49gTCulawbm6dLebAoOnXwBZ3FEGjrDQWN+fiRst2jEn1x+131mlAbLX66fiLqN8gh5BvAkR3GciAQ+nHlRdIaNf7UImb0/WP4UbO43PkipysXpyvDV2fIdE3Zr+SPAADzh6IHRO5VXAAAAAElFTkSuQmCC") no-repeat 0 5px;color:green}#register .error{color:red;background:url("data:image/gif;base64,R0lGODlhDgAOANUAAAAAAP///8/Oz/WgU+2MTOl3P+B+Udt2TepoNuadg+iljeRdL9JWLOGYgN1WLtxMJ8xHJ9qOfMlROcBHMdRpVui5sbtDMNB6bd6wqb1XS7poYcKVkczBwPbq6bBbVapZVbSIhaU/Ovz5+fn29v39/fr6+vf39/Hx8e3t7ejo6Nzc3MjIyMfHx////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAC0ALAAAAAAOAA4AAAafwJZQVIlQKJGKSMjsXAyEwYBguHSYoyehgEAUqJdRi4Q5FAKKriJQOGBIpkthkQgk6olF4WI6ZRALDA0BaAwLCBknKBkLCw6DaA6NGSgoGg4OaxERAQ2YGignGxIPAREQEJwPEhsnJioeE6gPD6gTHiomJCgcHxYQtBAWHxwoJC0lKQIgHyEhHyACKSVMJSgqAissAioo1ExjfZWux0JBADs=") no-repeat 0 5px}</style>
<form method="post" style="margin:20px" action="#action/register#">			
<p><strong>用户名:</strong> <input size="30" type="text" placeholder="推荐使用中文用户名" value="" onblur="chickname(this.value)" name="username" autofocus="autofocus"/> <span id="userchick">请输入用户名，长度为6-15个字符，不能包含空格。</span></p>
<p><strong>密码:</strong> <input size="30" type="password" id="password" onblur="chicpw(this.value)" value="" name="password"/> <span id="pwchick">请输入密码，长度为6-20个字符。</span></p>
<p><strong>确认密码:</strong> <input name="confirm_password" id="confirm_password" type="password" disabled="disabled" onblur="chicfpw(this.value)" value="" size="30"/> <span id="cfpwchick">请先输入密码再次确认密码。</span></p>			
<p><strong>邮箱:</strong> <input size="30" type="email" placeholder="请输入您常用邮箱地址" onblur="chickemail(this.value)" value="" name="email"/> <span id="mailchick">请输入您的E-mail地址。</span></p>
<!--{if empty($_MCONFIG['noseccode'])}--><p style="width:440px"><img src="#action/validate#" width="130" height="45" id="validate" title="点击刷新" onClick="this.src='#action/validate#-'+Math.random();"/><strong>验证码:</strong> <input size="4" type="text" onblur="chickseccode(this.value)" value="" style="width:100px;margin-top:5px" name="seccode"/> <span id="seccode"></span> </p><!--{/if}-->
<input class="input_search" type="submit" value="注 册" name="regsubmit"/>
<input type="hidden" name="formhash" value="$formhash" /></form>			
<script>function chickname(username){
	var userchick=document.getElementById("userchick");
	if(username==''){
		userchick.innerHTML='<i class="error">用户名不能为空！</i>';		
	}else if(getLength(username) < 4 || getLength(username) > 15){
		userchick.innerHTML='<i class="error">用户名长度为4-15个字符之间！</i>';
		return false;
	}else{
		reg = /[^\w\u4E00-\u9FA5]/g;
		if(reg.test(username)){ 
 			userchick.innerHTML='<i class="error">请输入正确的用户名，可以为汉字、英文、数字的组合！</i>';
			return false;
		}else{ 		
		ajax({
		 method: 'POST',
		  url: '#action/ajax#',
		  data: {
			  formhash: '$formhash',
			  username: username,
			  chickusername:true
			  },
			  success: function(e) {
				userchick.innerHTML=e;
			}
		});	
		}
	}
	return true;	
}
function chickemail(mail){
	var mailchick=document.getElementById("mailchick");
	var reg =/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,5})$/;
	if(mail==''){
		mailchick.innerHTML='<i class="error">E-mail 地址不能为空！</i>';
		return false;
	}else if(!reg.test(mail)){	
		mailchick.innerHTML='<i class="error">E-mail 地址不合法！</i>';
		return false;
	}else{
		ajax({
			 method: 'POST',
			  url: '#action/ajax#',
			  data: {
				  formhash: '$formhash',
				  mail: mail,
				  mailchick:true
				  },
				  success: function (e) {
					  mailchick.innerHTML=e;
				}
		});	
	}
	return true;	
}

function chickseccode(seccode){
	var seccodeHTML=document.getElementById("seccode");
	if(seccode==''){
		seccodeHTML.innerHTML='<i class="error"></i>';
		return false;
	}else{					
		ajax({
			 method: 'POST',
			  url: '#action/ajax#',
			  data: {
				  formhash: '$formhash',
				  seccode: seccode,
				  seccodechick:true
				  },
				  success: function (e) {
					  seccodeHTML.innerHTML=e;
				}
		});	
	}
	return true;
}
function getLength(str){return str.replace(/[^\x00-xff]/g,'xx').length;}
function chicpw(pw){
	var m=findStr(pw,pw[0]);
	var pwHTML=document.getElementById("pwchick");
	var cfpwHTML=document.getElementById("confirm_password");	
	if(pw==''){		
		pwHTML.innerHTML='<i class="error">输入的密码不能为空！</i>';	
		return false;
	}
	if(m==pw.length){
		pwHTML.innerHTML='<i class="error">不能使用相同字符！</i>';	
		return false;
	}
	if(getLength(pw)>5&&getLength(pw)<20){
		cfpwHTML.removeAttribute('disabled');
		pwHTML.innerHTML='<i class="ok">ok！</i>';	
		return true;	
	}else{
		cfpwHTML.setAttribute('disabled','disabled');
		pwHTML.innerHTML='<i class="error">密码长度不合格！</i>';	
		return false;
	}
}
function chicfpw(cfpw){
	var pw=document.getElementById("password");
	var cfpwHTML=document.getElementById("cfpwchick");
	if(cfpw!=pw.value){
		cfpwHTML.innerHTML='<i class="error">两次输入的密码不一致！</i>';	
		return false;
	}else{
		cfpwHTML.innerHTML='<i class="ok">ok！</i>';	
		return true;
	}
}
function findStr(str,n){
	var tmp = 0;
	for(var i=0;i<str.length;i++){
		if(str.charAt(i)==n){
		tmp++;
		}
	}
	return tmp;
}
function hide(v){
	var hvar = document.getElementById(v);
	hvar.style.display="none";
	}
</script><!--{if !empty($_MCONFIG[registerrule])}--><div id="registerrule"><h2>{$_MCONFIG[sitename]}服务条款</h2><div class="rule"><!--{eval echo htmlspecialchars_decode($_MCONFIG[registerrule])}--></div><p class="agree"><a href="javascript:hide('registerrule');">同意</a> <a href="{MURL}" style="background:#ccc;">不同意</a></p></div><!--{/if}--><!--{else}--><div id="message"><h2>网站未开放注册功能！</h2></div><!--{/if}--></div><!--{template 'footer'}-->