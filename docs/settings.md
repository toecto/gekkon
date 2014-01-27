#Settings
Gekkon has some settings that you can change after initialization.

```php
	$gekkon = new Gekkon($path.'tpl/', $path.'/tpl_cache/');
	$gekkon->settings['auto_escape'] = true;
```

## auto_escape
**By default is False**

HTML-escape each variables outputs.
Decreases performance.


## tag_systems

**By default, uses a Smarty-like syntax.**

Templating syntax setting. 


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

You can specify the characters for open and closing tags:

```php
	$gekkon = new Gekkon($path.'tpl/', $path.'/tpl_cache/');
	$gekkon->settings['tag_systems']['echo'] = array(
		'open' => '{{',
        'close' => '}}',
	);
	$gekkon->settings['tag_systems']['common'] = array(
		 'open' => '{%',
        'close' => '%}',
	);
```

And then, to use the new syntax in your templates:

```django
	{% foreach from=$data item=$item meta=$loop %}
     	<li>{{ $loop.counter }} {{ $item }}</li>
     	{% if $loop.last %}
         	<li>...</li>
     	{% /if %}
	{% empty %}
     	Sorry, items not found...
	{% /foreach %}
```
