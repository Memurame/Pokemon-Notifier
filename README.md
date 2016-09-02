# Pokemon-Notifier
![unbenannt](https://cloud.githubusercontent.com/assets/15847494/18051339/16b9e64a-6df4-11e6-9fe5-f93545232da7.JPG)

Dies ist ein PHP Script mit dem die raw_data von [PokemonGo-Map](https://github.com/PokemonGoMap/PokemonGo-Map)  angezapt wird und entsprechend eine Notification an eine Telegram Gruppe gesendet wird.
Um das Script zu betreiben wird eine DB vorausgesetzt und die Möglichkeit einen Cronjob zu starten.

### Installation
1. Telegram Bot anlegen [BotFather](https://telegram.me/botfather)
2. Telegram Kanal erstellen
3. Repository runterladen ```git clone git@github.com:n30nl1ght/Pokemon-Notifier.git```
4. Datenbank erstellen
5. ```createTable.sql``` ausführen
6. ```config_example.ini``` unbenennen zu ```config.ini``` und die nötigen Daten angeben.
8. Cronjob erstellen und die Datei ```runscript.php``` ausführen lassen.

