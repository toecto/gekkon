#Quick-Start Guide & Code Reference
***
Gekkon is an easy-to-use & fast PHP template engine that allows you to separate your HTML code from your PHP code.<br>
This results in cleaner code that's easier to read and maintain.<br>

## Features
- Includes
- Loops
- Conditions
- Caching
- Debugging
- Сalling PHP functions

##Installation

- Download the Gekkon ZIP package and extract it to your project directory.
- Create an empty directory to store your template files.
- Create another empty directory to store your compiled versions of templates.

##Usage
Include the `gekkon.php` in your project.

	include "gekkon/gekkon.php";

Create a new instance of the Gekkon class passing the configuration parameters:

- `$gekkon_path` — Full path to the Gekkon directory.
- `$template_path` — Full path to template directory. (need read permission)
- `$bin_path` — Full path to compiled templates directory. (need access to read and write)

All paths must end with `/`.

	$path = $_SERVER['DOCUMENT_ROOT'];
	$gekkon = new Gekkon($path.'/gekkon/', $path.'/tpl_bin/', $path.'tpl/');

##For Developers
Use the `register()` method to assign variables to the template engine, so designers can use them in their template files:

	$gekkon->register('variableName', $variableValue);

Use the `display()` method to display template by name:

	$gekkon->display('main.tpl');

##For Designers
A template contains variables or expressions, which get replaced with values when the template is evaluated, and tags, which control the logic of the template.

- Use `{$variableName}` markup in your template files to display the value of a variable defined in the PHP backend of your project.
- Use `{tagName}...HTML content...{/tagName}` markup in your template files to display dynamic content generated through foreach loops or conditional statements.

#### Variables

| Template syntax | Description | PHP equivalent |
|:----------------|:------------|:-------------|
| `{$var}`        | Display variable value. | `$var;` |
| `{$var.0}`<br>`{$var.1.2}`      | Access to simple array values by index.| `$var[0];`<br>`$var[1][2];`|
| `{$var.key_name}`, `{$var.key_name.key_name}` | Access to associative array values by key name. | `$var['key_name'];` `$var['key_name']['key_name'];` |

Also, you can use variables to access the value of an associative array as a key:

| Template syntax | PHP equivalent |
|:------------|:-------------|
| `{$var.$key_name}` | `$var[$key_name];` |
| `{$var.$key_name1.$key_name2}` | `$var[$key_name1][$key_name2];` |
| `{$var.$key_name&$sub_key_name}` | `$var[$key_name[$sub_key_name]];` |
| `{$var.$key_name1&$sub_key_name1.$key_name2}` | `$var[$key_name1[$sub_key_name1]][$key_name2];` |

#### Functions

You can call functions (including built-in PHP):

| Template syntax | PHP equivalent |
|:------------|:-------------|
| `{$var.foo()}`| `foo($var);` |
| `{$var.foo().bar()}`| `bar(foo($var));` |
| `{$var.isset()}` | `isset($var);` |
| `{$str.strlen()}` | `strlen($str);` |

object methods (without passing parameters):

	{$obj->method_name()}

> To access the object methods with parameters, use `{set}` template tag.

and object fields:

	{$obj->field_name}

#### Expressions 

	{$var + 1}
	{$var1 * $var2}
	
You can use the result of the expressions when accessing an associative array. To do this, you must enclose the expression in parentheses `()`.

	{$var.($arr.0)}
	{$var.($arr.1.foo())}
	{$var.($arr.1.foo()).bar()}
	
### Template tags

All template tags are contained within the structure.

	{tag_name}

Tags may contain parameters:

	{tag param}
	{tag param1 param2}

including a key-value format:

	{tag key=$value}
	{tag key=$var.0}
	{tag key1=$value1 key2=$value2}

> Order of the parameters does not matter

## Built-in template tags

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

<br>**{include}**<br>
Includes a template by file name. Template name can be variable.

	{include catalog.tpl}
	{include $catalog_tpl}


<br>**{php}...{/php}**<br>
Inserts native PHP code. <br>
Access to template variable can be derived from array `$this->data['var_name']`.

	{php}
		echo $this->data['var_name'];
	{/php}


<br>**{cache}...{/cache}**<br>
In addition to compiling the tag's contens is executed and stored as the result HTML code.

	{cache}
		...
	{/cache}


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


<br>**{for}...{/for}**<br>
Cycle operator. Works similarly to the operator in php.<br>

	{for from=1 to=10 key=$key}
    	<li>{$key}</li>
	{/for}


<br>**{foreach}...{empty}...{/foreach}**<br>
Iterates passed array.<br>
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

Also you can use an optional `{else}` condition:

	{foreach from=$days item=$date}
		{ifchange $date.month}
			{cycle 'row1' 'row2'}
		{else}
			row3
		{/ifchange}
	{/foreach}
