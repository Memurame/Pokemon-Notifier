<?php
/**
 * INstallation der DB Tabellen
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */


$sql= file_get_contents("database.sql");
$queries = explode(";",$sql);
foreach($queries as $query){ $db->query($query); }

echo "Installation beendet";

