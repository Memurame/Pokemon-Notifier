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

if (isset($update["message"])) {
    $text = $update["message"]["text"];
    $chat_id = $update["message"]["chat"]["id"];
} else if (isset($update["callback_query"])) {
    $text = $update["callback_query"]["data"];
    $chat_id = $update["callback_query"]["message"]["chat"]["id"];
    $telegram->answerCallbackQuery(array(
        'callback_query_id' => $update["callback_query"]["id"],
        'text' => 'Erfolgreich'
    ));
} else {
    Log::write("TELEGRAM: Fehler beim empfangen der Daten\n" . $content, true);
}

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
        $cChat->place       = PLACE;
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


        $reply = array(
            'chat_id' => $chat_id,
            'text' => Lang::get("stopped"),
            'reply_markup' => $telegram->buildInlineKeyBoard(array(
                array(
                    $telegram->buildInlineKeyboardButton('Start', '', "/start")
                )
            )));
        $telegram->sendMessage($reply);


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
    foreach($selected as $id){
        if (!is_numeric($id)) {
            $id = $pokemon->getID($id);
        }
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

    if(!empty($reply)){
        $reply .= Lang::get("added");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    } else {
        $keyboardButtons = array();
        foreach ($pokemon->pokemonArray() as $id => $value) {
            $cNotifyPokemon->pokemon_id = $id;
            $cNotifyPokemon->chat_id = $chat_id;
            if (!$cNotifyPokemon->Search()) {
                $keyboardButtons[] = array($telegram->buildKeyboardButton("/add ".$pokemon->getName($id)));
            }
        }
        $content = array(
            'chat_id' => $chat_id,
            'text' => Lang::get("addquestion"),
            'reply_markup' => $telegram->buildKeyBoard($keyboardButtons)
        );
        $telegram->sendMessage($content);
    }
}

/**
 * Pokemon aus Benachrichtigungsliste löschen
 */
if(substr(strtolower($text), 0, 7) == "/remove"){

    $reply = "";
    $selected = explode(",", substr($text, 8));
    foreach($selected as $id) {
        if (!is_numeric($id)) {
            $id = $pokemon->getID($id);
        }
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
    } else {
        $keyboardButtons = array();
        foreach ($pokemon->pokemonArray() as $id => $value) {
            $cNotifyPokemon->pokemon_id = $id;
            $cNotifyPokemon->chat_id = $chat_id;
            if ($cNotifyPokemon->Search()) {
                $keyboardButtons[] = array($telegram->buildKeyboardButton("/remove ".$pokemon->getName($id)));
            }
        }
        $content = array(
            'chat_id' => $chat_id,
            'text' => Lang::get("removequestion"),
            'reply_markup' => $telegram->buildKeyBoard($keyboardButtons)
        );
        $telegram->sendMessage($content);
    }

}

/**
 * Komplete Liste ausgeben welche Pokemons Benachritigt werden
 */

if(strtolower($text) == "/list") {

    $notify_pokemon = Array();

    $cNotifyPokemon->chat_id = $chat_id;
    $notify = $cNotifyPokemon->search();
    foreach($notify as $key){

        $text = $pokemon->getName($key['pokemon_id']);

        $cNotifyIV->chat_id     = $chat_id;
        $cNotifyIV->pokemon_id  = $key['pokemon_id'];
        $find = $cNotifyIV->search();
        if($find){
          $text .= "(" . $find[0]['iv_val'] . "%) ";
        }

        array_push($notify_pokemon, $text);
    }

    $reply = array(
        'chat_id' => $chat_id,
        'text' => Lang::get("replylist", array("list" => implode(", ", $notify_pokemon))),
        'reply_markup' => $telegram->buildInlineKeyBoard(array(
            array(
                $telegram->buildInlineKeyboardButton('add', '', "/add"),
                $telegram->buildInlineKeyboardButton('remove', '', "/remove")
            )
        )));

    $telegram->sendMessage($reply);

}

/**
 * Zurücksetzen der Notification Pokemon auf die Standart einstellung
 */
