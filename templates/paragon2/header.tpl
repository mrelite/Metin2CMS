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
        <form action="index.php" method="get">
            <input id="user" type="text" />
            <input id="pass" type="password" />
            <input id="button-login" type="submit" value="" />
        </form>
    </div>

    {include file='navigation.tpl'}

    <div id="vote">

    </div>
    <div id="content">
        <div id="content-left">
            {include file="sidebar_left.tpl"}
        </div>
        <div id="content-right">