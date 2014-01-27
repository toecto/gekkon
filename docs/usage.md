##Usage

Include the `gekkon.php` in your project.

```php
include "gekkon/gekkon.php";
```
	
or if you use [Composer](http://getcomposer.org/):

```php
require "gekkon/autoload.php";
```

Create a new instance of the Gekkon class passing the configuration parameters:

- `$tpl_path` — Full path to template directory. (need read permission)
- `$tpl_cache_path` — Full path to compiled templates directory. (need access to read and write)

All paths must ends with `/`.

```php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$gekkon = new Gekkon($path.'tpl/', $path.'/tpl_cache/');
```

Use the `register()` method to register variables to the template engine, so designers can use them in their template files:

```php
	$gekkon->register('variableName', $variableValue);
```

Use the `display()` method to display template by name:

```php
	$gekkon->display('main.tpl');
```