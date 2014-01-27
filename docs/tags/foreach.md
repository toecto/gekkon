## Tag {foreach}

Operator cycle iterates passed array.

You can use `{empty}` condition for ease of handling when the passed array is empty.

```smarty
	{foreach from=$data item=$item key=$key}
    	<li>{$item}</li>
	{empty}
     	Sorry, items not found...
	{/foreach}
```

This tag has a second syntax:

```smarty
	{foreach $data as $item}
    	<li>{$item}</li>
	{/foreach}
```
```smarty
	{foreach $data as $key=>$value}
    	<li>{$key}, {$value}</li>
	{/foreach}
```

Also, you can specify the `meta` parameter, where declare a variable which will be contain meta-information about the counters of iteration.

```smarty
	{foreach from=$data item=$item meta=$loop}
     	<li>{$loop.counter} {$item}</li>
     	{if $loop.last}
         	<li>...</li>
     	{/if}
	{empty}
     	Sorry, items not found...
	{/foreach}
```

or

```smarty
	{foreach $data as $item; $loop}
     	<li>{$loop.counter} {$item}</li>
     	{if $loop.last}
         	<li>...</li>
     	{/if}
	{empty}
     	Sorry, items not found...
	{/foreach}
```

| Meta-property | Description |
| :------ | :------ | 
| `$loop.counter` | Loop counter starts from `1`. |
| `$loop.counter0` | Loop counter starts from `0`. |
| `$loop.revcounter` | Reverse loop counter, starts from `1`. |
| `$loop.revcounter0` | Reverse loop counter, starts from `0`. |
| `$loop.first` | `true` if it's the first iteration |
| `$loop.last` | `true` if it's the last iteration |