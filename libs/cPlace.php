<?php
/**
 * @titel Class für Tabelle der Locations
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @git https://github.com/n30nl1ght/Pokemon-Notifier
 */
require_once("easyCRUD.php");

class cPlace  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'locations';

    # Primary Key of the table
    protected $pk  = 'id';

}