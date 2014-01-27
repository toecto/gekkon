## Tag {spaceless}

Removes spaces between HTML tags, including tabs and newlines.

```smarty
{spaceless}
    <p>
        <a href='/'>Home </a>
    </p>
{/spaceless}
```

Result:

```html
<p><a href='/'>Home </a></p>
```