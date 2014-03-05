<?php

/**
 * Equals:
 * account: localhost, mt2, mt2, account
 * player: 1.1.1.1, root, , player_server_x
 * common: localhost, root, , common
 * homepage: localhost, root, , homepage
 * log: log, root, , log
 */

// for every connection if not overwritten by another connection
$MySQL["*"]["host"] = "localhost";
$MySQL["*"]["user"] = "root";
$MySQL["*"]["password"] = "";
$MySQL["*"]["database"] = "%usage%";       // Will replaced
// account connection
$MySQL["account"]["host"] = "localhost";
$MySQL["account"]["user"] = "mt2";
$MySQL["account"]["password"] = "mt2";
// player connection
$MySQL["player"]["host"] = "1.1.1.1";
$MySQL["player"]["user"] = "root";
$MySQL["player"]["password"] = "";
$MySQL["player"]["database"] = "player_server_x";

// comment out this to activate debug. Attention! If debug mode is true it is possible to see the mysql password if connection failed
// $GENERAL["debug"] = true;

// Design directory name
$GENERAL["design"] = "default";

// Servername
$GENERAL["name"] = "ExampleMt2";

// Metin2Base Plugin Configuration
$PLUGINS["mt2base"]["load_type"] = "intern";                // Possible: intern, wbb
$PLUGINS["mt2base"]["wbb_board_id"] = 1;                    // Only needed if loadtype is wbb
$PLUGINS["mt2base"]["server_timeout"] = 0.5;                // Timeout after 0.5 seconds (for server status check)
$PLUGINS["mt2base"]["refresh_interval"] = 5 * 60;           // 5 * 60 seconds (5 Minutes)
$PLUGINS["mt2base"]["user_count_method"] = "mysql";         // Use mysql query to calculate players online or api function
$PLUGINS["mt2base"]["rankings_refresh_interval"] = 60 * 60; // Refresh time for caching rankings
$PLUGINS["mt2base"]["rankings_cache_count"] = 200;          // How much player should maximum cached
$PLUGINS["mt2base"]["rankings_top_count"] = 10;             // How much player should view on the top list