<?php
namespace GCWorld\ErrorHandlers;

use \Exception;
use \PDOException;

class ErrorHandlers
{
    /**
     * @param $errorNumber
     * @param $errorMessage
     * @param $errorFile
     * @param $errorLine
     *
     * @throws \ErrorException
     *
     * @return void
     */
    public static function errorHandler($errorNumber, $errorMessage, $errorFile, $errorLine)
    {
        throw new \ErrorException($errorMessage, 0, $errorNumber, $errorFile, $errorLine);
    }

    public static function exceptionHandler($exception)
    {
        $output = '';
        if ($exception instanceof PDOException) {
            $output .= 'PDO Exception:<br />'."\n";
            $output .= 'MESSAGE: '.$exception->getMessage()."<br />\n";
            $output .= 'FILE: '.$exception->getFile()."<br />\n";
            $output .= 'TRACE: ';
            $trace = array_reverse($exception->getTrace());
            //print_r($trace);
            $output .= '<p class="error_backtrace">' . "\n";
            $output .= '  <ol>' . "\n";
            foreach ($trace as $item) {
                $output .= '	<li>' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
            }
            $output .= '  </ol>' . "\n";
            $output .= '</p>' . "\n";

            $str = '<pre>'.htmlspecialchars(print_r($exception->getTrace(), true)).'</pre>';
            self::reportError($output."\n\n".$str, 0, $exception->getTrace());
            return true;
        } elseif ($exception instanceof Exception) {
            if (function_exists('d')) {
                d($exception);
            } else {
                debug_print_backtrace();
            }
            trigger_error($exception->getMessage(), E_USER_WARNING);
        }
        return false;
    }

    public static function reportError($message, $errno = 0, $trace = null)
    {

        //return false;
        if (function_exists('d')) {
            d($message);
            d($trace);
        } else {
            $display = false;
            if ($display) {
                echo 'An error has occurred.  Staff has been contacted and should have the problem resolved shortly.<br /><br /><hr /><br />'.$message;
            } else {
                if ($trace != null) {
                    echo '<br><br><pre>';
                    print_r($trace);
                    echo '</pre>';
                }
            }
        }
    }
    
    public static function shutdownHandler()
    {
        $err = error_get_last();
        if ($err) {
            self::reportError(print_r($err, true));
        }
    }
}
