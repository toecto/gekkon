## Tag {for}

Loop tag, similar `for` loop in PHP.<br>

```smarty
{for from=0 to=10 key=$i}
    {$i}
{/for}
```

This tag has a second syntax:

```smarty
{for $i=0; $i<10; $i++}
    {$i}
{/for}
```

The first expression `$i=0` is executed once unconditionally at the beginning of the loop.

In the beginning of each iteration, second expression `$i<10)` is evaluated. If it evaluates to `true`, the loop continues and the nested statement(s) are executed. If it evaluates to `false`, the execution of the loop ends.

At the end of each iteration, third expression `$i++` is evaluated (executed).