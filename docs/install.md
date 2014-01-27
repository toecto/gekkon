## Simple Install

- Download the [latest Gekkon release](https://github.com/toecto/gekkon/releases/) in ZIP/GZIP package and extract it to your project directory.
- Create an empty directory to store your template files.
- Create another empty directory to store your compiled versions (cache) of templates.

Include the `gekkon.php` in your project.

```php
include "gekkon/gekkon.php";
```


## Install via [Composer](http://getcomposer.org/)

Add Gekkon to your `composer.json` requirements:

```json
{
    "require": {
        "gekkon/gekkon": "4.*"
    }
}
```

Install...

```bash
php composer.phar install
```

...and load gekkon in your project:

```php
require "gekkon/autoload.php";
```
