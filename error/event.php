<?php //-->
/*
 * This file is part of the Eden package.
 * (c) 2010-2012 Christian Blanquera <cblanquera@gmail.com>
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 *
 * @package    Eden
 * @category   error
 * @author     Christian Blanquera <cblanquera@gmail.com>
 * @version    $Id: exception.php 1 2010-01-02 23:06:36Z blanquera $
 */
class Eden_Error_Event extends Eden_Event {
	/* Constants
	-------------------------------*/
	//error type
	const PHP		= 'PHP';	//used when argument is invalidated
	const UNKNOWN	= 'UNKNOWN';
	
	//error level
	const WARNING 	= 'WARNING';
	const ERROR 	= 'ERROR';
	
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	protected static $_instance = NULL;
	
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function get() {
		$class = __CLASS__;
		if(is_null(self::$_instance)) {
			self::$_instance = new $class();
		}
		
		return self::$_instance;
	}
	
	/* Magic
	-------------------------------*/
	/* Public Methods
	-------------------------------*/
	/** 
	 * Called when a PHP error has occured. Must
	 * use setErrorHandler() first.
	 *
	 * @param number error number 
	 * @param string message
	 * @param string file
	 * @param string line
	 * @return true
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline) {
		//depending on the error number 
		//we can determine the error level
    	switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_WARNING:
			case E_USER_WARNING:
				$level = self::WARNING;
				break;
			case E_ERROR:
			case E_USER_ERROR:
			default:
				$level = self::ERROR;
				break;
        }
		
		//errors are only triggered through PHP
		$type = self::PHP;
		
		//get the trace
		$trace = debug_backtrace();
		
		//by default we do not know the class
		$class = self::UNKNOWN;
		
		//if there is a trace
		if(count($trace) > 1) {
			//formulate the class
			$class = $trace[1]['function'].'()';
			if(isset($trace[1]['class'])) {
				$class = $trace[1]['class'].'->'.$class;
			}
		}
		
		$this->trigger(
			'error',	$trace,		1,		
			$type, 		$level, 	$class, 		
			$errfile, 	$errline, 	$errstr);
		
		//Don't execute PHP internal error handler
		return true;
	}
	
	/** 
	 * Registers this class' error handler to PHP
	 *
	 * @return this
	 */
	public function setErrorHandler() {
		set_error_handler(array($this, 'errorHandler'));
		return $this;
	}
	
	/** 
	 * Returns default handler back to PHP
	 *
	 * @return this
	 */
	public function releaseErrorHandler() {
		restore_error_handler();
		return $this;
	}
	
	/** 
	 * Called when a PHP exception has occured. Must
	 * use setExceptionHandler() first.
	 *
	 * @param Exception
	 * @return void
	 */
	public function exceptionHandler(Exception $e) {
		//by default set LOGIC ERROR
		$type 		= Eden_Error::LOGIC;
		$level 		= Eden_Error::ERROR;
		$offset 	= 1;
		$reporter 	= get_class($e);
		
		//if the exception is an eden exception
		if($e instanceof Eden_Error) {
			//set type and level from that
			$type 		= $e->getType();
			$level 		= $e->getLevel();
			$offset 	= $e->getTraceOffset();
			$reporter 	= $e->getReporter();
		}
		
		//get trace
		$trace = $e->getTrace();
		
		$this->trigger(
			'exception',	$trace,			$offset,		
			$type, 			$level, 		$reporter, 		
			$e->getFile(), 	$e->getLine(), 	$e->getMessage());
	}
	
	/** 
	 * Registers this class' exception handler to PHP
	 *
	 * @return this
	 */
	public function setExceptionHandler() {
		set_exception_handler(array($this, 'exceptionHandler'));
		return $this;
	}
	
	/** 
	 * Returns default handler back to PHP
	 *
	 * @return this
	 */
	public function releaseExceptionHandler() {
		restore_exception_handler();
		return $this;
	}
	
	/**
	 * Sets reporting
	 *
	 * @param int
	 * @return this
	 */
	public function setReporting($type) {
		error_reporting($type);
		return $this;
	}
	
	
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}