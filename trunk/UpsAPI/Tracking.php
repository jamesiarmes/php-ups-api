<?php
/**
 * Handles the sending, receiving, and processing of tracking data
 * 
 * Copyright (c) 2008, James I. Armes
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY COPYRIGHT HOLDERS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL COPYRIGHT HOLDERS AND CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
class UpsAPI_Tracking extends UpsAPI {
	/**
	 * Array of inquiry data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $inquiry_array;
	
	/**
	 * Tracking number that we are requesting data about
	 * 
	 * @access protected
	 * @var string
	 */
	protected $tracking_number;
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param string $tracking_number number of the pacaage(s) we are tracking
	 * @param array $inquiry array of inquiry data
	 */
	public function __construct($tracking_number = null, $inquiry = array()) {
		parent::__construct();
		
		// set object properties
		$this->server = $GLOBALS['ups_api']['server'].'/ups.app/xml/Track';
		$this->tracking_number = $tracking_number;
		$this->inquiry_array = $inquiry;
	} // end function __construct()
	
	/**
	 * Gets the current tracking number for the object
	 * 
	 * @access public
	 * @return string the current tracking number
	 */
	public function getTrackingNumber()
	{
		return $this->tracking_number;
	} // end function getTrackingNumber()
	
	/**
	 * Sets a new tracking number for the object
	 * 
	 * @access public
	 * @param string $value numeric tracking number
	 */
	public function setTrackingNumber($value)
	{
		$this->tracking_number = $value;
			
		return true;
	} // sets a new tracking number
	
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
		$access_element = $acces_dom->appendChild(
			new DOMElement('AccessRequest'));
		$access_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// creat the child elements
		$access_element->appendChild(
			new DOMElement('AccessLicenseNumber', $this->access_key));
		$access_element->appendChild(
			new DOMElement('UserId', $this->username));
		$access_element->appendChild(
			new DOMElement('Password', $this->password));
		
		
		/** create the TrackRequest element **/
		$track_element = $track_dom->appendChild(
			new DOMElement('TrackRequest'));
		$track_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
			
		// create the child elements
		$request_element = $track_element->appendChild(
			new DOMElement('Request'));
		if (!empty($this->tracking_number))
		{
			$track_element->appendChild(
				new DOMElement('TrackingNumber', $this->tracking_number));
		} // end if we have a tracking number
		
		// check to see if we have inquiry data
		if (!empty($this->inquiry_array))
		{
			$reference_number = $track_element->appendChild(
				new DOMElement('ReferenceNumber'));
			$reference_number = $reference_number->appendChild(
				new DOMElement('Value',
					$this->inquiry_array['reference_number']));
			$track_element->appendChild(
				new DOMElement('ShipperNumber',
					$this->inquiry_array['shipper_number']));
		} // end if we have inquiry data
		
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
		var_dump($track_xml);
		$return_value = $access_xml.$track_xml;
		
		return $return_value;
	} // end function buildRequest()
	
	/**
	 * Gets the number of packages related to the tracking number
	 * 
	 * @access public
	 * @return integer $return_value number of packages for this tracking number
	 */
	public function getNumberOfPackages()
	{
		$return_value = count(
			$this->response_array['Shipment']['Package']['Activity']);
		
		return $return_value;
	} // end function getNumberOfPackages()
	
	/**
	 * Gets the status of all packages
	 * 
	 * @access public
	 * @return array $return_value status of each package
	 */
	public function getPackageStatus()
	{
		$return_value = array();
		
		// iterate over the packages and create a status array for each
		$packages = $this->response_array['Shipment']['Package']['Activity'];
		foreach ($packages as $key => $current_package)
		{
			$status_type = $current_package['Status']['StatusType'];
			$return_value[$key] = array(
				'code' => $status_type['Code'],
				'description' => $status_type['Description'],
			); // end $return_value[$key]
		} // end for each package
		
		return $return_value;
	} // end function getPackageStatus()
	
	/**
	 * Gets the shipping address of the package(s)
	 * 
	 * @return array $return_value array of address information
	 */
	public function getShippingAddress()
	{
		$return_value = array();
		
		// get the address and iterate over its parts
		$address = $this->response_array['Shipment']['ShipTo']['Address'];
		foreach($address as $key => $address_part)
		{
			// check which address part this is
			switch ($key)
			{
				case 'AddressLine1':
					
					$return_value['address1'] = $address_part;
					break;
					
				case 'AddressLine2':
					
					$return_value['address2'] = $address_part;
					break;
					
				case 'City':
					
					$return_value['city'] = $address_part;
					break;
					
				case 'StateProvinceCode':
					
					$return_value['state'] = $address_part;
					break;
					
				case 'PostalCode':
					
					$return_value['zip_code'] = $address_part;
					break;
					
				case 'CountryCode':
					
					$return_value['country'] = $address_part;
					break;
					
				default:
					
					$return_value[$key] = $address_part;
					break;
					
			} // end switch ($key)
		} // end for each address part
		
		return $return_value;
	} // end function getShippingAddress()
	
	/**
	 * Gets the method used to ship the package(s)
	 * 
	 * @return array $return_value array of information about the shipping
	 * method
	 */
	public function getShippingMethod()
	{
		$service = $this->response_array['Shipment']['Service'];
		
		// create the array of shipping information
		$return_value = array(
			'code' => $service['Code'],
			'description' => $service['Description'],
		); // end $return_value
		
		return $return_value;
	} // end function getShippingMenthod()
} // end class UpsAPI_Tracking

?>
