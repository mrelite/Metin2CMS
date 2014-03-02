{foreach from=$news item=entry}
    <b class="news-title">{$entry["title"]}</b>
    {$entry["content"]}
    <b class="news-footer">written by {$entry["author"]}</b>
{/foreach}