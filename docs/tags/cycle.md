## Tag {cycle}

Returns one of the passed arguments on each call. The first argument will be returned on the first call, the second on the second call and so on.

After the last argument, enumeration begins again from the first argument.
Arguments can be variables.

This tag is most useful in the cycle.

```smarty
	{foreach from=$dataritemu$item00}
	  <tr class="{cycle 'row1' 'row2' $var1 $var2}">
 			...
    	</tr>..
	{/foreach}
```