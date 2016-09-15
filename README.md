# Pokemon-Notifier
![unbenannt](https://cloud.githubusercontent.com/assets/15847494/18051339/16b9e64a-6df4-11e6-9fe5-f93545232da7.JPG)

Das ist ein Telegram Bot für PokemonGo.
Dieser Bot wird über Webhook gesteuer, so sind die Benachrichtigungen stets in echtzeit.
Ebenfalls können die User selber einstellen zu welchen Pokémons Sie eine Benachrichtigung möchten.

### Voraussetzung
- [PokemonGo-Map](https://github.com/n30nl1ght/PokemonGo-Map)
- Mysql
- Telegram API-Key -> [BotFather](https://telegram.me/botfather)


### Installation
- Repositiry runterladen ```git clone https://github.com/n30nl1ght/Pokemon-Notifier.git```
- ```config.ini.Example``` kopieren und unbenennen in ```config.ini```
- In der config vom Notifier die DB-Verbindungsdaten und Telegram API-Key eintragen
- In der config von der PokemonGo-Map musst du nun die Webhook Adresse entsprechend setzen. 
```z.B http://DEINEDOMAIN.CH/pokeHook.php```
- In der config der PokemonGo-Map ein webhook-api-key eintragen und denselben wert ebenfalls in der config vom Notifier eintragen.
So können dir keine anderen Personen Pokémons unterjubeln.
- Die Telegram WebhookUrl setzen
```https://api.telegram.org/bot[API-KEY]/setWebhook?url=[URL zu deinem Script]/telegramHook.php``` (Muss zwingend HTTPS sein)
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


