<?php
/**
 *
 * @author Thomas Hirter <t.hirter@outlook.com>
 */
require_once("easyCRUD.php");

class cPokemon  Extends Crud {

    # The table you want to perform the database actions on
    protected $table = 'pokemon';

    # Primary Key of the table
    protected $pk  = 'id';

}