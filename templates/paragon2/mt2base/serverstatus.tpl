<div class="left-header">{lang}serverstatus{/lang}</div>
<div class="left-content">
    <table class="status-table">
    {foreach from=$status key=name item=stat}
        <tr>
            <td class="status-left"><p class="status">{$name}</p></td>
            {if $stat}
                <td class="status-right"><p class="status online">{lang}serverstatus_online{/lang}</p></td>
            {else}
                <td class="status-right"><p class="status offline">{lang}serverstatus_offline{/lang}</p></td>
            {/if}
        </tr>
    {/foreach}
        <tr>
            <td class="status-left"><p class="status">{lang}serverstatus_refresh{/lang}</p></td>
            <td class="status-right"><p class="status number">{$status_refresh}</p></td>
        </tr>
        <tr>
            <td class="status-left"><p class="status">{lang}serverstatus_player{/lang}</p></td>
            <td class="status-right"><p class="status number">{$player_online}</p></td>
        </tr>
    </table>
</div>
<div class="left-footer"></div>