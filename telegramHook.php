<?php

/**
 * PokemonGo Telegram Hook.
 * @author Thomas Hirter <t.hirter@outlook.com>
 */

/**
 * Erforderliche Dateien einbinden und Klassen laden
 */
require_once(__DIR__."/init.php");
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$text = $update["message"]["text"];
$chat_id = $update["message"]["chat"]["id"];

/**
 * Bot Starten
 */

$helptext = "Du chasch säuber istelle wasde für Benachrichtigunge wosch.\n\n/add Hinzuefüege\n/delete = Löschä\n/list = Benachrichtigungslischte\n/stop = Benachrichtigungen beenden\n\n";
$helptext .="Wende mehreri Pokémons wosch lösche oder hinzuefüege muesch immer es ',' zwüsche de Pokémons schribe.\n";
$helptext .= "Es Bispil:\n/add Glumanda\n/add Glumanda, Glurak\n";


if ($text == "/start") {


    $notify = array();
    $notify_pokemon = array();
    for($i=1; $i <= count($pokemon->pokemonArray()); $i++){
        if($pokemon->getNotify($i)){
            array_push($notify, $i);
            array_push($notify_pokemon, $pokemon->getName($i));
        }
    }
    $notify = implode(":", $notify);



    $sth = $dbc->prepare("SELECT * FROM chats WHERE chat_id = :chat_id");
    $sth->bindParam("chat_id", $chat_id);
    $sth->execute();
    $chat = $sth->fetch(PDO::FETCH_ASSOC);
    if(!$chat){
        $sth = $dbc->prepare("INSERT INTO chats 
          (chat_id, notify_pokemon)
          VALUES
          (:chat_id, :notify_pokemon)");
        $sth->bindParam("chat_id", $chat_id);
        $sth->bindParam("notify_pokemon", $notify);
        $sth->execute();
    } else {
        $reply = "Du hesch der chat bereits gstartet!";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
        die();
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
}

/**
 * Bot Beenden
 */
if($text == "/stop"){
    $sth = $dbc->prepare("DELETE FROM chats WHERE chat_id = :chat_id");
    $sth->bindParam("chat_id", $chat_id);
    $sth->execute();

    $reply = "Du wirsch ize nüm benachrichtigt. Zum erneute starte muesch /start i chat schribe.";
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);
}

/**
 * Ein Pokemon zur Benachrichtigungsliuste himzufügen
 */
if(substr($text, 0, 4) == "/add"){
    $sth = $dbc->prepare("SELECT notify_pokemon FROM chats WHERE chat_id = :chat_id");
    $sth->bindParam("chat_id", $chat_id);
    $sth->execute();
    $chat = $sth->fetch(PDO::FETCH_ASSOC);

    $notify = explode(":", $chat['notify_pokemon']);
    $selected = explode(",", substr($text, 5));

    $reply = "";
    foreach($selected as $select){
        foreach($pokemon->pokemonArray() as $id => $value){
            $select = trim($select);
            if($value['Name'] == $select){
                if(!in_array($id, $notify)){
                    array_push($notify, $id);
                    $reply .= $value['Name']."\n";
                }
            }

        }
    }
    sort($notify);
    $notify = implode(":", $notify);

    $update = $dbc->prepare("UPDATE chats SET notify_pokemon = :notify_pokemon WHERE chat_id = :chat_id");
    $update->bindParam("chat_id", $chat_id);
    $update->bindParam("notify_pokemon", $notify);
    $update->execute();

    if(empty($reply)){ $reply = "Es isch es Problem uftoucht."; }
    else{ $reply .= "zu Benachrichtigung hinzuegfüegt."; }
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);
}

/**
 * Pokemon aus Benachrichtigungsliste löschen
 */
if(substr($text, 0, 7) == "/delete"){
    $sth = $dbc->prepare("SELECT notify_pokemon FROM chats WHERE chat_id = :chat_id");
    $sth->bindParam("chat_id", $chat_id);
    $sth->execute();
    $chat = $sth->fetch(PDO::FETCH_ASSOC);

    $notify = explode(":", $chat['notify_pokemon']);
    $notify = array_combine(range(1, count($notify)), $notify);
    $selected = explode(",", substr($text, 8));

    $reply = "";
    $i = 0;
    foreach($selected as $select){
        $select = trim($select);
        $id = $pokemon->getID($select);
        echo $select." - ".$id."\n";
        if(in_array($id, $notify)) {
            $notify_id = array_search($id, $notify);
            unset($notify[$notify_id]);
            $reply .= $select . "\n";
        } else {
            $error = $select. "\n";
        }
    }

    $notify = implode(":", $notify);

    $update = $dbc->prepare("UPDATE chats SET notify_pokemon = :notify_pokemon WHERE chat_id = :chat_id");
    $update->bindParam("chat_id", $chat_id);
    $update->bindParam("notify_pokemon", $notify);
    $update->execute();

    if(!empty($reply)){
        $reply .= "von Benachrichtigung gelöscht.";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);
    }
    if(!empty($error)){
        $reply = "Bi folgende Pokémons isch es Problem uftoucht:\n".$error."Entweder isches nüm i dire Benachrichtigungsliste oder es isch kes Pokemon.";
        $content = array('chat_id' => $chat_id, 'text' => $reply);
        $telegram->sendMessage($content);

    }

}

/**
 * Komplete Liste ausgeben welche Pokemons Benachritigt werden
 */

if($text == "/list") {
    $sth = $dbc->prepare("SELECT notify_pokemon FROM chats WHERE chat_id = :chat_id");
    $sth->bindParam("chat_id", $chat_id);
    $sth->execute();
    $chat = $sth->fetch(PDO::FETCH_ASSOC);

    $notify = explode(":", $chat['notify_pokemon']);
    $notify = array_combine(range(1, count($notify)), $notify);

    $notify_pokemon = Array();
    for($i=1; $i <= count($notify); $i++){
        array_push($notify_pokemon, $pokemon->getName($notify[$i]));
    }

    //print_r($notify_pokemon);
    $reply = "Zu folgende Pokémons berchunsch du e Benachrichtigung:\n";
    $reply .= implode(", ", $notify_pokemon);
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

}

if($text == "/help") {

    $reply = $helptext;
    $content = array('chat_id' => $chat_id, 'text' => $reply);
    $telegram->sendMessage($content);

