<?php exit()?><!--{template 'header'}--><div id="login"><h1><a href="#action/login#" id="log">登陆</a><a href="#action/register#" id="reg">注册</a></h1>
<style type="text/css">#login{margin:100px auto;width:880px;border:1px solid #dadada;padding-bottom:30px;background-color:#fff;box-shadow:0 0 8px #dadada;border-radius:3px}
#login h1{margin:0;padding:0}#login h1 a{width:440px;display:inline-block;text-align:center;height:69px;font:24px/69px "Microsoft YaHei", Verdana, Arial, sans-serif, simsun}
#reg{background-image:url("data:image/gif;base64,R0lGODlhAQBFALMAAAAAAP///+7u7u3t7ezs7Ovr6+rq6unp6ejo6Ofn5+bm5uXl5eTk5P///wAAAAAAACH5BAEAAA0ALAAAAAABAEUAAAQcUMhJxbiX6L2LL0YoGkd5IGiarGyivPC7zDTDRAA7");color:#333}#log{color:#FFF;background-image:url("data:image/gif;base64,R0lGODlhAQBFANUAAAAAAP///95yEOJwEOFzEt9xEuJ0E+ByE99yE+Z0FOV0FON1FOFzFOZ1Feh2Fud2Fuh3F+p4GOl4GOt5Gex6Gux8Gut6Gu99HO58G+19G+x7G/B+HfOBHvF/HvSCH/KAH/eDIPWAIPWDIPOBIPmFIviEIfeBIfaBIfqGI////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAACkALAAAAAABAEUAAAY2QJQQRSoaiSBUSeQ5mUKc0ehD7Vg3m4sWg8lUNBTLZBKRSCCQh+PRaCQUC4OcQD8wDojCQBAEADs=")}#login p{margin:10px 0}#login p strong{display:inline-block;width:150px;text-align:right}input{width:260px;height:35px}select{width:160px;height:30px}.input_search{margin:20px 150px;height:45px;font:18px 'Microsoft YaHei UI',Microsoft YaHei}#login i{padding-left:20px}#login .ok{background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAwAAAAMCAIAAADZF8uwAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAa9JREFUeNo0kc9LFGEYx995d+adnRk3Z/2BrSG60MFM8LJdxIvdCyPPRUQdK7rVJTz0L3gQlJBATxbSuYLqoGgRSQjLQlvalLvOrrPzzvvOvL96txKe0/P58OXh+RpUKACAVKxG1mrxy4BsC6EGrEq558qkfyNnIE0NLVHx+214u8fsdyyowBHh7ZBGTdyBfHS+vF5AJagzXh/f7EOjZ90hAb6F2W6YfgUq67N9Ir6v7s8LlcFq8tzN+X7e+pW+CtO9hCUkY21y0KT1S0N3T1h96+czs9rZGHTdnOrtsCOapZTJlAM998Zb+po3P1Z2gw0Y4I8CHE+4C3ODTcIkZYB2DayNxzsFy7RrrR3IhQbR2qGvt7fGsDbu/zUebXtKmVwhJS1YNKdCgpUYWKx6mj248N+QIGdb54BwSt5FeP7MXCOOufQYR08/e6eGCY2ib49gTCulawbm6dLebAoOnXwBZ3FEGjrDQWN+fiRst2jEn1x+131mlAbLX66fiLqN8gh5BvAkR3GciAQ+nHlRdIaNf7UImb0/WP4UbO43PkipysXpyvDV2fIdE3Zr+SPAADzh6IHRO5VXAAAAAElFTkSuQmCC") no-repeat 0 5px}#login .error{background: url("data:image/gif;base64,R0lGODlhDgAOANUAAAAAAP///8/Oz/WgU+2MTOl3P+B+Udt2TepoNuadg+iljeRdL9JWLOGYgN1WLtxMJ8xHJ9qOfMlROcBHMdRpVui5sbtDMNB6bd6wqb1XS7poYcKVkczBwPbq6bBbVapZVbSIhaU/Ovz5+fn29v39/fr6+vf39/Hx8e3t7ejo6Nzc3MjIyMfHx////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAC0ALAAAAAAOAA4AAAafwJZQVIlQKJGKSMjsXAyEwYBguHSYoyehgEAUqJdRi4Q5FAKKriJQOGBIpkthkQgk6olF4WI6ZRALDA0BaAwLCBknKBkLCw6DaA6NGSgoGg4OaxERAQ2YGignGxIPAREQEJwPEhsnJioeE6gPD6gTHiomJCgcHxYQtBAWHxwoJC0lKQIgHyEhHyACKSVMJSgqAissAioo1ExjfZWux0JBADs=") no-repeat 0 5px;}</style>
<form method="post" style="margin:40px auto" action="#action/login/login/1#">
<p><strong>用户名:</strong> <input size="30" type="text" value=""  name="username" autofocus="autofocus"/></p>
<p><strong>密码:</strong> <input size="30" type="password" value="" name="password"/></p>
<!--{if empty($_MCONFIG['noseccode'])}--><p style="width:418px"><strong>验证码:</strong> <input size="30" type="text" value="" onblur="chickseccode(this.value)" style="width:80px;margin:5px 5px 0 0" name="seccode"/> <span id="seccode"></span> <img src="#action/validate#" width="130" height="45" id="validate" title="点击刷新" onClick="this.src='#action/validate#-'+Math.random();"/></p><!--{/if}-->
<p><strong>有效期:</strong> <select class="input_select" name="cookietime" id="cookietime">
<option value="0">浏览器进程</option>
<option value="315360000">永久</option>
<option value="2592000">一个月</option>
<option value="86400">一天</option>
<option value="3600">一小时</option></select></p>			
<input class="input_search" type="submit" value="登 录" name="loginsubmit"/>
<input type="hidden" name="formhash" value="$formhash" /></form>
<script>function chickseccode(seccode){
	var seccodeHTML=document.getElementById("seccode");
	if(seccode==''){
		seccodeHTML.innerHTML='<i class="error"></i>';
	}else{					
		ajax({
			 method: 'POST',
			  url: '#action/ajax#',
			  data: {
				  formhash: '$formhash',
				  seccode: seccode,
				  seccodechick:true
				  },
		success: function (e) {seccodeHTML.innerHTML=e;}
		});	
	}	
}</script></div><!--{template 'footer'}-->