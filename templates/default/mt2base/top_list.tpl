<div class="box">
    <div class="box-title">Rangliste</div>
    <div class="box-middle">
        <table style="width: 100%;">
            <tr>
                <th>Platz</th>
                <th>Name</th>
                <th>Level</th>
                <th>Reich</th>
            </tr>
            {foreach from=$ranking_top item=player}
                <tr>
                    <td>{$player['place']}</td>
                    <td>{$player['name']}</td>
                    <td>{$player['level']}</td>
                    <td>
                        {if $player['empire'] == 1}
                            Shinsoo
                        {elseif $player['empire'] == 2}
                            Chunjo
                        {else}
                            Jinno
                        {/if}
                    </td>
                </tr>
            {/foreach}
        </table>
    </div>
    <div class="box-bottom"></div>
</div>