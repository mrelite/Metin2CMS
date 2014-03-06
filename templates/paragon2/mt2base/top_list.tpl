<div class="left-header">Rangliste</div>
<div class="left-content">
    <table class="ranking-table">
        <tr>
            <td><p class="ranking text">Level</p></td>
            <td><p class="ranking text">Name</p></td>
            <td><p class="ranking text">Reich</p></td>
        </tr>
        {foreach from=$ranking_top item=player}
            <tr>
                <td class="ranking level"><p class="ranking text">{$player['level']}</p></td>
                <td class="ranking name"><p class="ranking text">{$player['name']}</p></td>
                <td class="ranking flag"><img class="ranking flagimg" src="{$resource_dir}flags/empire_{$player['empire']}.png" /></td>
            </tr>
        {/foreach}
    </table>
</div>
<div class="left-footer"></div>