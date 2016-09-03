<?php
/**
 *
 * @author Thomas Hirter <t.hirter@outlook.com>
 */

/**
 * Zeitzone festlegen
 */
date_default_timezone_set('Europe/Zurich');
define("ROOT", __DIR__);

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */
require_once(__DIR__."/libs/pokemon.php");
require_once(__DIR__."/libs/telegram.php");
require_once(__DIR__."/libs/cPokemon.php");
require_once(__DIR__."/libs/cChat.php");
require_once(__DIR__."/libs/cNotifylist.php");
require_once(__DIR__."/libs/cNotified.php");

$cPokemon = new cPokemon();
$cChat = new cChat();
$cNotifylist = new cNotifylist();
$cNotified = new cNotified();



require_once(__DIR__."/libs/database.php");
$db = new DB();










$cfg = parse_ini_file(__DIR__."/config/config.ini", TRUE);
$pokemon = new Pokemon(__DIR__."/pokemon.json");
$telegram = new Telegram($cfg['telegram']['bot-id']);

