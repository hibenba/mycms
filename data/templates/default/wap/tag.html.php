<?php exit()?><!--{template 'header'}--><div id="article_crumb"><a href="/">{$_MCONFIG[sitename]}</a> &gt; 标签：$tag['tagname']</div><div class="main"><div id="title"><h1>关于<strong>“$tag['tagname']”</strong>的文章</h1></div><ul id="tag$tag['tagid']" class="tag"><!--{loop $articlearr $value}--><li><a href="$value['url']">$value['subject']</a></li><!--{/loop}--></ul><aside><!--{template 'webright'}--></aside></div><div class="clear"></div><!--{template 'footer'}-->