<!DOCTYPE html>
<html>
    <head>
        <title>{$config["general_name"]}</title>
        <link rel="stylesheet" type="text/css" href="{$resource_dir}main.css" />
        <meta charset="utf-8">
    </head>
    <body>
        <div id="container">
            {include file='navigation.tpl'}
            <div id="logo"><a href="index.php"><img src="{$resource_dir}logo.png" alt="Logo" /></a></div>
            <div id="page-content">
                <div class="top"></div>
                <div class="middle">
                    {include file="sidebar_left.tpl"}
                    <div id="sidebar-middle">
                        <div class="box-top"></div>
                        <div class="box-middle">