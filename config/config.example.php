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

// comment out this to activate debug
// $GENERAL["debug"] = true;