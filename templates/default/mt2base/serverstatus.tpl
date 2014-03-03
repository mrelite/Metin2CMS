<div id="sidebar-left">
    <div class="box">
        <div class="box-title">Serverstatus</div>
        <div class="box-middle">
            <table style="width: 100%;">
            {foreach from=$status key=name item=stat}
                <tr>
                    <td>{$name}</td>
                    {if $stat}
                        <td class="serverstatus"><p><img src="{$resource_dir}online.png" /></p></td>
                    {else}
                        <td class="serverstatus"><p><img src="{$resource_dir}offline.png" /></p></td>
                    {/if}
                </tr>
            {/foreach}
            </table><br />
            Last refresh: {$status_refresh}
        </div>
        <div class="box-bottom"></div>
    </div>
</div>