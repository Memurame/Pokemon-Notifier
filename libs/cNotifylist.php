<?php
/**
 *
 * @author Thomas Hirter <t.hirter@outlook.com>
 */
require_once("easyCRUD.php");

class cNotifylist  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'notifylist';

    # Primary Key of the table
    protected $pk  = 'chat_id';

}