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
http_response_code(200);

$json_decode = json_decode($data);
$msg = $json_decode->message;
$typ = $json_decode->type;



/**
 * Prüffen ob es sich um ein Pokemon handelt
 */
if($typ == "pokemon"){
    Log::write($data);
    $IV = 0;
    if (isset($msg->individual_attack) && isset($msg->individual_defense) && isset($msg->individual_stamina)) {
        $IV = ($msg->individual_attack + $msg->individual_defense + $msg->individual_stamina)/(15+15+15)*100;
    }

    /**
     * Prüfen ob da Pokemon bereits per Webhook empfangen wurde.
     * Wenn Bereits ein eintrag vorhanden wird das Script abgebrochen damit keine Doppelten benachrichtigungen gesendet werden.
     */
    $db->bind("pokemon_id", $msg->pokemon_id);
    $db->bind("encounter_id", $msg->encounter_id);
    $db->bind("spawnpoint_id", $msg->spawnpoint_id);
    $db->bind("disappear_time", $msg->disappear_time);
    $checkDuplicate = $db->query("SELECT * FROM pokemonhistory
        WHERE pokemon_id = :pokemon_id
        AND encounter_id = :encounter_id
        AND spawnpoint_id = :spawnpoint_id
        AND disappear_time = :disappear_time");

    if(!empty($checkDuplicate)){
        Log::write("Doppelter Webhook eintrag erhalten.",true);
    }


    $db->bind("pokemon_id", $msg->pokemon_id);
    $db->bind("encounter_id", $msg->encounter_id);
    $db->bind("spawnpoint_id", $msg->spawnpoint_id);
    $db->bind("disappear_time", $msg->disappear_time);
    $insertPokemon = $db->query("INSERT INTO pokemonhistory SET 
        pokemon_id = :pokemon_id,
        encounter_id = :encounter_id,
        spawnpoint_id = :spawnpoint_id,
        disappear_time = :disappear_time");






    /**
     * Prüfen welcher chat notifications zum pokemon erhalten möchte
     */

    $db->bind("pokemon_id", $msg->pokemon_id);
    $db->bind("place", PLACE);
    $notifylist = $db->query("
        SELECT chats.chat_id, chats.place, chats.active, notify_pokemon.pokemon_id, notify_iv.iv_val 
        FROM notify_pokemon 
        LEFT JOIN chats 
        ON notify_pokemon.chat_id = chats.chat_id 
        LEFT JOIN notify_iv
        ON notify_pokemon.chat_id = notify_iv.chat_id AND notify_pokemon.pokemon_id = notify_iv.pokemon_id
        WHERE notify_pokemon.pokemon_id = :pokemon_id
        AND chats.place = :place
        AND chats.active = '1'
        ORDER BY priority desc");
    $i = 0;

    if(empty($notifylist)){
        Log::write($pokemon->getName($msg->pokemon_id) .
            ", keine Benachrichtigungen zu diesem Pokemon gefunden."); }

    while($i < count($notifylist)){
        $chat_id = $notifylist[$i]['chat_id'];

        $msg->disappear_time = $msg->disappear_time;

        $time = $msg->disappear_time -time();
        if($time <= 0){
            $countdown = "*Zeit Abgelaufen*";
        } else {
            $countdown = date("H\h i\m s\s", $time);
        }



        if($notifylist[$i]['iv_val'] <= $IV || empty($notifylist[$i]['iv_val'])){
            /**
             * Nachricht an telegram senden
             */
            $text = '*' . $pokemon->getName($msg->pokemon_id) . "* ";
            if ($IV) {
                $text .= Lang::get('iv', array("iv" => number_format($IV, 1, ',', '\''))) .
                    Lang::get('ivsplit', array(
                        "attack"    => $msg->individual_attack,
                        "defense"   => $msg->individual_defense,
                        "stamina"   => $msg->individual_stamina
                    )).
                    Lang::get('hit1', array(
                        "name" => $pokemon->getMoves($msg->move_1),
                        "wert" => $pokemon->getMovesInfo($msg->move_1)
                    )).
                    Lang::get('hit2', array(
                        "name" => $pokemon->getMoves($msg->move_2),
                        "wert" => $pokemon->getMovesInfo($msg->move_2)
                    ));
            } else {
                $text .= "\n";
            }
            $text .= Lang::get('time', array(
                "time" => date('H:i:s', $msg->disappear_time),
                "countdown" => $time));
            $name = array(
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
                'reply_markup' => $telegram->buildInlineKeyBoard(array(
                    array(
                        $telegram->buildInlineKeyboardButton('Sticker', '', "/sticker $msg->pokemon_id"),
                        $telegram->buildInlineKeyboardButton('Location', '', "/location $msg->latitude $msg->longitude"),
                        $telegram->buildInlineKeyboardButton('Remove', '', "/remove $msg->pokemon_id")
                    )
                )));

            /**
             * reypl_markup entfernen wenne s sich um einen Kanal handelt
             */
            if(substr($chat_id, 0, 1) == "@" || $chat_id < 0){
                unset($name['reply_markup']);

                $bild = array(
                    'chat_id' => $chat_id,
                    'sticker' => $pokemon->getSticker($msg->pokemon_id));

                $location = array(
                    'chat_id' => $chat_id,
                    'latitude' => $msg->latitude,
                    'longitude' => $msg->longitude);

                /**
                 * Sticker nur senden wenn in der Config eingeschalten
                 */
                if($cfg['notifier']['sticker']){
                    $returnBild = $telegram->sendSticker($bild);
                }
                $returnMessage = $telegram->sendMessage($name);
                $returnLocation = $telegram->sendLocation($location);
            } else {
                $returnMessage = $telegram->sendMessage($name);
            }



            if($returnMessage['ok'] != 1){
                Log::write("Pokemon " . $pokemon->getName($msg->pokemon_id) . ", Telegram Fehler: " . $returnMessage['description'] ." -> " . $chat_id);
            } else {
                if(empty($notifylist[$i]['iv_val']))
                    { Log::write($chat_id . " hat keine Spezifischen IV-Werte zu diesem Pokemon definiert, Benachrichtigung gesendet."); }
                elseif($notifylist[$i]['iv_val'] <= $IV )
                    { Log::write($chat_id . ", Benachrichtigung aufgrund zutreffenden IV-Wet gesendet"); }
            }
        } else {
            Log::write($chat_id . " hat den IV-Wert höher eingestellt");
        }


        $i++;
    }
}
?>
