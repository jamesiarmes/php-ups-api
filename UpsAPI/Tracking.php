<?php
/**
 * Handles the sending, receiving, and processing of tracking data
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */

/**
 * Handles the sending, receiving, and processing of tracking data
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI_Tracking {
	/**
	 * Tracking number that we are requesting data about
	 * 
	 * @param string
	 * @access protected
	 */
	protected $tracking_number;
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 */
	public function __construct($tracking_number) {
		parent::__construct();
		
		$this->tracking_number = $tracking_number;
	} // end function __construct()
	
	/**
	 * Builds the XML used to make the request
	 * 
	 * @access protected
	 */
	protected function buildRequest() {
		$dom = new DOMDocument('1.0');
	} // end function buildRequest()
} // end class UpsAPI_Tracking

?>
