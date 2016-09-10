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

$helptext = "Du chasch säuber istelle wasde für Benachrichtigunge wosch.\n\n/add = Hinzuefüege\n/remove = Löschä\n/list = Benachrichtigungslischte\n/reset = Istellige zuüggsetze\n/stop = Chat beände\n/help = Hilfe\n\n";
$helptext .="Wende mehreri Pokémons wosch lösche oder hinzuefüege muesch immer es ',' zwüsche de Pokémons schribe.\n";
$helptext .= "Es Bispil:\n/add Glumanda\n/add Glumanda, Glurak\n";

/**
 * Bot Starten
 */
if (strtolower($text) == "/start") {




    $cChat->chat_id = $chat_id;
    if(!$cChat->search()){
        $cChat->chat_id     = $chat_id;
        $cChat->place       = 'burgdorf';
        $create = $cChat->Create();

        $cNotifylist->chat_id = $chat_id;
        if(empty($cNotifylist->Search())){
            $notify_pokemon = array();
            for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
                if($pokemon->getNotify($i)){
                    array_push($notify_pokemon, $pokemon->getName($i));

                    $cNotifylist->chat_id       = $chat_id;
                    $cNotifylist->pokemon_id    = $i;
                    $create = $cNotifylist->Create();
                }
            }

            $reply = "Hallo bim PokemonBot vo Burgdorf. Mit däm Bot chasch dini benachrichtigunge säuber istelle. Zu folgende Pokémons wirsch standart mässig benachrichtigt:";
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

            $reply = implode(", ", $notify_pokemon);
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

            $reply = $helptext;
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

        } else {
            $reply = "Willkommen zrüg.\nDini Istellige si immerno vorhande.";
            $content = array('chat_id' => $chat_id, 'text' => $reply);
            $telegram->sendMessage($content);

        }

    } else {
        $reply = "Du hesch der chat bereits gstartet!";
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

        $reply = "Du wirsch ize nüm benachrichtigt. Zum erneute starte muesch /start i chat schribe.";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    } else {
        $reply = "Du hesch d Benachrichtigunge bereits beändet.";
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
            $cNotifylist->pokemon_id    = $id;
            $cNotifylist->chat_id       = $chat_id;
            if(!$cNotifylist->Search()){
                $reply .= $pokemon->getName($id)."\n";

                $cNotifylist->chat_id       = $chat_id;
                $cNotifylist->pokemon_id    = $id;
                $create = $cNotifylist->Create();
            }
        }
    }

    if(!empty($reply)){ $reply .= "zu Benachrichtigung hinzuegfüegt."; }
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
            $cNotifylist->pokemon_id = $id;
            $cNotifylist->chat_id = $chat_id;
            if ($cNotifylist->Search()) {
                $reply .= $pokemon->getName($id) . "\n";

                $cNotifylist->chat_id = $chat_id;
                $cNotifylist->pokemon_id = $id;
                $create = $cNotifylist->Delete();
            }
        }
    }

    if(!empty($reply)){
        $reply .= "von Benachrichtigung gelöscht.";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }

}

/**
 * Komplete Liste ausgeben welche Pokemons Benachritigt werden
 */

if(strtolower($text) == "/list") {

    $notify_pokemon = Array();

    $cNotifylist->chat_id = $chat_id;
    $notify = $cNotifylist->Search();

    foreach($notify as $key){
        array_push($notify_pokemon, $pokemon->getName($key['pokemon_id']));
    }

    $reply = "Zu folgende Pokémons berchunsch du e Benachrichtigung:\n";
    $reply .= implode(", ", $notify_pokemon);
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}


if(strtolower($text) == "/reset") {

    $notify_pokemon = Array();

    $cNotifylist->chat_id = $chat_id;
    $delete = $cNotifylist->Delete();

    $notify_pokemon = array();
    for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
        if($pokemon->getNotify($i)){
            array_push($notify_pokemon, $pokemon->getName($i));

            $cNotifylist->chat_id       = $chat_id;
            $cNotifylist->pokemon_id    = $i;
            $create = $cNotifylist->Create();
        }
    }


    $reply = "Benachrichtigungs Pokémon si zurüggsetzt worde.\n";
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}


/**
 * Alle Befehle erneut anzeigen
 */
if(strtolower($text) == "/help") {

    $reply = $helptext;
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}
if(strtolower($text) == "/bern") {

    $cChat->place   = "bern";
    $cChat->chat_id = $chat_id;
    $save = $cChat->save();

    $reply = "Du wirsch ize über Pokemons in Bern benachrichtigt..";
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}
if(strtolower($text) == "/burgdorf") {

    $cChat->place   = "burgdorf";
    $cChat->chat_id = $chat_id;
    $save = $cChat->save();

    $reply = "Du wirsch ize über Pokemons in Burgdorf benachrichtigt..";
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}

if(strtolower($text) == "/oberburg") {

    $cChat->place   = "oberburg";
    $cChat->chat_id = $chat_id;
    $save = $cChat->save();

    $reply = "Du wirsch ize über Pokemons in Oberburg benachrichtigt..";
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
            $reply = "Meldung vom Administrator:\n\n";
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


    if(strtolower($text) == "/bot stop"){

        $handle = fopen ("blocked", "w");
        fwrite($handle, "Wenn diese Datei existiert wurde der Bot vom Admin gestoppt über das Telegram App");
        fclose ($handle);

        $reply = "Bot gestoppt";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }

    if(strtolower($text) == "/bot start"){

        unlink("blocked");

        $reply = "Bot gestartet";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }
}