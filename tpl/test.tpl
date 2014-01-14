{set $arr=array('aaa','bbb','ccc','ddd')}
 {foreach $arr as $tag; $loop}
<span>{$tag}{if !$loop.last},{else}.{/if}</span>
{/foreach}