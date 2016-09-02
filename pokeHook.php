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
    $sth = $dbc->prepare("SELECT * FROM pokemon WHERE 
        encounter_id = :encounter_id AND
        spawnpoint_id = :spawnpoint_id");
    $sth->bindParam("encounter_id", $msg->encounter_id);
    $sth->bindParam("spawnpoint_id", $msg->spawnpoint_id);
    $sth->execute();
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    if(!$result){
        /**
         * Pokemon in der DB eintragen
         */
        $insert = $dbc->prepare("INSERT INTO pokemon
        (pokemon_id, encounter_id, disappear_time, geo_lat, geo_lng, spawnpoint_id)
        VALUES
        (:pokemon_id, :encounter_id, :disappear_time, :geo_lat, :geo_lng, :spawnpoint_id)");
        $insert->bindParam("pokemon_id", $msg->pokemon_id);
        $insert->bindParam("encounter_id", $msg->encounter_id);
        $insert->bindParam("disappear_time", $msg->disappear_time);
        $insert->bindParam("geo_lat", $msg->latitude);
        $insert->bindParam("geo_lng", $msg->longitude);
        $insert->bindParam("spawnpoint_id", $msg->spawnpoint_id);
        $insert->execute();
    }


	
} else { die(); }




/**
 * Prüft ob bereits ein Telegram Nachricht verschickt wird.
 * Es kann nur eine Versende Aktion gleichzeitig gestartet werden.
 * Dies damit die Nachricht korrekt ahgezeigt wird.
 */
if(file_exists("blocked")){ die(); }
$fp = fopen("blocked","wb");
fclose($fp);



/**
 * Pokemons durchschleiffen und benachrichtigung an Chat senden.
 * Suche Nach Pokemons die noch immer auf der Map sind.
 */
$sth = $dbc->prepare("SELECT * FROM pokemon 
    WHERE disappear_time > '".time()."'");
$sth->execute();
$result = $sth->fetchAll(PDO::FETCH_ASSOC);
foreach($result as $notifier){
    $sth = $dbc->prepare("SELECT * FROM chats");
    $sth->execute();
    $chats = $sth->fetchAll(PDO::FETCH_ASSOC);
    /**
     * Registrierte Chats durchschleiffen
     */
    foreach($chats as $chat){
        /**
         * Prüfen ob der Aktualle Chat dieses Pokemon als Benachrichtigung festgelegt hat
         */
        if($pokemon->getNotify($notifier['pokemon_id'], $chat['notify_pokemon'])) {
            /**
             * Prüfen ob bereits eine Benachrichtigung zu der Chat ID und der Pokemon DB ID geschickt wurde
             */
            $check = $dbc->prepare("SELECT * FROM notified
                WHERE chat_id = :chat_id
                AND pokemon_id = :pokemon_id");
            $check->bindParam("chat_id", $chat['chat_id']);
            $check->bindParam("pokemon_id", $notifier['id']);
            $check->execute();
            $result = $check->fetchAll(PDO::FETCH_ASSOC);
            if(count($result) == 0)
            {
                /**
                 * In der Datenbank eintragen das zu diesem Pokemon bereits eine Benachrichtigung an die
                 * Chat ID geschickt wurde
                 */
                $insert = $dbc->prepare("INSERT INTO notified 
                (chat_id, pokemon_id)
                VALUES
                (:chat_id, :pokemon_id)");
                $insert->bindParam("chat_id", $chat['chat_id']);
                $insert->bindParam("pokemon_id", $notifier['id']);
                $insert->execute();

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

unlink('blocked');

