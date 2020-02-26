<?php
namespace WS\Classes;

/**
 * Class AbstractSettings
 * @package WS
 */
abstract class AbstractSettings
{
    public function logger($message)
    {
        $log =  date("F j, Y, g:i a") .': '.$message. PHP_EOL .
            "-------------------------" . PHP_EOL;
        $logDir = WS_PATH.'/logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        file_put_contents($logDir.  '/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);
    }

}