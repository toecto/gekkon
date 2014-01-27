{#one line comment#}
Test template<br>
qq{set $a = 1}<br>
ww{$a}<br>
ee{$a+=2}
rr{$a.pow(2)}<br>
{#
Multiline
comment
 #}
 {{$a}}
{="Str".strtoupper()}
{set $arr=array('aaa','bbb','ccc','ddd')}
{set $arr_empty=array()}

<div>
    <b>Scope</b><br>
{set $local='outside';$lol=111}
{$local}<br>
{use array('lol'=>123)+@scope}
{$lol}
{$local}<br>
{set $local='inside'}
{$local}<br>
{/use}
{$lol}<br>
{$local}<br>
</div>
<table border>
    {foreach from=$arr item=$value key=$key meta=@data}
        
        <tr class="">
            <td>row{cycle "0", "1" data=$globalCycle}</td>
            <td>row{cycle "a","b","c"} </td>
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
    row{cycle "x","y","z" data=$globalCycle};{$key}=>{$value} {@data.print_r()}<br>
{/foreach}


<div>If else if</div>
{foreach $arr.array_reverse(true) as $key=>$value;@data}
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
{for $i=0; $i<65;$i++}
{ifchange}<div><b>{@ date('Y F',$now+$i*60*60*24)}</b></div>{/ifchange}
{echo 'd'.date($now+$i*60*60*24)}
{/for}
   
<div>for loop2</div>
{set $now=time()}
{cache timeout=3} 
{spaceless}
    {for from=0 key=$i to=65}
        {ifchange}<div><b>{@ date('Y F',$now+$i*60*60*24)}</b></div>{/ifchange}
        {echo 'd'.date($now+$i*60*60*24)}
    {/for}
{/spaceless}
{/cache}