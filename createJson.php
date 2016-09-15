<?php
/**
 * Generiert ein JSON mit den Pokemons der aktuallen region.
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

/**
 * Erforderliche Files und Classen laden
 */
require_once(__DIR__."/init.php");

$array = array();
$db->bind("disappear_time", time());
$db->bind("place", $place);
$result   =  $db->query("
    SELECT * FROM pokemon 
    WHERE disappear_time > :disappear_time 
    AND place = :place");
foreach($result as $key) {
    $entry = array();
    $entry['pokemon_id']        = $key['pokemon_id'];
    $entry['pokemon_name']              = $pokemon->getName($key['pokemon_id']);
    $entry['disappear_time']    = $key['disappear_time'];
    $entry['latitude']          = $key['geo_lat'];
    $entry['longitude']         = $key['geo_lng'];
    $entry['pokemon_rarity']    = $pokemon->getRarity($key['pokemon_id']);
    $entry['iv_attack']         = $key['iv_attack'];
    $entry['iv_defense']        = $key['iv_defense'];
    $entry['iv_stamina']        = $key['iv_stamina'];
    $entry['iv_result']         = $key['iv_result'];
    array_push($array, $entry);

}

$json = json_encode($array, JSON_PRETTY_PRINT);
print_r($json);
