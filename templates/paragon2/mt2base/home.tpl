{foreach from=$news item=entry}
    <div class="right-header">News</div>
    <div class="right-content">
        <div class="right-content-inner">
            <img src="{$resource_dir}surahead.png" class="content-img" />
            <h2>{$entry["title"]}</h2>
            <p>{$entry["content"]}</p>
            <p class="content-cred">Geschrieben von {$entry["author"]}</p>
        </div>
    </div>
    <div class="right-footer"></div>

{/foreach}