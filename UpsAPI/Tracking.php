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
	 * If $customer_context is an array it should be in the format:
	 * $customer_context = array('Element' => 'Value');
	 * 
	 * @access public
	 * @param array|string $cutomer_context customer data
	 * @return string $return_value request XML
	 */
	public function buildRequest($customer_context = null)
	{
		/** create DOMDocument objects **/
		$acces_dom = new DOMDocument('1.0');
		$track_dom = new DOMDocument('1.0');
		
		
		/** create the AccessRequest element **/
		$access_element = $acces_dom->appendChild(new DOMElement('AccessRequest'));
		$access_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// creat the child elements
		$access_element->appendChild(
			new DOMElement('AccessLicenseNumber', $this->access_key));
		$access_element->appendChild(
			new DOMElement('UserId', $this->username));
		$access_element->appendChild(
			new DOMElement('Password', $this->password));
		
		
		/** create the TrackRequest element **/
		$track_element = $track_dom->appendChild(new DOMElement('TrackRequest'));
		$track_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
			
		// create the child elements
		$request_element = $track_element->appendChild(
			new DOMElement('Request'));
		$track_element->appendChild(
			new DOMElement('TrackingNumber', $this->tracking_number));
		
		// create the children of the Request element
		$transaction_element = $request_element->appendChild(
			new DOMElement('TransactionReference'));
		$request_element->appendChild(
			new DOMElement('RequestAction', 'Track'));
		$request_element->appendChild(
			new DOMElement('RequestOption', 'activity'));
		
		// create the children of the TransactionReference element
		$transaction_element->appendChild(
			new DOMElement('XpciVersion', '1.0001'));
		
		// check if we have customer data to include
		if (!empty($customer_context))
		{
			if (is_array($customer_context))
			{
				$customer_element = $transaction_element->appendChild(
					new DOMElement('CustomerContext'));

				// iterate over the array of customer data
				foreach ($customer_context as $element => $value)
				{
					$customer_element->appendChild(
						new DOMElement($element, $value));
				} // end for each customer data
			} // end if the customer data is an array
			else
			{
				$transaction_element->appendChild(
					new DOMElement('CustomerContext', $customer_context));
			} // end if the customer data is a string
		} // end if we have customer data to include
		
		
		/** generate the XML **/
		$access_xml = $acces_dom->saveXML();
		$track_xml = $track_dom->saveXML();
		$return_value = $access_xml.$track_xml;
		
		return $return_value;
	} // end function buildRequest()
} // end class UpsAPI_Tracking

?>
