Extending template
{extends "test.tpl"}
{block name}
    <div>
New block
{include "test.tpl" block="name"}
</div>
{/block}