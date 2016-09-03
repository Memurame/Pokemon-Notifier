<?php
/**
 * @titel Class für Tabelle der Chat Teilnehmer
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */
require_once("easyCRUD.php");

class cChat  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'chats';

    # Primary Key of the table
    protected $pk  = 'chat_id';

}