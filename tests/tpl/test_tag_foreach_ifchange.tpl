{foreach from=$date_list item=$date}{$date.day}{ifchange}={$date.month}{/ifchange}{/foreach}