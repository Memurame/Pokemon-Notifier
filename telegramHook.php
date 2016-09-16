<?php

/**
 * PokemonGo Telegram Hook.
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */
require_once(__DIR__."/init.php");
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$text = $update["message"]["text"];
$chat_id = $update["message"]["chat"]["id"];

$cChat->chat_id = $chat_id;
$chat = $cChat->Search();

/**
 * Bot Starten
 */
if (strtolower($text) == "/start") {

    $cChat->chat_id = $chat_id;
    if(!$cChat->search()){

        if($cfg['telegram']['admin-id'] == $chat_id){
            $cChat->admin = 1;
            $cChat->priority = 1;
        }
        $cChat->chat_id     = $chat_id;
        $cChat->place       = $place;
        $create = $cChat->Create();

        $cNotifyPokemon->chat_id = $chat_id;
        if(empty($cNotifyPokemon->Search())){
            $notify_pokemon = array();
            for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
                if($pokemon->getNotify($i)){
                    array_push($notify_pokemon, $pokemon->getName($i));

                    $cNotifyPokemon->chat_id       = $chat_id;
                    $cNotifyPokemon->pokemon_id    = $i;
                    $create = $cNotifyPokemon->Create();
                }
            }

            $reply = Lang::get("welcome");
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

            $reply = implode(", ", $notify_pokemon);
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

            $reply = Lang::get("helptext");;
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

        } else {
            $reply = Lang::get("welcomeback");
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

        }

    } else {
        $reply = Lang::get("botalredystarted");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        die();
    }
}

if(!$chat){ die(); };

/**
 * Bot Beenden
 */
if(strtolower($text) == "/stop"){

    $cChat->chat_id = $chat_id;
    if(!empty($cChat->Search())){
        $cChat->chat_id = $chat_id;
        $delete = $cChat->Delete();

        $reply = Lang::get("stopped");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    } else {
        $reply = Lang::get("botalredystopped");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        die();
    }

}

/**
 * Ein Pokemon zur Benachrichtigungsliuste himzufügen
 */
if(substr(strtolower($text), 0, 4) == "/add"){

    $reply = "";
    $selected = explode(",", substr($text, 5));
    foreach($selected as $select){
        $id = $pokemon->getID($select);
        if($id){
            $cNotifyPokemon->pokemon_id    = $id;
            $cNotifyPokemon->chat_id       = $chat_id;
            if(!$cNotifyPokemon->Search()){
                $reply .= $pokemon->getName($id)."\n";

                $cNotifyPokemon->chat_id       = $chat_id;
                $cNotifyPokemon->pokemon_id    = $id;
                $create = $cNotifyPokemon->Create();
            }
        }
    }

    if(!empty($reply)){ $reply .= Lang::get("added"); }
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);
}

/**
 * Pokemon aus Benachrichtigungsliste löschen
 */
if(substr(strtolower($text), 0, 7) == "/remove"){

    $reply = "";
    $selected = explode(",", substr($text, 8));
    foreach($selected as $select) {
        $id = $pokemon->getID($select);
        if ($id) {
            $cNotifyPokemon->pokemon_id = $id;
            $cNotifyPokemon->chat_id = $chat_id;
            if ($cNotifyPokemon->Search()) {
                $reply .= $pokemon->getName($id) . "\n";

                $cNotifyPokemon->chat_id = $chat_id;
                $cNotifyPokemon->pokemon_id = $id;
                $create = $cNotifyPokemon->Delete();
            }
        }
    }

    if(!empty($reply)){
        $reply .= Lang::get("removed");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }

}

/**
 * Komplete Liste ausgeben welche Pokemons Benachritigt werden
 */

if(strtolower($text) == "/list") {

    $notify_pokemon = Array();

    $cNotifyPokemon->chat_id = $chat_id;
    $notify = $cNotifyPokemon->Search();

    foreach($notify as $key){
        array_push($notify_pokemon, $pokemon->getName($key['pokemon_id']));
    }

    $reply = Lang::get("replylist");
    $reply .= implode(", ", $notify_pokemon);
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}

/**
 * Zurücksetzen der Notification Pokemon auf die Standart einstellung
 */
if(strtolower($text) == "/reset") {

    $notify_pokemon = Array();

    $cNotifyPokemon->chat_id = $chat_id;
    $delete = $cNotifyPokemon->Delete();

    $notify_pokemon = array();
    for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
        if($pokemon->getNotify($i)){
            array_push($notify_pokemon, $pokemon->getName($i));

            $cNotifyPokemon->chat_id       = $chat_id;
            $cNotifyPokemon->pokemon_id    = $i;
            $create = $cNotifyPokemon->Create();
        }
    }


    $reply = Lang::get("reset");
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}


/**
 * Alle Befehle erneut anzeigen
 */
if(strtolower($text) == "/help") {

    $reply = Lang::get("helptext");
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}


/**
 * ###############################################
 * Admin befehle
 * ###############################################
 */
if($chat && $chat[0]['admin']){

    /**
     * Senden einer Nachricht an alle registrierten Chats
     */
    if(substr(strtolower($text), 0, 5) == "/send"){

        $text = substr($text, 6);

        $chats = $cChat->All();
        foreach($chats as $chat){
            $reply = Lang::get("adminmsg");
            $reply .= $text;
            $content = array('chat_id' => $chat['chat_id'], 'text' => $reply);
            $telegram->sendMessage($content);
        }

    }

    /**
     * Säubern der Datenbank
     * Löscht alle Pokemons die nicht mehr auf de Map sind.
     */
    if(strtolower($text) == "/cleandb"){

        $db->bind("disappear_time", time());
        $result   =  $db->query("DELETE FROM pokemon WHERE disappear_time < :disappear_time");

        $reply = $result ." gelöschte einträge.";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }
}