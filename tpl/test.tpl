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
            <td></td>
            <td></td>
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