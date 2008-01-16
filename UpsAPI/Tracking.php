<?php
/**
 * Handles the sending, receiving, and processing of tracking data
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */

/**
 * Include the configuration file
 */

/**
 * Handles the sending, receiving, and processing of tracking data
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI_Tracking extends UpsAPI {
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
		
		// set object properties
		$this->server = $GLOBALS['ups_api']['server'].'/ups.app/xml/Track';
		$this->tracking_number = $tracking_number;
	} // end function __construct()
	
	/**
	 * Builds the XML used to make the request
	 * 
	 * @access public
	 */
	public function buildRequest() {
		$return_value =
			'<?xml version="1.0"?>'."\n".
			'<AccessRequest xml:lang="en-US">'."\n".
  			'	<AccessLicenseNumber>'.$this->access_key.
  				'</AccessLicenseNumber>'."\n".
  			'	<UserId>'.$this->username.'</UserId>'."\n".
  			'	<Password>'.$this->password.'</Password>'."\n".
  			'</AccessRequest>'."\n".
			'<?xml version="1.0"?>'."\n".
			'<TrackRequest xml:lang="en-US">'."\n".
			'	<Request>'."\n".
			'		<TransactionReference>'."\n".
			'			<CustomerContext>QAST Track</CustomerContext>'."\n".
			'			<XpciVersion>1.0001</XpciVersion>'."\n".
			'		</TransactionReference>'."\n".
			'		<RequestAction>Track</RequestAction>'."\n".
			'		<RequestOption>activity</RequestOption>'."\n".
			'	</Request>'."\n".
			'	<TrackingNumber>'.$this->tracking_number.'</TrackingNumber>'."\n".
			'</TrackRequest>';
		
		return $return_value;
	} // end function buildRequest()
} // end class UpsAPI_Tracking

?>
