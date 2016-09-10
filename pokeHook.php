<?php
/**
 * Webhook für PokemonGo
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With");
header("Access-Control-Allow-Methods: POST, GET");

/**
 * Webhook daten empfangen
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
 * ############################################################
 */

/**
 * Prüfen ob der KEY mit dem der Map übereinstimmt
 * Wenn nicht wird der Push verweigert
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
 * Prüfen ob der Telegramchat vom Admin über den Bot gestoppt wurde.
 */
if(file_exists("blocked")){die("Service wurde vom Admin beendet");}

/**
 * ############################################################
 * ############################################################
 */


$place = explode('.', $_SERVER['SERVER_NAME']);


if($typ == "pokemon"){

    /**
     * Prüfen ob das Pokemon bereits erschienen ist und in der Db vorhanden ist
     */
    $cPokemon->pokemon_id       = $msg->pokemon_id;
    $cPokemon->encounter_id     = $msg->encounter_id;
    $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
    $cPokemon->place            = $place[0];
    if(empty($cPokemon->search())) {
        /**
         * Pokemon in der DB eintragen
         */
        $cPokemon->pokemon_id       = $msg->pokemon_id;
        $cPokemon->encounter_id     = $msg->encounter_id;
        $cPokemon->disappear_time   = $msg->disappear_time;
        $cPokemon->geo_lat          = $msg->latitude;
        $cPokemon->geo_lng          = $msg->longitude;
        $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
        $cPokemon->place            = $place[0];
        $create = $cPokemon->Create();


        /**
         * Das eben eingetragene Pokemon auslesen um die ID zu erhalten.
         */
        $cPokemon->pokemon_id       = $msg->pokemon_id;
        $cPokemon->encounter_id     = $msg->encounter_id;
        $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
        $cPokemon->place            = $place[0];
        $last = $cPokemon->search();

        /**
         * Prüfen welcher chat notifications zum pokemon erhalten möchte
         */
        $cNotifylist->pokemon_id    = $msg->pokemon_id;
        $notifylist = $cNotifylist->Search();
        foreach($notifylist as $notify){
            $cChat->find($notify['chat_id']);
            if($cChat->place == $place[0]){
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
                    $cNotified->pokemon_id  = $msg->pokemon_id;
                    $create = $cNotified->Create();


                    /**
                     * Nachricht an telegram senden
                     */
                    $bild = array('chat_id' => $notify['chat_id'], 'sticker' => $pokemon->getSticker($msg->pokemon_id));
                    $name = array('chat_id' => $notify['chat_id'], 'text' => "I hanes " . $pokemon->getName($msg->pokemon_id) . " gse, \nblibt no bis " . date("H:i:s", $msg->disappear_time));
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
