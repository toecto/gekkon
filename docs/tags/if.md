## Tag {if}

This tag checks variable value and if the variable is `true` (variable is exists, is not empty and it's not `false`) displays the contents of the block.

Optionally, can be expanded by `elseif` and `else` tags.


```smarty
{if $value == 0}
    ...
{elseif $value == 1}
    ...
{else}
    ...
{/if}
```