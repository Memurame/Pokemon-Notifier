<?php
/**
 * Initialisierung aller benötigten resourcen und classen
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */

/**
 * COnfig Laden
 */
$cfg = parse_ini_file(__DIR__."/config/config.ini", TRUE);

if(isset($_GET['region']) and !empty($_GET['region'])){
    define("PLACE", strtolower($_GET['region']));
} else {
    define("PLACE", $cfg['notifier']['place']);
}

define("ROOT", __DIR__);
define("LOG", $cfg['notifier']['log']);

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */

require_once(__DIR__."/libs/pokemon.php");
require_once(__DIR__."/libs/telegram.php");
require_once(__DIR__."/libs/cChat.php");
require_once(__DIR__."/libs/cPlace.php");
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

$cChat = new cChat();
$cPlace = new cPlace();
$cNotifyPokemon = new cNotifyPokemon();
$cNotifyIV = new cNotifyIV();
$pokemon = new Pokemon(__DIR__, $cfg['notifier']['lang']);
$telegram = new Telegram($cfg['telegram']['bot-id']);


Lang::set($cfg['notifier']['lang']);


