<?php
/**
 * Generiert ein JSON mit den Pokemons der aktuallen region.
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With");
header("Access-Control-Allow-Methods: GET");

$urldecode = explode('.', $_SERVER['SERVER_NAME']);
$place = $urldecode[0];

/**
 * Erforderliche Files und Classen laden
 */
require_once(__DIR__."/init.php");

$array = array();
$db->bind("disappear_time", time());
$db->bind("place", $place);
$result   =  $db->query("SELECT * FROM pokemon WHERE disappear_time > :disappear_time AND place = :place");
foreach($result as $key) {
    $entry = array();
    $entry['pokemon_id']        = $key['pokemon_id'];
    $entry['name']              = $pokemon->getName($key['pokemon_id']);
    $entry['disappear_time']    = $key['disappear_time'];
    $entry['latitude']          = $key['geo_lat'];
    $entry['longitude']          = $key['geo_lng'];
    array_push($array, $entry);

}

$json = json_encode($array, JSON_PRETTY_PRINT);
print_r($json);
