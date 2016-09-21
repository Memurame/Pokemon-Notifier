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
    Log::write("Pokemon " . $pokemon->getName($msg->pokemon_id) . " per Webhook erhalten");
    $IV = ($msg->individual_attack + $msg->individual_defense + $msg->individual_stamina)/(15+15+15)*100;

    /**
     * Prüfen welcher chat notifications zum pokemon erhalten möchte
     */

    $db->bind("pokemon_id", $msg->pokemon_id);
    $db->bind("place", PLACE);
    $notifylist = $db->query("
        SELECT chats.chat_id, chats.place, notify_pokemon.pokemon_id, notify_iv.iv_val 
        FROM notify_pokemon 
        LEFT JOIN chats 
        ON notify_pokemon.chat_id = chats.chat_id 
        LEFT JOIN notify_iv
        ON notify_pokemon.chat_id = notify_iv.chat_id AND notify_pokemon.pokemon_id = notify_iv.pokemon_id
        WHERE notify_pokemon.pokemon_id = :pokemon_id
        AND chats.place = :place
        ORDER BY priority desc");
    $i = 0;

    if(empty($notifylist)){
        Log::write($pokemon->getName($msg->pokemon_id) .
            ", keine Benachrichtigungen zu diesem Pokemon gefunden."); }

    while($i < count($notifylist)){
        $chat_id = $notifylist[$i]['chat_id'];
        $time = date("i\m s\s", $msg->disappear_time - time());


        if($notifylist[$i]['iv_val'] <= $IV || empty($notifylist[$i]['iv_val'])){
            /**
             * Nachricht an telegram senden
             */
            $bild = array(
                'chat_id' => $chat_id,
                'sticker' => $pokemon->getSticker($msg->pokemon_id));
            $name = array(
                'chat_id' => $chat_id,
                'text' => "*".$pokemon->getName($msg->pokemon_id) . " *" .  Lang::get("iv") . "*" . number_format($IV, 1, ",", "'").
                    "*%\n" .  Lang::get("attack") . $msg->individual_attack." / " .  Lang::get("defense") . $msg->individual_defense ." / " .  Lang::get("stamina") . $msg->individual_stamina.
                    "\n\n" .  Lang::get("hit1") . $pokemon->getMoves($msg->move_1) . " (" . $pokemon->getMovesInfo($msg->move_1) . ")".
                    "\n" .  Lang::get("hit2") . $pokemon->getMoves($msg->move_2) . " (" . $pokemon->getMovesInfo($msg->move_2) . ")".
                    "\n\n" .  Lang::get("time") . date("H:i:s", $msg->disappear_time) ." ". "(" . $time . ")",
                'parse_mode' => 'Markdown');
            $location = array(
                'chat_id' => $chat_id,
                'latitude' => $msg->latitude,
                'longitude' => $msg->longitude);

            $returnBild = $telegram->sendSticker($bild);
            $returnMessage = $telegram->sendMessage($name);
            $returnLocation = $telegram->sendLocation($location);

            if($returnBild['ok'] != 1 || $returnMessage['ok'] != 1 || $returnLocation['ok'] != 1){
                Log::write("Pokemon " . $pokemon->getName($msg->pokemon_id) . ", Telegram Fehler: " . $returnBild['description'] ." -> " . $chat_id);
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
    Log::write("_____________________________________________");
}
?>
