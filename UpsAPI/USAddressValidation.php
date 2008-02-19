<?php
/**
 * Handles the validation of US Shipping Addresses
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 * @todo Implement
 */

/**
 * Handles the validation of US Shipping Addresses
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI_USAddressValidation extends UpsAPI {
	/**
	 * Shipping address that we are to validate
	 * 
	 * @access protected
	 * @param array
	 */
	protected $address;
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param array $address array of address parts to validate
	 */
	public function __construct($address) {
		parent::__construct();
		
		// set object properties
		$this->server = $GLOBALS['ups_api']['server'].'/ups.app/xml/AV';
		$this->address = $address;
	} // end function __construct()
	
	/**
	 * Gets the current city on the object
	 *	
	 * @access public
	 * @return string the current city
	 */
	public function getCity()
	{
		return $this->address['city'];
	} // end function getCity()
	
	/**
	 * Sets the city on the object
	 * 
	 * @access public
	 * @param string $city city to set on the object
	 */
	public function setCity($city)
	{
		$this->address['city'] = $city;
		
		return true;
	} // end function setCity()
	
	/**
	 * Gets the current full address on the object
	 * 
	 * @access public
	 * @return array the current address
	 */
	public function getFullAddress()
	{
		return $this->address;
	} // end function getFullAddress()
	
	/**
	 * Sets the full address on the object
	 * 
	 * @access public
	 * @param array $address address to set on the object
	 */
	public function setFullAddress($address)
	{
		$this->address = $address;
		
		return true;
	} // end function setFullAddress()
	
	/**
	 * Gets the current state on the object
	 * 
	 * @access public
	 * @return string the current state
	 */
	public function getState()
	{
		return $this->address['state'];
	} // end function getState()
	
	/**
	 * Sets the state on the object
	 * 
	 * @access public
	 * @param string $state state to set on the object
	 */
	public function setState($state)
	{
		$this->address['state'] = $state;
		
		return true;
	} // end function setState()
	
	/**
	 * Gets the current zip code on the object
	 * 
	 * @access public
	 * @return integer the curret zip code
	 */
	public function getZipCode()
	{
		return $this->address['zip_code'];
	} // end function getZipCode()
	
	/**
	 * Sets the zip code on the object
	 * 
	 * @access public
	 * @param integer $zip_code zip code to set on the object
	 */
	public function setZipCode($zip_code)
	{
		$this->address['zip_code'] = $zip_code;
	} // end function setZipCode()
	
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
		$address_dom = new DOMDocument('1.0');
		
		
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
		
		
		/** create the AddressValidationRequest element **/
		$address_element = $address_dom->appendChild(
			new DOMElement('AddressValidationRequest'));
		$address_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
			
		// create the child elements
		$request_element = $address_element->appendChild(
			new DOMElement('Request'));
		$address_element = $address_element->appendChild(
			new DOMElement('Address'));
		
		// create the children of the Request element
		$transaction_element = $request_element->appendChild(
			new DOMElement('TransactionReference'));
		$request_element->appendChild(
			new DOMElement('RequestAction', 'AV'));
		
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
		
		
		/** create the children of the Address Element **/
		// check if a city was entered
		$create = (!empty($this->address['city']))
			? $address_element->appendChild(new DOMElement(
				'City', $this->address['city'])) : false;
		$create = (!empty($this->address['state']))
			? $address_element->appendChild(new DOMElement(
				'StateProvinceCode', $this->address['state'])) : false;
		$create = (!empty($this->address['zip_code'])) 
			? $address_element->appendChild(new DOMElement(
				'PostalCode', $this->address['zip_code'])) : false;
		unset($create);
		
		
		/** generate the XML **/
		$access_xml = $acces_dom->saveXML();
		$address_xml = $address_dom->saveXML();
		$return_value = $access_xml.$address_xml;
		
		return $return_value;
	} // end function buildRequest()
	
	/**
	 * Gets any matches from the response including their rank and quality
	 * 
	 * @access public
	 * @param string $match_type type of match returned by getMatchType()
	 * @return array $return_value array of matches
	 */
	public function getMatches($match_type = null)
	{
		$return_value = array();
		
		// check if a valid match type was passed in
		$valid_match = $this->validateMatchType($match_type);
		if (empty($match_type))
		{
			$match_type = $this->getMatchType();
		} // end if no valid match type was passed in
		
		// check if there are any matches
		if ($match_type == 'None')
		{
			return $return_value;
		} // end if there are not matches
		
		// check if we only have one match
		$match_array = $this->response_array['AddressValidationResult'];
		if (!isset($match_array[0]))
		{
			$return_value[0] = array(
				'quality' => $match_array['Quality'],
				'address' => array(
					'city' => $match_array['Address']['City'],
					'state' => $match_array['Address']['StateProvinceCode'],
				),
				'zip_code_low' => $match_array['PostalCodeLowEnd'],
				'zip_code_high' => $match_array['PostalCodeHighEnd'],
			); // end $return_value
		} // end if we only have one match
		else
		{
			// iterate over the matches
			foreach ($match_array as $current_match)
			{
				$return_value[] = array(
					'quality' => $current_match['Quality'],
					'address' => array(
						'city' => $current_match['Address']['City'],
						'state' => $current_match['Address']['StateProvinceCode'],
					),
					'zip_code_low' => $current_match['PostalCodeLowEnd'],
					'zip_code_high' => $current_match['PostalCodeHighEnd'],
				); // end $return_value
			} // end for each match
		} // end if we have multiple matches
		
		return $return_value;
	} // end function getMatches()
	
	/**
	 * Returns the type of match(s)
	 * 
	 * @access public
	 * @return string $return_value whether or not a full or partial match was
	 * found
	 */
	public function getMatchType()
	{
		// check if we received any matched
		if (!isset($this->response_array['AddressValidationResult']))
		{
			return 'None';
		} // end if we received no matches
		
		$match_array = $this->response_array['AddressValidationResult'];
		switch ($match_array)
		{
			case isset($match_array['Quality'])
				&& $match_array['Quality'] == '1.0':
				
				$return_value = 'Exact';
				break;
				
			case isset($match_array['Quality']):
				
				$return_value = 'Partial';
				break;
			
			case sizeof($match_array) > 1:
				
				// iterate over the results to see if we have an exact match
				foreach ($match_array as $result)
				{
					if ($result['Quality'] == '1.0')
					{
						$return_value = 'Multiple With Exact';
						break(2);
					} // end if the match is an exact
				} // end for each result
				
				$return_value = 'Multiple Partial';
				break;
			
			default:
				
				$return_value = false;
				break;
		} // end switch ($match_array)
		
		return $return_value;
	} // end function getMatchType()
	
	/**
	 * Checks a match type to see if it is valid
	 * 
	 * @access protected
	 * @param string $match_type match type to validate
	 * @return bool whether or not the match type is valid
	 */
	protected function validateMatchType($match_type)
	{
		// declare the valid match types
		$valid_match_types = array('Exact', 'None', 'Partial',
			'Multiple With Exact', 'Multiple Partial');
		
		return in_array($match_type, $valid_match_types);
	} // end function validateMatchType()
} // end class UpsAPI_USAddressValidation

?>
