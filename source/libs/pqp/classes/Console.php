<?php

/* - - - - - - - - - - - - - - - - - - - - -

 Title : PHP Quick Profiler Console Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : This class serves as a wrapper around a global
 php variable, debugger_logs, that we have created.
 
 Edit [July, 16 2009 by Chris Jaure]: Created a static variable
 to store logs instead of a global var.

- - - - - - - - - - - - - - - - - - - - - */

class Console {
	
	private static $logs = array();
	
	/*-----------------------------------
	     LOG A VARIABLE TO CONSOLE
	------------------------------------*/
	
	public static function log($data) {
		$logItem = array(
			"data" => $data,
			"type" => 'log'
		);
		self::$logs['console'][] = $logItem;
		self::$logs['logCount'] += 1;
	}
	
	/*---------------------------------------------------
	     LOG MEMORY USAGE OF VARIABLE OR ENTIRE SCRIPT
	-----------------------------------------------------*/
	
	public static function logMemory($object = false, $name = 'PHP') {
		$memory = memory_get_usage();
		if($object) $memory = strlen(serialize($object));
		$logItem = array(
			"data" => $memory,
			"type" => 'memory',
			"name" => $name,
			"dataType" => gettype($object)
		);
		self::$logs['console'][] = $logItem;
		self::$logs['memoryCount'] += 1;
	}
	
	/*-----------------------------------
	     LOG A PHP EXCEPTION OBJECT
	------------------------------------*/
	
	public static function logError($exception, $message) {
		$logItem = array(
			"data" => $message,
			"type" => 'error',
			"file" => $exception->getFile(),
			"line" => $exception->getLine()
		);
		self::$logs['console'][] = $logItem;
		self::$logs['errorCount'] += 1;
	}
	
	/*------------------------------------
	     POINT IN TIME SPEED SNAPSHOT
	-------------------------------------*/
	
	public static function logSpeed($name = 'Point in Time') {
		$logItem = array(
			"data" => PhpQuickProfiler::getMicroTime(),
			"type" => 'speed',
			"name" => $name
		);
		self::$logs['console'][] = $logItem;
		self::$logs['speedCount'] += 1;
	}
	
	/*-----------------------------------
	     SET DEFAULTS & RETURN LOGS
	------------------------------------*/
	
	public static function getLogs() {
		if(!self::$logs['memoryCount']) self::$logs['memoryCount'] = 0;
		if(!self::$logs['logCount']) self::$logs['logCount'] = 0;
		if(!self::$logs['speedCount']) self::$logs['speedCount'] = 0;
		if(!self::$logs['errorCount']) self::$logs['errorCount'] = 0;
		return self::$logs;
	}
}

?>
