<?php exit()?><!--{template 'header'}--><div id="article_crumb"><a href="/">{$_MCONFIG[sitename]}</a> &gt; <a href="#action/category/catid/$news[catid]#">$thecat[$news[catid]][name]</a></div><div class="main"><h1 id="subject">$news[subject]</h1><article><time>#date('Y-m-d', $news[dateline],1)#(#date('Y-m-d', $news[dateline])#)</time><div id="content$news[id]" class="content"><!--{eval echo str_replace('src="/data/attachment/','src="'.MURL.'/data/attachment/',$news[content])}--><p>除非注明，<a href="{MURL}">{$_MCONFIG[sitename]}</a>的文章均为原创，转载请以链接形式标明本文地址：<a href="{MURL}#action/article/id/$news[id]#" title="$news[subject]">{MURL}#action/article/id/$news[id]#</a></p><p id="gopage"><!--{if $nexarticle}--><span>下一篇 &raquo;：$nexarticle</span><!--{/if}--><!--{if $prearticle}-->&laquo;上一篇：$prearticle<!--{/if}--></p></div></article><!--{if !empty($relatedarr)}--><section class="bottom"><h3>相关文章推荐阅读</h3><ul><!--{loop $relatedarr $vaule}--><li><a href="$vaule[url]">$vaule[subject]<br /><time datetime="#date('Y-m-d', $value['dateline'])#">#date('Y-m-d', $value['dateline'],1)#(#date('Y-m-d', $value['dateline'])#)</time> &nbsp; <span>阅读：$value[viewnum]</span> &nbsp; <span>评论：$value[replynum]</span><!--{if $value[good]>0}--> <span class="iconfont icon-good"> $value[good]</span><!--{/if}--></a></li><!--{/loop}--></ul><div class="clear"></div></section><!--{/if}--><aside><!--{template 'webright'}--></aside></div><div class="clear"></div><!--{template 'footer'}-->