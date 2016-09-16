<?php
/**
 * Logging class
 * @author Thomas Hirter <t.hirter@outlook.com>
 * @version 0.1.0
 */

class Log {

    /**
     * @var string
     */
    static private $path = '/logs/';

    /**
     * @return string
     */
    static private function init() {
        date_default_timezone_set('Europe/Zurich');
        return dirname(__FILE__, 2) . self::$path;

    }

    /**
     * @param string $message
     */
    static public function write($message, $exit = false) {
        self::$path = self::init();

        $date = new DateTime();
        $log = self::$path . $date->format('Y-m-d').".txt";
        if(is_dir(self::$path)) {
            if(!file_exists($log)) {
                $fh  = fopen($log, 'a+') or die("Fatal Error !");
                $logcontent = "Time : " . $date->format('H:i:s')."\r\n" . $message ."\r\n";
                fwrite($fh, $logcontent);
                fclose($fh);
            }
            else {
                self::edit($log, $date, $message);
            }
        }
        else {
            if(mkdir(self::$path,0777) === true)
            {
                self::write($message);
            }
        }
        if($exit){ die("<b>Unhandled Exception</b><br>You can find the error back in the log"); }
    }

    /**
     * @param string $log
     * @param DateTimeObject $date
     * @param string $message
     */
    static private function edit($log,$date,$message) {
        $logcontent = "Time : " . $date->format('H:i:s')."\r\n" . $message ."\r\n\r\n";
        $logcontent = $logcontent . file_get_contents($log);
        file_put_contents($log, $logcontent);
    }
}