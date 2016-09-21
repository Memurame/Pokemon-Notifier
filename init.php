<?php
/**
 * Initialisierung aller benötigten resourcen und classen
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */


/**
 * Zeitzone festlegen
 */

$cfg = parse_ini_file(__DIR__."/config/config.ini", TRUE);

$urldecode = explode('.', $_SERVER['SERVER_NAME']);
if($urldecode[0] == "www"){ array_shift($urldecode); }

define("ROOT", __DIR__);
define("LOG", $cfg['notifier']['log']);
define("PLACE", strtolower($urldecode[0]));

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */

require_once(__DIR__."/libs/pokemon.php");
require_once(__DIR__."/libs/telegram.php");
require_once(__DIR__."/libs/cPokemon.php");
require_once(__DIR__."/libs/cChat.php");
require_once(__DIR__."/libs/cNotifyPokemon.php");
require_once(__DIR__."/libs/cNotifyIV.php");
require_once(__DIR__."/libs/database.php");
require_once(__DIR__ ."/libs/log.php");
require_once(__DIR__ ."/libs/language.php");

$db = new DB();

if(file_exists("sql/create.sql")){
    $sql= file_get_contents("sql/create.sql");
    $queries = explode(";",$sql);
    foreach($queries as $query){ $db->query($query); }
    Log::write("Datenbank Update");
    unlink("sql/create.sql");
}

$cPokemon = new cPokemon();
$cChat = new cChat();
$cNotifyPokemon = new cNotifyPokemon();
$cNotifyIV = new cNotifyIV();
$pokemon = new Pokemon(__DIR__, $cfg['notifier']['lang']);
$telegram = new Telegram($cfg['telegram']['bot-id']);


Lang::set($cfg['notifier']['lang']);


