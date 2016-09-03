<?php
/**
 *
 * @author Thomas Hirter <t.hirter@outlook.com>
 */
require_once("easyCRUD.php");

class cChat  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'chats';

    # Primary Key of the table
    protected $pk  = 'chat_id';

}