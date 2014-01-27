#Settings
Gekkon has some settings that you can change after initialization.

```php
	$gekkon = new Gekkon($path.'tpl/', $path.'/tpl_cache/');
	$gekkon->settings['auto_escape'] = true;
```

## auto_escape

HTML-escape each variables outputs.

(**By default is False**)


Affects performance.


## tag_systems

Templating syntax setting.

(**By default, uses a Smarty-like syntax.**)

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

```
	{% foreach from=$data item=$item meta=$loop %}
     	<li>{{ $loop.counter }} {{ $item }}</li>
     	{% if $loop.last %}
         	<li>...</li>
     	{% /if %}
	{% empty %}
     	Sorry, items not found...
	{% /foreach %}
```
