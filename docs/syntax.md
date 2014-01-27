# Gekkon Syntax

Gekkon syntax similar to the [Smarty](http://www.smarty.net/) with some differences.

A template contains variables or expressions, which get replaced with values when the template is evaluated, and tags, which control the logic of the template.

- Use `{$global_variable}` or `{@local_variable}` markup in your template files to display the value of a variable defined in the PHP backend of your project (or in the template).

- Use `{tagName}...HTML content...{/tagName}` markup in your template files to display dynamic content generated through foreach loops or conditional statements.


## Variables

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



## Functions

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


## Expressions

	{$var + 1}
	{$var1 * $var2}
	
You can use the result of the expressions when accessing an associative array. To do this, you must enclose the expression in parentheses `()`.

	{$var.($arr.0)}
	{$var.($arr.1.foo())}
	{$var.($arr.1.foo()).bar()}


## Template tags

All template tags are contained within the structure.

	{tag_name}

Tags may contain parameters:

	{tag arg}
	{tag arg1 arg2}

including a key-value format:

	{tag arg_name=$arg_value}
	{tag arg_name=$var.0}
	{tag arg_name1=$arg_value1 arg_name2=$arg_value2}

> Order of the parameters does not matter


### Gekkon built-in tags:

* [auto_escape](./tags/auto_escape.md)
* [cache](./tags/cache.md)
* [comment](./tags/comment.md)
* [cycle](./tags/cycle.md)
* [echo](./tags/echo.md)
* [for](./tags/for.md)
* [foreach](./tags/foreach.md)
* [if](./tags/if.md)
* [ifchange](./tags/ifchange.md)
* [include](./tags/include.md)
* [no_parse](./tags/no_parse.md)
* [set](./tags/set.md)
* [spaceless](./tags/spaceless.md)
* [static](./tags/static.md)
* [use](./tags/use.md)