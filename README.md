# Pokemon-Notifier
![unbenannt](https://cloud.githubusercontent.com/assets/15847494/18547280/dbff5c22-7b42-11e6-83b6-3462d5dac425.png)

Das ist ein Telegram Bot für PokemonGo.
Dieser Bot wird über Webhook gesteuer, so sind die Benachrichtigungen stets in echtzeit.
Ebenfalls können die User selber einstellen zu welchen Pokémons Sie eine Benachrichtigung möchten.

### Voraussetzung
- [PokemonGo-Map](https://github.com/n30nl1ght/PokemonGo-Map)
- Mindestens PHP 5.6
- Mysql
- Telegram API-Key -> [BotFather](https://telegram.me/botfather)


### Installation
- Repositiry runterladen ```git clone https://github.com/n30nl1ght/Pokemon-Notifier.git```
- ```config.ini.Example``` kopieren und unbenennen in ```config.ini```
- In der config vom Notifier die DB-Verbindungsdaten, Sprache und Telegram API-Key eintragen
- In der config von der PokemonGo-Map musst du nun die Webhook Adresse entsprechend setzen. 
```z.B https://DEINEDOMAIN.CH/pokeHook.php```
- Die installation aufrufen die sich im Ordner ```/install/index.php```.
Diese Datei legt die erforderlichen Tabellen in der DB an.
Anschliessend unbedingt das ganze install Verzeichniss löschen.
- Die Telegram WebhookUrl setzen
```https://api.telegram.org/bot[API-KEY]/setWebhook?url=[URL zu deinem Script]/telegramHook.php```.
Die URL die du als Webhook angeben willst muss zwingend eine SSL-Verschlüsselung besitzen (Voraussetzung von Telegram).
- Damit der Bot dich immer als Admin einträgt wenn du den Bot beendest und erneut startest musst du deine Telegram-ID auslesen und in der Notifier config eintragen.
Du kannst diesen Wert jedoch auch leer lassen.


### TelegramBot Befehle
#### User Befehle
- /add = Hinzufügen von Pokémons
- /remove = Löschen von Pokémons
- /list = Anzeigen zu welchen Pokémons du eine Benachrichtigung erhälst
- /stop = Stopt die Benachrichtigngen (EInstellungen bleiben erhalten)
- /reset = STellt die Benachrichtigungs Pokémon wieder auf die STandart einstellung
- /start = Startet den Bot

#### Admin Befehle
- /send [Text] = Senden einer Nachricht an alle User die den Bot gestartet haben
- /cleandb = Löscht alle Pokémons aus der DB die nicht mehr auf der Map sind


