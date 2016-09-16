<?php
/**
 * @titel Class für Tabelle der Benachrichtigungen
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */
require_once("easyCRUD.php");

class cNotifyIV  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'notify_iv';

    # Primary Key of the table
    protected $pk  = 'chat_id';

}