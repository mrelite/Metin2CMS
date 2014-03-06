<div class="left-header">Serverstatus</div>
<div class="left-content">
    <table class="status-table">
    {foreach from=$status key=name item=stat}
        <tr>
            <td class="status-left"><p class="status">{$name}</p></td>
            {if $stat}
                <td class="status-right"><p class="status online">Online</p></td>
            {else}
                <td class="status-right"><p class="status offline">Offline</p></td>
            {/if}
        </tr>
    {/foreach}
        <tr>
            <td class="status-left"><p class="status">Aktualisiert</p></td>
            <td class="status-right"><p class="status number">{$status_refresh}</p></td>
        </tr>
        <tr>
            <td class="status-left"><p class="status">Spieler online</p></td>
            <td class="status-right"><p class="status number">{$player_online}</p></td>
        </tr>
    </table>
</div>
<div class="left-footer"></div>