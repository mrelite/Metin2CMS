{if $useServerStatus}
    <a href="?p=Downloads"><img class="left-button" src="{$resource_dir}/download.png" /></a>
    <a href="board/"><img class="left-button" src="{$resource_dir}/community.png" /></a>
    {include file='mt2base/serverstatus.tpl'}
    {include file='mt2base/top_list.tpl'}
{/if}