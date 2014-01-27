## Basic usage

Include the `gekkon.php` in your project.

```php
include "gekkon/gekkon.php";
```

or if you use [Composer](http://getcomposer.org/):

```php
require "gekkon/autoload.php";
```

## Initialize Gekkon

Create a new instance of the Gekkon class passing the configuration parameters:

- Full path to template directory. (need read permission)
- Full path to compiled templates directory. (need access to read and write)

All paths must ends with `/`.

```php
$gekkon = new Gekkon('/path/to/templates/', '/path/to/compiled/templates/');
```

## Render template

Use the `display()` method to display template by name:

```php
$gekkon->display('main.tpl', $vars);
```

Use the `get_display()` method to save output into the variable:

```php
$output = $gekkon->get_display('main.tpl', $vars);
```

