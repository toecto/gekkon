Test template<br>
qq{set $a = 1}<br>
ww{$a}<br>
ee{$a+=2}
rr{$a.pow(2)}<br>

{if $a>3.}
tt{@ "String".strtoupper()}<br>
yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}
yy{@ 4.pow(2)}<br>
{if $a>3}
tt{@ "String".strtoupper()}<br>
yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}{if $a>3}
tt{@ "String".strtoupper()}<br>
yy{@ 4.pow(2)}{@ 4.pow(2)}<br>
{/if}