{foreach from=$items item=$item key=$key meta=$loop}{$key}={$item}{if !$loop.last}|{/if}{/foreach}