<?php //-->
/*
 * This file is part of the Eden package.
 * (c) 2010-2012 Christian Blanquera <cblanquera@gmail.com>
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * Model Errors
 *
 * @package    Eden
 * @category   Model
 * @author     Christian Blanquera <cblanquera@gmail.com>
 * @version    $Id: exception.php 1 2010-01-02 23:06:36Z blanquera $
 */
class Eden_Session_Error extends Eden_Error_Class {
	/* Constants
	-------------------------------*/
	const NOT_STARTED = 'Session was not started yet. Try using Eden_Session->start().';
	
	/* Public Properties
	-------------------------------*/
	/* Protected Properties
	-------------------------------*/
	/* Private Properties
	-------------------------------*/
	/* Get
	-------------------------------*/
	public static function get($message = NULL, $code = 0) {
		$class = __CLASS__;
		return new $class($message, $code);
	}
	
	/* Magic
	-------------------------------*/
    /* Public Methods
	-------------------------------*/
	/* Protected Methods
	-------------------------------*/
	/* Private Methods
	-------------------------------*/
}