        <div class="right-header">{lang}register{/lang}</div>
        <div class="right-content">
            <div class="right-content-inner">
                {if !$user}
                    <p>
                        {if $request && !$success}
                            <div class="error-box">
                                {foreach from=$errors item=error}
                                    {$error}<br />
                                {/foreach}
                            </div>
                        {/if}
                        {if $request && $success}
                            <div class="success-box">
                                {lang}register_success_email{/lang}
                            </div>
                        {/if}
                        {lang}register_info{/lang}<br /><br />
                        <form method="post">
                            {foreach from=$fields item=field}
                                {if $field->getType() == 'captcha'}
                                    {$field->getCaptchaHtml()}
                                {else}
                                    <input type="{$field->getType()}" placeholder="{$field->getDisplay()} {if $field->isRequired()}*{/if}" name="register[{$field->getName()}]"><br /><br />
                                {/if}
                            {/foreach}
                            <input type="hidden" name="do_register" value="1" />
                            <input type="submit" value="{lang}register{/lang}" class="highlight" />
                        </form>
                    </p>
                {else}
                    <p>{lang}register_already{/lang}</p>
                {/if}
            </div>
        </div>
        <div class="right-footer"></div>