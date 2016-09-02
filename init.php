<?php
/**
 *
 * @author Thomas Hirter <t.hirter@outlook.com>
 */

/**
 * Zeitzone festlegen
 */
date_default_timezone_set('Europe/Zurich');


/**
 * Erforderliche Dateien einbinden und Klassen laden
 */
require_once(__DIR__."/libs/Pokemon.php");
require_once(__DIR__."/libs/Telegram.php");
$cfg = parse_ini_file(__DIR__."/config/config.ini", TRUE);
$pokemon = new Pokemon(__DIR__."/pokemon.json");
$telegram = new Telegram($cfg['telegram']['bot-id']);




try{
    /**
     * DB Verbindung aufbauen und utf-8 setzen
     */
    $dsn = 'mysql:dbname=' . $cfg['database']['dbname'] . ';host=' . $cfg['database']['host'] . '';
    $dbc = new PDO($dsn, $cfg['database']['user'], $cfg['database']['pass'], array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
    ));
} catch (PDOException $pe) {
    echo "Keine Verbindung zur DB möglich!";
    die();
}