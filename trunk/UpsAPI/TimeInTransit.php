<?php
/**
 * Get shipping time based on origin and destination address
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
 * @todo Implement
 */

/**
 * Get shipping time based on origin and destination address
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI_TimeInTransit extends UpsAPI {
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
} // end class UpsAPI_TimeInTransit

?>
