<?php
/**
 * Webhook für PokemonGo
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */
/**
 * Erforderliche Files und Classen laden
 */

require_once(__DIR__."/init.php");

/**
 * Webhook daten empfangen un umwandeln
 */
$data = file_get_contents("php://input");

if(empty($data)){ Log::write("It has received no content.", true); }

$json_decode = json_decode($data);
$msg = $json_decode->message;
$typ = $json_decode->type;


/**
 * ############################################################
 * Zugriffs Berechtigung prüfen
 * Nur zugriffe mit dem richtigen KEY haben zugriff
 *
 * Prüfen ob der KEY mit dem der Map übereinstimmt
 * Wenn nicht wird der Push verweigert
 *
 * !! Dieser Schutz wird nur aktiv wenn du in der config einen Webhook Key hinterlegt hast!!
 * ############################################################
 */
if(!empty($cfg['webhook']['key'])){
    if(isset($_SERVER['HTTP_WEBHOOKKEY'])){
        if($_SERVER['HTTP_WEBHOOKKEY'] != $cfg['webhook']['key']){
            header('HTTP/1.1 401 Unauthorized');
            Log::write("Wrong Webhook KEY.");
        }
    } else {
        header('HTTP/1.1 401 Unauthorized');
        Log::write("No defined Webhook KEY");
    }
}


/**
 * ############################################################
 * ############################################################
 */

/**
 * Prüffen ob es sich um ein Pokemon handelt
 */
if($typ == "pokemon"){

    /**
     * Prüfen ob das Pokemon bereits erschienen ist und in der Db vorhanden ist.
     * Zur absicherung das es kein doppelter eintrag gibt.
     */
    $cPokemon->pokemon_id       = $msg->pokemon_id;
    $cPokemon->encounter_id     = $msg->encounter_id;
    $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
    if(empty($cPokemon->search())) {
        /**
         * IV ausrechnen
         */
        $IV = ($msg->individual_attack + $msg->individual_defense + $msg->individual_stamina)/(15+15+15)*100;

        /**
         * Pokemon in der DB eintragen
         */
        $cPokemon->pokemon_id       = $msg->pokemon_id;
        $cPokemon->encounter_id     = $msg->encounter_id;
        $cPokemon->disappear_time   = $msg->disappear_time;
        $cPokemon->geo_lat          = $msg->latitude;
        $cPokemon->geo_lng          = $msg->longitude;
        $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
        $cPokemon->place            = $place;
        $cPokemon->iv_attack        = $msg->individual_attack;
        $cPokemon->iv_defense       = $msg->individual_defense;
        $cPokemon->iv_stamina       = $msg->individual_stamina;
        $cPokemon->iv_result        = $IV;
        $create = $cPokemon->Create();


        /**
         * Prüfen welcher chat notifications zum pokemon erhalten möchte
         */
        $db->bind("pokemon_id", $msg->pokemon_id);
        $db->bind("place", $place);
        $notifylist = $db->query("
            SELECT * FROM notify_pokemon 
            LEFT JOIN chats 
            ON notify_pokemon.chat_id = chats.chat_id 
            WHERE notify_pokemon.pokemon_id = :pokemon_id
            AND chats.place = :place
            ORDER BY priority desc");
        $i = 0;
        while($i < count($notifylist)){

            /**
             * Nachricht an telegram senden
             */
            $chat_id = $notifylist[$i]['chat_id'];
            $bild = array(
                'chat_id' => $chat_id,
                'sticker' => $pokemon->getSticker($msg->pokemon_id));
            $name = array(
                'chat_id' => $chat_id,
                'text' => "*".$pokemon->getName($msg->pokemon_id) . "* mit IV: *".number_format($IV, 1, ",", "'").
                    "*%\nAttack: ".$msg->individual_attack." / Defense: ".$msg->individual_defense ." / Stamina: ".$msg->individual_stamina.
                    "\nblibt no bis " . date("H:i:s", $msg->disappear_time),
                'parse_mode' => 'Markdown');
            $location = array(
                'chat_id' => $chat_id,
                'latitude' => $msg->latitude,
                'longitude' => $msg->longitude);

            $telegram->sendSticker($bild);
            $telegram->sendMessage($name);
            $telegram->sendLocation($location);

            $i++;
        }
    }
	
}

?>
