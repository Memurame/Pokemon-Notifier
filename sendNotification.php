<?php
/**
 * Notification senden an Telegram
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */
require_once(__DIR__."/init.php");


/**
 * Pokemons durchschleiffen und benachrichtigung an Chat senden.
 * Suche Nach Pokemons die noch immer auf der Map sind.
 */


$db->bind("disappear_time", time());
$result   =  $db->query("SELECT * FROM pokemon WHERE disappear_time > :disappear_time");

foreach($result as $notifier){


    /**
     * Prüfen welcher chat notifications zum pokemon erhalten möchte
     */
    $cNotifylist->pokemon_id    = $notifier['pokemon_id'];
    $notifylist = $cNotifylist->Search();
    foreach($notifylist as $notify){
        /**
         * Prüfen ob bereits eine Benachrichtigung zu der Chat ID und der Pokemon DB ID geschickt wurde
         */

        $cNotified->chat_id     = $notify['chat_id'];
        $cNotified->pokemon_id  = $notifier['id'];
        if(empty($cNotified->search()))
        {
            /**
             * In der Datenbank eintragen das zu diesem Pokemon bereits eine Benachrichtigung an die
             * Chat ID geschickt wurde
             */
            $cNotified->chat_id     = $notify['chat_id'];
            $cNotified->pokemon_id  = $notifier['id'];
            $create = $cNotified->Create();


            /**
             * Nachricht an telegram senden
             */
            $bild = array('chat_id' => $notify['chat_id'], 'sticker' => $pokemon->getSticker($notifier['pokemon_id']));
            $name = array('chat_id' => $notify['chat_id'], 'text' => "I hanes " . $pokemon->getName($notifier['pokemon_id']) . " gse, \nblibt no bis " . date("H:i:s", $notifier['disappear_time']));
            $location = array('chat_id' => $notify['chat_id'], 'latitude' => $notifier['geo_lat'], 'longitude' => $notifier['geo_lng']);

            $telegram->sendSticker($bild);
            $telegram->sendMessage($name);
            $telegram->sendLocation($location);
        }

    }

}