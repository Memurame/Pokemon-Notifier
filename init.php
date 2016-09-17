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
require_once(__DIR__."/libs/cNotifyPokemon.php");
require_once(__DIR__."/libs/cNotifyIV.php");
require_once(__DIR__."/libs/database.php");
require_once(__DIR__ ."/libs/log.php");
require_once(__DIR__ ."/libs/language.php");

$cPokemon = new cPokemon();
$cChat = new cChat();
$cNotifyPokemon = new cNotifyPokemon();
$cNotifyIV = new cNotifyIV();
$db = new DB();
$pokemon = new Pokemon(__DIR__."/pokemon.json");
$telegram = new Telegram($cfg['telegram']['bot-id']);

/**
 * Server Adresse auslesen um zu bestimmen welche Region zugreift.
 */

$urldecode = explode('.', $_SERVER['SERVER_NAME']);
if($urldecode[0] == "www"){ array_shift($urldecode); }
$place = strtolower($urldecode[0]);


Lang::set($cfg['notifier']['lang']);


