# Pokemon-Notifier
![unbenannt](https://cloud.githubusercontent.com/assets/15847494/18051339/16b9e64a-6df4-11e6-9fe5-f93545232da7.JPG)

Das ist ein Telegram Bot für PokemonGo.
Dieser Bot wird über Webhook gesteuer, so sind die Benachrichtigungen stets in echtzeit.
Ebenfalls können die User selber einstellen zu welchen Pokémons Sie eine Benachrichtigung möchten.

### Voraussetzung
- [PokemonGo-Map](https://github.com/PokemonGoMap/PokemonGo-Map)
- Mysql
- Telegram API-Key -> [BotFather](https://telegram.me/botfather)


### Installation
- Repositiry runterladen ```git clone https://github.com/PokemonGoMap/PokemonGo-Map.git```
- ```config.ini.Example``` kopieren und unbenennen in ```config.ini```
- In der config die DB-Verbindungsdaten und Telegram API-Key eintragen
- In der config von der PokemonGo-Map musst du nun die Webhook Adresse entsprechend setzen. 
```z.B http://DEINEDOMAIN.CH/pokeHook.php```
- Die Telegram WebhookUrl setzen
```https://api.telegram.org/bot[API-KEY]/setWebhook?url=[URL zu deinem Script]```

So wenn dies alles geklappt hat kannst du nun dein Bot Starten und solltest eine Meldung erhalten.
Hat auch dies geklappt steht dem Starten der PokemonGo-Map nichts mehr im wege.


