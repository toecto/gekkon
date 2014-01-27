## Tag {no_parse}, {static}

Stops compiling the contents enclosed in this tag.

Conveniently used to insert JavaScript code that may conflict with Gekkon syntax.

This tag has alias `{static}`.

```smarty
{no_parse}
    <script type="text/javascript">...JS code...</script>
{/no_parse}
```

or

```smarty
{static}
    <script type="text/javascript">...JS code...</script>
{/static}
```