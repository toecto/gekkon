{#one line comment#}
<h1>{block name}Gekkon{/block}</h1>
<h2>Test template</h2>

<h3>Variable</h3>
{set $a = 1}
Output: {$a}<br>
Output expression 1: {$a+=2}<br>
Output expression 2: {$a.pow(2)}<br>
Output expression 3: {="Str".strtoupper()}

<h3>Scope</h3>
{set $arr=array('aaa','bbb','ccc','ddd')}
{set $arr_empty=array()}
<div>
        {set $local='outside';$second=111}
        {$local}<br>
        {use array('second'=>123)+@scope}
        {$second}
        {$local}<br>
        {set $local='inside'}
        {$local}<br>
        {/use}
        {$second}<br>
        {$local}<br>
</div>

<h3>Foreach</h3>
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

<h3>Empty foreach</h3>
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

<h3>No parse</h3>
{no_parse}
<pre>
    {foreach from=$arr_empty item=$value key=$key meta=@data}
            {$key}
            {$value}
            {@data.print_r()}
        {empty}
            NoData
    {/foreach}
</pre>
{/no_parse}

<h3>Another foreach syntax</h3>
{foreach $arr as $key=>$value;@data}
    row{cycle "x","y","z" data=$globalCycle};{$key}=>{$value} {@data.print_r()}<br>
{/foreach}

<h3>If, else if</h3>
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


<h3>Ifchange</h3>
{set $arr2=array('aaa','aaa','ccc','ccc')}
{foreach $arr2 as $key=>$value}
    {$value} {ifchange $value}chaged{else}not changed{/ifchange}
    <br>
{/foreach}
    
<h3>For loop</h3>
{set $now=time()}
{for $i=0; $i<65;$i++}
{ifchange}<div><b>{@ date('Y F',$now+$i*60*60*24)}</b></div>{/ifchange}
{echo 'd'.date($now+$i*60*60*24)}
{/for}

<h3>For loop other syntax + cache</h3>
{cache timeout=3} {set $now=time()}
{spaceless}
    {for from=0 key=$i to=65}
        {ifchange}<div><b>{@ date('Y F',$now+$i*60*60*24)}</b></div>{/ifchange}
        {echo 'd'.date($now+$i*60*60*24)}
    {/for}
{/spaceless}
{/cache}
{#
Multiline
comment
 #}