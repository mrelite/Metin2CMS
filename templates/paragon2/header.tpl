<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Paragon 2</title>
    <link rel="stylesheet" type="text/css" href="{$resource_dir}main.css" />
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js" type="text/javascript"></script>
</head>

<body>
<div id="main">
    <div id="logo">

    </div>

    <div id="login">
        {if !$user}
            <form method="post">
                <input name="login_user" id="user" type="text" />
                <input name="login_pwd" id="pass" type="password" />
                <input id="button-login" type="submit" value="" />
            </form>
            {if !$login_error}
                <b style="color: red;">{$login_error}</b>
            {/if}
        {else}
            <p>{lang login=$user->getLogin()}user_loggedinas{/lang}</p>
        {/if}
    </div>
    
    {include file='navigation.tpl'}

    <div id="vote">

    </div>
    <div id="content">
        <div id="content-left">
            {include file="sidebar_left.tpl"}
        </div>
        <div id="content-right">