if(strtolower($text) == "/reset") {

    $notify_pokemon = Array();

    $cNotifyPokemon->chat_id = $chat_id;
    $delete = $cNotifyPokemon->delete();

    $cNotifyIV->chat_id = $chat_id;
    $delete = $cNotifyIV->delete();

    $notify_pokemon = array();
    for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
        if($pokemon->getNotify($i)){
            array_push($notify_pokemon, $pokemon->getName($i));

            $cNotifyPokemon->chat_id       = $chat_id;
            $cNotifyPokemon->pokemon_id    = $i;
            $create = $cNotifyPokemon->create();
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


if(substr(strtolower($text), 0, 3) == "/iv"){

    $reply = "";
    $text = trim(substr($text, 4));
    $textArray = array_filter(explode(" ", $text, 2));
    if(!empty($textArray) && intval($textArray[0]) AND $textArray[0] > 0 AND $textArray[0] <= 100){
        $iv = $textArray[0];
        if(isset($textArray[1])) {
            $pokemons = explode(",", $textArray[1]);
            foreach ($pokemons as $select) {
                $id = $pokemon->getID($select);
                if ($id) {
                    $reply .= $pokemon->getName($id) . "\n";

                    $cNotifyIV->pokemon_id = $id;
                    $cNotifyIV->chat_id = $chat_id;
                    if (!$cNotifyIV->search()) {
                        $cNotifyIV->chat_id = $chat_id;
                        $cNotifyIV->pokemon_id = $id;
                        $cNotifyIV->iv_val = $iv;
                        $create = $cNotifyIV->create();
                    } else {
                        $db->bind("chat_id", $chat_id);
                        $db->bind("pokemon_id", $id);
                        $db->bind("iv_val", $iv);
                        $update = $db->query("UPDATE notify_iv SET
                          iv_val = :iv_val
                          WHERE chat_id = :chat_id
                          AND pokemon_id = :pokemon_id");
                    }

                    $cNotifyPokemon->pokemon_id = $id;
                    $cNotifyPokemon->chat_id = $chat_id;
                    $exist = $cNotifyPokemon->search();
                    if (!$exist) {
                        $cNotifyPokemon->chat_id = $chat_id;
                        $cNotifyPokemon->pokemon_id = $id;
                        $create = $cNotifyPokemon->Create();
                    }
                }
            }

            if(!empty($reply)){ $reply .= Lang::get("setiv", array("iv" => $iv)); }
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);
        } else {
            $keyboardButtons = array();
            foreach ($pokemon->pokemonArray() as $id => $value) {
                $keyboardButtons[] = array($telegram->buildKeyboardButton("/iv $iv ".$pokemon->getName($id)));
            }

            $content = array(
                'chat_id' => $chat_id,
                'text' => Lang::get("selectivpokemon", array("iv" => $iv)),
                'reply_markup' => $telegram->buildKeyBoard($keyboardButtons)
            );
            $telegram->sendMessage($content);
        }



    } else {
        $reply = Lang::get("erroriv");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }



}

/**
 * Sendet den Sticker des Pokemons
 */
if(substr(strtolower($text), 0, 8) == "/sticker"){
    $id = substr($text, 9);
    if (!is_numeric($id)) {
        $id = $pokemon->getID($id);
    }
    $bild = array(
        'chat_id' => $chat_id,
        'sticker' => $pokemon->getSticker($id)
    );
    $telegram->sendSticker($bild);
}

/**
 * Sendet die Location des Pokemons
 */
if(substr(strtolower($text), 0, 9) == "/location"){
    list($latitude, $longitude) = explode(' ', substr($text, 10));
    $location = array(
        'chat_id' => $chat_id,
        'latitude' => $latitude,
        'longitude' => $longitude
    );
    $telegram->sendLocation($location);
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
            $reply = Lang::get("adminmsg", array("message" => $text));
            $content = array('chat_id' => $chat['chat_id'], 'text' => $reply);
            $telegram->sendMessage($content);
        }

    }
    /**
     * Hilfe für den Admin
     */
    if(substr(strtolower($text), 0, 10) == "/adminhelp"){

        $reply = Lang::get("adminhelp");
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);

    }

    /**
     * Fügt einen neuen Chat in die chats Tabelle und weisst diesem die standart Pokemons zu
     */
    if(substr(strtolower($text), 0, 12) == "/groupcreate"){

        $text = trim(substr($text, 13));
        $textArray = array_filter(explode(" ", $text, 3));
        if(!empty($textArray[0]) AND !empty($textArray[1])){

            $place = isset($textArray[2]) ? trim($textArray[2]) : PLACE;

            $cChat->chat_id = $textArray[0];
            if(!$cChat->search()) {

                $cChat->chat_id = trim($textArray[0]);
                $cChat->place = $place;
                $cChat->alias = trim($textArray[1]);
                $cChat->priority = "1";
                $create = $cChat->Create();

                $cNotifyPokemon->chat_id = $textArray[0];
                if(empty($cNotifyPokemon->Search())) {
                    $notify_pokemon = array();
                    for ($i = 1; $i <= count($pokemon->pokemonArray()); $i++) {
                        if ($pokemon->getNotify($i)) {
                            array_push($notify_pokemon, $pokemon->getName($i));

                            $cNotifyPokemon->chat_id = trim($textArray[0]);
                            $cNotifyPokemon->pokemon_id = $i;
                            $create = $cNotifyPokemon->Create();
                        }
                    }
                }
                $reply = Lang::get("groupadded", array(
                    "id" => $textArray[0],
                    "alias" => $textArray[1],
                    "place" => $place));
                $content = array(
                    'chat_id' => $chat_id,
                    'text' => $reply,
                    'parse_mode' => 'Markdown');
                $telegram->sendMessage($content);
            } else {
                $reply = Lang::get("groupidexists", array("id" => $textArray[0]));
                $content = array(
                    'chat_id' => $chat_id,
                    'text' => $reply,
                    'parse_mode' => 'Markdown');
                $telegram->sendMessage($content);
            }
        }


    }
}
