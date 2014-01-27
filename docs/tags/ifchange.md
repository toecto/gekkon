## Tag {ifchange}

Check if a value has changed from the last loop iteration.
This tag is used within a foreach loop and has two possible use cases:

Checks its own rendered contents against its previous state and only displays the content if it has changed:

```smarty
{foreach from=$days item=$date}
    {ifchange}<b>{$date.month}</b>{/ifchange}
{/foreach}
```

If given one or more variables, check whether any variable has changed:

```smarty
{foreach from=$days item=$date}
    {ifchange $date.month}...{/ifchange}
{/foreach}
```

Also you can use an optional `{else}` condition:

```smarty
{foreach from=$days item=$date}
    {ifchange $date.month}
        {cycle 'row1' 'row2'}
    {else}
        row3
    {/ifchange}
{/foreach}
```