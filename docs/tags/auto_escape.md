## Tag {auto_escape}

Controls the current auto-escaping behavior. This tag takes either `on` or `off` as an argument and that determines whether auto-escaping is in effect inside the tag. 

```smarty
	{auto_escape on}
		{$html_title}
	{/auto_escape}
```

Output:
```html
	&lt;h1&gt;Gekkon&lt;/h1&gt;
```

===

```smarty
	{auto_escape off}
		{$html_title}
	{/auto_escape}
```

Output:

```html
	<h1>Gekkon</h1>
```