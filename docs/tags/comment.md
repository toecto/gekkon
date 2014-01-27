## Tag {comment}, {#}

Ignoring all code between the opening and closing `comment` tags.
This tag doesn't support nesting.

```smarty
{comment}
    <p>This is commented and don’t render. {$foo} {set $var=true}</p>
{/comment}
```

Have a short form `#`.

```smarty
{# <p>This is commented and don’t render. {$foo} {set $var=true}</p> #}
```