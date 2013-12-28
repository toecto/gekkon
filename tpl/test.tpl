1Test template<br>
2qq{set $a = 1}<br>
3ww{$a}<br>
4ee{$a+=2}
5rr{$a.pow(2)}<br>
6
{if $a>3}
8tt{@ "String".strtoupper()}<br>
9yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}
11yy{@ 4.pow(2)}<br>
{if $a>3}
8tt{@ "String".strtoupper()}<br>
9yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}{if $a>3}
8tt{@ "String".strtoupper()}<br>
9yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}