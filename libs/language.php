<?php
/**
 * Logging class
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @version 0.1.0
 */

class Lang
{
    static private $lang;
    static private $dir = "/lang/";
    static private $file;

    public static function set($var){
        self::$lang = self::getlanguage($var);
    }

    private static function getlanguage($var){
        self::$file = ROOT . self::$dir .$var.'.json';
        if(file_exists(self::$file))
        { return json_decode(file_get_contents(self::$file)); }
        else
        { Log::write("The language file does not exist", true); }
    }

    public static function get($text){
        if(empty(self::$lang)){ Log::write("There was no defined language", true); }
        if(array_key_exists($text, self::$lang))
        { return self::$lang->$text; }
        else
        { Log::write("The text could not be translated: " . $text); }

    }

}