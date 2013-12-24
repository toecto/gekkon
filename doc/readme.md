# Gekkon template engine

### Built-in template tags
***
**{comment}...{/comment}**, **{# .. #}**<br>
Ignoring all code between the opening and closing `comment` tags.<br>
Have a short form `#`.<br>
This tag doesn't support nesting.

	{comment}
   		<p>This is commented and don’t render. {$foo} {now}</p>
	{/comment}
	
	{# <p>This is commented and don’t render. {$foo} {now}</p> #}


<br>**{csrf_token}**<br>
This tag is used to protect against CSRF-attacks. <br>
Displays hidden input containing the token.

	{csrf_token}
	
	// <input type='hidden' name='csrf_token' value='ZIPpLNMVYXEsKhYrT7mbnlnddLXfsn5k' />`


<br>**{cycle}**<br>
Returns one of the passed arguments on each call. The first argument will be returned on the first call, the second on the second call and so on.<br>
After the last argument, enumeration begins again from the first argument.
Arguments can be variables.<br>
This tag is most useful in the cycle.

	{foreach from=$data item=$item}
	    <tr class="{cycle 'row1' 'row2' $var1 $var2}">
 			...
    	</tr>
	{/foreach}

<br>**{debug}**<br>
Displays all debugging information, including the current context.
	
	{debug}


<br>**{load}**<br>
Loads & register custom tag library in template scope.

	{load news_tags.php}
	
	// Or without php extension.
	{load news_tags}


<br>**{now}**<br>
Displays the current date and/or time, with the ability to specify date format.

	{now}
	{now 'Y-m-d H:i:s'}


<br>**{spaceless}...{/spaceless}**<br>
Removes spaces between HTML tags, including tabs and newlines.

	{spaceless}
    	<p>
       		<a href='/'>Home </a>
    	</p>
	{/spaceless}

Result:
	
	<p><a href='/'>Home </a></p>


<br>**{static}...{/static}**<br>
Stops compiling the contents enclosed in this tag.<br>
Conveniently used to insert JavaScript code that may conflict with Gekkon syntax.

	{static}
    	<script type="text/javascript">...JS code...</script>
	{/static}



<br>**{foreach}...{empty}...{/foreach}**<br>
Operator cycle iterates passed array.<br>
You can use `{empty}` condition for ease of handling when the passed array is empty.

	{foreach from=$data item=$item key=$key}
    	<li>{$item}</li>
	{empty}
     	Sorry, items not found...
	{/foreach}

Also, you can specify the `meta` parameter, where declare a variable which will be contain meta-information about the counters of iteration.

	{foreach from=$data item=$item meta=$loop}
     	<li>{$loop.counter} {$item}</li>
     	{if $loop.last}
         	<li>...</li>
     	{/if}
	{empty}
     	Sorry, items not found...
	{/foreach}

| Meta-property | Description |
| :------ | :------ | 
| `$loop.counter` | Loop counter starts from `1`. |
| `$loop.counter0` | Loop counter starts from `0`. |
| `$loop.revcounter` | Reverse loop counter, starts from `1`. |
| `$loop.revcounter0` | Reverse loop counter, starts from `0`. |
| `$loop.first` | `true` if it's the first iteration |
| `$loop.last` | `true` if it's the last iteration |



<br>**{if}...{elseif}...{else}..{/if}**<br>
This tag checks variable value and if the variable is `true` (variable is exists, is not empty and it's not `false`) displays the contents of the block.<br>
Optionally, can be expanded by `elseif` and `else` tags.

	{if $value == 0}
    	...
	{elseif $value == 1}
    	...
	{/if}


<br>**{link}**<br>
This tag generates HTML tags for including scripts and styles (based on file extension checking). <br>
Optionally, you can specify the following params:

| param | description |
| :--- | :--- |
|`nocache` | Adds to the query string, time of last file modification (timestamp).|
|`min` | Adds `.min` between name and extension of the file (If min-version is exist).|


	{link '/css/style.css'}  
	// <link href="/css/style.css" rel="stylesheet" type="text/css"/>

	{link '/js/common.js'}  
	// <script src="js/common.js"></script>

	{link '/js/common.js' nocache}  
	// <script src="js/common.js?t=1387906949"></script>

	{link '/js/common.js' min nocache}  
	// <script src="js/common.min.js?е=1387906949"></script>



<br>**{set}**<br>
Declares a global variable.

	{set $post_total = blog.post.count()}



<br>**{with}**<br>
Declares a local variable is accessible inside the tag `with`.
You can assign several variables at once.

	{with $post_total = blog.post.count()}
    	Total {$post_total} news.
	{/with}

	{with $alpha = 1 $beta = 2}
    	...
	{/with}


<br>**{ifchange}**<br>
Check if a value has changed from the last loop iteration.
This tag is used within a foreach loop and has two possible use cases:

Checks its own rendered contents against its previous state and only displays the content if it has changed:

	{foreach from=$days item=$date}
		{ifchange}<b>{$date.month}</b>{/ifchange}
	{/foreach}

If given one or more variables, check whether any variable has changed:

	{foreach from=$days item=$date}
		{ifchange $date.month}...{/ifchange}
	{/foreach}

Also you can take an optional `{else}` condition:

	{foreach from=$days item=$date}
		{ifchange $date.month}
			{cycle 'row1' 'row2'}
		{else}
			row3
		{/ifchange}
	{/foreach}

