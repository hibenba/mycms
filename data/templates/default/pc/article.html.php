<?php exit()?><!--{template 'header'}--><div id="article_crumb"><a href="/">{$_MCONFIG[sitename]}</a> &gt; <a href="#action/category/catid/$news[catid]#">$thecat[$news[catid]][name]</a> &gt; $news[subject]</div><div class="main"><div class="web_left"><div id="title"><time>#date('Y-m-d', $news[dateline],1)#(#date('Y-m-d', $news[dateline])#)</time><h1>$news[subject]</h1></div><article><div id="content$news[id]" class="content">$news['content']<p>除非注明，<a href="{MURL}">{$_MCONFIG[sitename]}</a>的文章均为原创，转载请以链接形式标明本文地址：<a href="{MURL}#action/article/id/$news[id]#" title="$news[subject]">{MURL}#action/article/id/$news[id]#</a></p><div class="attsub"><span class="good"><a href="javascript:like($news[id],$news[good])"><i class="iconfont icon-good"></i> 赞 <em id="good$news[id]">$news[good]</em></a></span><span class="reward"><a href="javascript:reward()">赏</a></span><span class="share"><a href="javascript:share()"><i class="iconfont icon-share"></i> 分享</a></span><script>bdshare();</script><div class="clear"></div></div><p id="gopage"><!--{if $nexarticle}--><span>下一篇 &raquo;：$nexarticle</span><!--{/if}--><!--{if $prearticle}-->&laquo;上一篇：$prearticle<!--{/if}--></p></div></article><!--{if !empty($relatedarr)}--><div class="relatedarr"><h3>《$news[subject]》相关文章推荐阅读</h3><ul><!--{loop $relatedarr $vaule}--><li><a href="$vaule[url]">$vaule[subject]</a></li><!--{/loop}--></ul><div class="clear"></div></div><!--{/if}--><comment><h2>《$news[subject]》的网友评论($count)</h2><!--{if $count>0}--><ol class="user_comments"><!--{loop $comments $comment}--><li class="comment" id="comment$comment[cid]"><span>#$comment[i]</span>$comment[message]<div class="user_show"><!--{if $comment[hideauthor]}-->佚名<!--{else}-->$comment[username]<!--{/if}-->于#date('Y-m-d', $comment[dateline],1)#(#date('Y-m-d', $comment[dateline])#)发表。 <a href="javascript:hot($comment[cid],$comment[hot]);"><i class="iconfont icon-good"></i></a> (<em id="hot$comment[cid]">$comment[hot]</em>)</div></li><!--{/loop}--></ol><!--{/if}--><script>comments($news[id]);</script><script>function hot(cid,num){var hotid=document.getElementById('hot'+cid);num++;ajax({method:'GET',url:'#action/ajax/hot#-'+cid,success:function(e){eval(e);}});}function like(aid,num){var goodid=document.getElementById('good'+aid);num++;ajax({method:'GET',url:'#action/ajax/good#-'+aid,success:function(e){eval(e);}});}window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"32"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];</script></comment></div><aside class="web_right"><!--{template 'webright'}--></aside></div><div class="clear"></div><div id="reward_show" onclick="this.style.display='none'"><img src="/static/reward.jpg" alt="感谢打赏！"/></div><!--{template 'footer'}-->