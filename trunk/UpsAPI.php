<?php
/**
 * PHP API for use with UPS OnLine Tools.  This is the main class that
 * all other classes will extend.
 *
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
 
/**
 * Include the configuration file
 */
require_once 'config.php';

/**
 * Parent class for the UpsAPI
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI {
	/**
	 * Access key provided by UPS
	 * 
	 * @param string
	 * @access protected
	 */
	protected $access_key;
	
	/**
	 * Developer key provided by UPS
	 * 
	 * @param string
	 * @access protected
	 */
	protected $developer_key;
	
	/**
	 * Password used to access UPS Systems
	 * 
	 * @param string
	 * @access protected
	 */
	protected $password;
	
	/**
	 * Username used to access UPS Systems
	 * 
	 * @param string
	 * @access protected
	 */
	protected $username;
	
	/**
	 * Sets up the API Object
	 * 
	 * @access public
	 */
	public function __construct() {
		/** Set the Keys on the Object **/
		$this->access_key = $GLOBALS['ups_api']['access_key'];
		$this->developer_key = $GLOBALS['ups_api']['developer_key'];
		
		
		/** Set the username and password on the Object **/
		$this->password = $GLOBALS['ups_api']['password'];
		$this->username = $GLOBALS['ups_api']['username'];
	} // end funciton __construct()
	
	/**
	 * Send a request to the UPS Server using xmlrpc
	 * 
	 * @params string $request_xml
	 */
	public function sendRequest($request_xml) {
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: text/xml',
				'content' => $request_xml,
			),
		));
	} // end function sendRequest()
} // end class UpsAPI

?>
