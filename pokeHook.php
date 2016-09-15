<?php
/**
 * Webhook für PokemonGo
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */


/**
 * Webhook daten empfangen un umwandeln
 */
$data = file_get_contents("php://input");
$json_decode = json_decode($data);
$msg = $json_decode->message;
$typ = $json_decode->type;

/**
 * Erforderliche Files und Classen laden
 */
require_once(__DIR__."/init.php");

/**
 * ############################################################
 * Zugriffs Berechtigung prüfen
 * Nur zugriffe mit dem richtigen KEY haben zugriff
 *
 * Prüfen ob der KEY mit dem der Map übereinstimmt
 * Wenn nicht wird der Push verweigert
 * ############################################################
 */

if(isset($_SERVER['HTTP_WEBHOOKKEY'])){
    if($_SERVER['HTTP_WEBHOOKKEY'] != $cfg['webhook']['key']){
        header('HTTP/1.1 401 Unauthorized');
        die("Zugriff nicht erlaubt, Falscher WebhookKey!");
    }
} else {
    header('HTTP/1.1 401 Unauthorized');
    die("Zugriff nicht erlaubt, der WebhookKey existiert nicht.!");
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
        $IV = ($msg->individual_attack + $msg->individual->individual_defense + $msg->individual_stamina)/(15+15+15)*100;


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
         * Das eben eingetragene Pokemon auslesen um die ID zu erhalten.
         */
        $cPokemon->pokemon_id       = $msg->pokemon_id;
        $cPokemon->encounter_id     = $msg->encounter_id;
        $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
        $last = $cPokemon->search();

        /**
         * Prüfen welcher chat notifications zum pokemon erhalten möchte
         */
        $cNotifylist->pokemon_id    = $msg->pokemon_id;
        $notifylist = $cNotifylist->Search();
        foreach($notifylist as $notify){

            /**
             * Prüfen ob es sich um die richtige region handelt
             */
            $cChat->chat_id = $notify['chat_id'];
            $cChat->find();
            if($cChat->place == $place){
                /**
                 * Prüfen ob bereits eine Benachrichtigung zu der Chat ID und der Pokemon DB ID geschickt wurde
                 */
                $cNotified->chat_id     = $notify['chat_id'];
                $cNotified->pokemon_id  = $last[0]['id'];
                if(empty($cNotified->search()))
                {
                    /**
                     * In der Datenbank eintragen das zu diesem Pokemon bereits eine Benachrichtigung an die
                     * Chat ID geschickt wurde
                     */
                    $cNotified->chat_id     = $notify['chat_id'];
                    $cNotified->pokemon_id  = $last[0]['id'];
                    $create = $cNotified->Create();


                    /**
                     * Nachricht an telegram senden
                     */
                    $bild = array('chat_id' => $notify['chat_id'], 'sticker' => $pokemon->getSticker($msg->pokemon_id));
                    $name = array('chat_id' => $notify['chat_id'], 'text' => "*".$pokemon->getName($msg->pokemon_id) . "* mit IV: *".number_format($IV, 1, ",", "'")."*%\nAttack: ".$msg->individual_attack." / Defense: ".$msg->individual_defense ." / Stamina: ". $msg->individual_stamina."\nblibt no bis " . date("H:i:s", $msg->disappear_time), 'parse_mode' => 'Markdown');
                    $location = array('chat_id' => $notify['chat_id'], 'latitude' => $msg->latitude, 'longitude' => $msg->longitude);

                    $telegram->sendSticker($bild);
                    $telegram->sendMessage($name);
                    $telegram->sendLocation($location);
                }
            }




        }
    }
	
}

?>
