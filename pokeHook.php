<?php
/**
 * Webhook für PokemonGo
 * @author Thomas Hirter <t.hirter@outlook.com>
 */


/**
 * Webhook daten empfangen
 */

$data = file_get_contents("php://input");
$json_decode = json_decode($data);
$msg = $json_decode->message;
$typ = $json_decode->type;


require_once(__DIR__."/init.php");


if($typ == "pokemon"){

    /**
     * Prüfen ob das Pokemon bereits erschienen ist und in der Db vorhanden ist
     */
    $cPokemon->pokemon_id       = $msg->pokemon_id;
    $cPokemon->encounter_id     = $msg->encounter_id;
    $cPokemon->spawnpoint_id    = $msg->spawnpoint_id;
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
        $create = $cPokemon->Create();

    }
	
} else { die(); }




/**
 * Pokemons durchschleiffen und benachrichtigung an Chat senden.
 * Suche Nach Pokemons die noch immer auf der Map sind.
 */

$result = $cPokemon->all();
foreach($result as $notifier){

    $chats = $cChat->all();
    /**
     * Registrierte Chats durchschleiffen
     */
    foreach($chats as $chat){
        /**
         * Prüfen ob der Aktualle Chat dieses Pokemon als Benachrichtigung festgelegt hat
         */

        $cNotifylist->chat_id       = $chat['chat_id'];
        $cNotifylist->pokemon_id    = $notifier['pokemon_id'];
        if(!empty($cNotifylist->search())) {
            /**
             * Prüfen ob bereits eine Benachrichtigung zu der Chat ID und der Pokemon DB ID geschickt wurde
             */


            $cNotified->chat_id     = $chat['chat_id'];
            $cNotified->pokemon_id  = $notifier['id'];
            if(empty($cNotified->search()))
            {
                /**
                 * In der Datenbank eintragen das zu diesem Pokemon bereits eine Benachrichtigung an die
                 * Chat ID geschickt wurde
                 */
                $cNotified->chat_id     = $chat['chat_id'];
                $cNotified->pokemon_id  = $notifier['id'];
                $create = $cNotified->Create();


                /**
                 * Nachricht an telegram senden
                 */
                $bild = array('chat_id' => $chat['chat_id'], 'sticker' => $pokemon->getSticker($notifier['pokemon_id']));
                $name = array('chat_id' => $chat['chat_id'], 'text' => "I hanes " . $pokemon->getName($notifier['pokemon_id']) . " gse, \nblibt no bis " . date("H:i:s", $notifier['disappear_time']));
                $location = array('chat_id' => $chat['chat_id'], 'latitude' => $notifier['geo_lat'], 'longitude' => $notifier['geo_lng']);

                $telegram->sendSticker($bild);
                $telegram->sendMessage($name);
                $telegram->sendLocation($location);
            }

        }
    }

}

?>
