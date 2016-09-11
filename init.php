<?php
/**
 * Initialisierung aller benötigten resourcen und classen
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */

/**
 * Zeitzone festlegen
 */
date_default_timezone_set('Europe/Zurich');
define("ROOT", __DIR__);

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */
$cfg = parse_ini_file(__DIR__."/config/config.ini", TRUE);

require_once(__DIR__."/libs/pokemon.php");
require_once(__DIR__."/libs/telegram.php");
require_once(__DIR__."/libs/cPokemon.php");
require_once(__DIR__."/libs/cChat.php");
require_once(__DIR__."/libs/cNotifylist.php");
require_once(__DIR__."/libs/cNotified.php");
require_once(__DIR__."/libs/database.php");

$cPokemon = new cPokemon();
$cChat = new cChat();
$cNotifylist = new cNotifylist();
$cNotified = new cNotified();
$db = new DB();
$pokemon = new Pokemon(__DIR__."/pokemon.json");
$telegram = new Telegram($cfg['telegram']['bot-id']);

/**
 * Server Adresse auslesen um zu bestimmen welche Region zugreift.
 */
$urldecode = explode('.', $_SERVER['SERVER_NAME']);
$place = $urldecode[0];