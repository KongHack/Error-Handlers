<?php
namespace GCWorld\ErrorHandlers;

use \Exception;
use \PDOException;

class ErrorHandlers
{
	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		if(!(error_reporting() & $errno))
		{
			return false;
		}
		switch($errno)
		{
			case E_NOTICE		:
			case E_USER_NOTICE	:
				return false;
				break;
			case E_WARNING		:
			case E_USER_WARNING	:
			case E_STRICT		:
				$type = 'warning';
				$fatal = false;
				break;
			default			 :
				$type = 'fatal error';
				$fatal = true;
				break;
		}
		$output = '';
		$trace = array_reverse(debug_backtrace());
		array_pop($trace);
		if(php_sapi_name() == 'cli')
		{
			$output .= 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
			foreach($trace as $item)
			{
				$output .= '  ' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()' . "\n";
			}
		}
		else
		{
			$output .= '<p class="error_backtrace">' . "\n";
			$output .= '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
			$output .= '  <ol>' . "\n";
			foreach($trace as $item)
			{
				$output .= '	<li>' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
			}
			$output .= '  </ol>' . "\n";
			$output .= '</p>' . "\n";
		}
		$items = array();
		foreach($trace as $item)
		{
			$items[] = (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()';
		}
		$output .= '<br><br>Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join(' | ', $items);
		//error_log('----- START'.$message.'----- END');

		self::reportError($output, $errno, $trace);
		if($fatal)
		{
			exit(1);
		}
	}

	public static function exceptionHandler($exception)
	{
		$output = '';
		if ($exception instanceof PDOException)
		{
			$output .= 'PDO Exception:<br />'."\n";
			$output .= 'MESSAGE: '.$exception->getMessage()."<br />\n";
			$output .= 'FILE: '.$exception->getFile()."<br />\n";
			$output .= 'TRACE: ';
			$trace = array_reverse($exception->getTrace());
			//print_r($trace);
			$output .= '<p class="error_backtrace">' . "\n";
			$output .= '  <ol>' . "\n";
			foreach($trace as $item)
			{
				$output .= '	<li>' . (isset($item['file']) ? $item['file'] : '<unknown file>') . ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>') . ' calling ' . $item['function'] . '()</li>' . "\n";
			}
			$output .= '  </ol>' . "\n";
			$output .= '</p>' . "\n";

			$str = '<pre>'.htmlspecialchars(print_r($exception->getTrace(), true)).'</pre>';
			self::reportError($output."\n\n".$str, 0, $exception->getTrace());
			die();
		}
		elseif ($exception instanceof Exception)
		{
			if(function_exists('d'))
			{
				d($exception);
			}
			else
			{
				debug_print_backtrace();
			}
			trigger_error($exception->getMessage(), E_USER_WARNING);
		}
	}

	public static function reportError($message, $errno = 0, $trace = null)
	{

		//return false;
		if(function_exists('d'))
		{
			d($message);
			d($trace);
			if($trace != null)
			{
				echo '<br><br><pre>';
				print_r($trace);
				echo '</pre>';
			}
		}
		else
		{
			$display = false;
			if($display)
			{
				echo 'An error has occurred.  Staff has been contacted and should have the problem resolved shortly.<br /><br /><hr /><br />'.$message;
			}
		}
		$output ="
		Server: ".$_SERVER['SERVER_NAME']."<br />\n
		Request URI: ".$_SERVER['REQUEST_URI']."<br />\n
		IP Address: ".$_SERVER['REMOTE_ADDR']."<br />\n
		Error Output:\n".$message."<br />\n
		Request Array: <pre>".print_r($_REQUEST,true)."</pre>\n
		Session Array: <pre>".htmlspecialchars(print_r($_SESSION,true))."</pre>\n";
	}
	
	public static function shutdownHandler()
	{
		$err = error_get_last();
		if($err)
		{
			self::reportError(print_r($err,true));
		}
	}


}