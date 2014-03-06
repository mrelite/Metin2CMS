<div id="navi">
    <ul>
        {foreach from=$navigation_points item=link key=name}
            <li>
                <a href="{$link}">{$name}</a>
            </li>
        {/foreach}
    </ul>
</div>