Test template<br>
qq{set $a = 1}<br>
ww{$a}<br>
ee{$a+=2}
rr{$a.pow(2)}<br>
{@ "Str".strtoupper()}
{set $arr=array('aaa','bbb','ccc','ddd')}
{set $arr_empty=array()}
<table border>
    {foreach from=$arr item=$value key=$key meta=@data}
        <tr class="">
            <td>row{cycle "0", "1";$globalCycle}</td>
            <td>row{cycle "a","b","c"}</td>
            <td>{$key}</td>
            <td>{$value}</td>
            <td>{@data.print_r()}</td>
        </tr>
        {empty}
        <tr>
            <td>NoData</td>
        </tr>
    {/foreach}
</table>

<table border>
    {foreach from=$arr_empty item=$value key=$key meta=@data}
        <tr>
            <td>{$key}</td>
            <td>{$value}</td>
            <td>{@data.print_r()}</td>
        </tr>
        {empty}
        <tr>
            <td>NoData</td>
        </tr>
    {/foreach}
</table>

{no_parse}Wrong placed {empty}{/no_parse}
{empty}

<div>Another foreach syntax</div>
{foreach $arr as $key=>$value;@data}
    row{cycle "x","y","z";$globalCycle};{$key}=>{$value} {@data.print_r()}<br>
{/foreach}


<div>If else if</div>
{foreach $arr as $key=>$value;@data}
    {$key}
    {if $key==0}
        Key is zero!
    {elseif  $key==1}
        Key is one!
    {elseif  $key==2}
        Key is two!
    {else}
        Else!
    {/if}
    <br>
{/foreach}



{set $arr2=array('aaa','aaa','ccc','ccc')}
<div>Ifchange</div>
{foreach $arr2 as $key=>$value}
    {$value} {ifchange $value}chaged{else}not changed{/ifchange}
    <br>
{/foreach}
    
    
    
<div>for loop</div>
{set $now=time()}
{for $i=0; $i<365;$i++}
{ifchange}<div><b>{@ date('Y F',$now+$i*60*60*24)}</b></div>{/ifchange}
{echo 'd'.date($now+$i*60*60*24)}
{/for}
