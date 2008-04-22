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
	 * Request data
	 * 
	 * @access protected
	 * @param array
	 */
	protected $data;
	
	/**
	 * Destination data
	 * 
	 * @access protected
	 * @param array
	 */
	protected $destination;
	
	/**
	 * Origin data
	 * 
	 * @access protected
	 * @param array
	 */
	protected $origin;
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param array $origin array of origin data
	 * @param array $destination array of destination data
	 * @param array $data array of request data
	 */
	public function __construct($origin, $destination, $data) {
		parent::__construct();
		
		// set object properties
		$this->server      =
			$GLOBALS['ups_api']['server'].'/ups.app/xml/TimeInTransit';
		$this->origin      = $origin;
		$this->destination = $destination;
		$this->data        = $data;
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
		$transit_dom = new DOMDocument('1.0');
		
		
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
		
		
		/** create the TimeInTransitRequest element **/
		$transit_element = $transit_dom->appendChild(
			new DOMElement('TimeInTransitRequest'));
		$transit_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$request_element = $transit_element->appendChild(
			new DOMElement('Request'));
		$transit_from_element = $transit_element->appendChild(
			new DOMElement('TransitFrom'));
		$transit_to_element = $transit_element->appendChild(
			new DOMElement('TransitTo'));
		
		// create the children of the Request element
		$transaction_element = $request_element->appendChild(
			new DOMElement('TransactionReference'));
		$request_element->appendChild(
			new DOMElement('RequestAction', 'TimeInTransit'));
		
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
		
		
		/** create the children of the TransitFrom Element **/
		// check if a city was entered
		$from_address_element = $transit_from_element->appendChild(
			new DOMElement('AddressArtifactFormat'));
		$create = (!empty($this->origin['name']))
			? $from_address_element->appendChild(new DOMElement(
				'Consignee', $this->origin['name'])) : false;
		$create = (!empty($this->origin['street_number']))
			? $from_address_element->appendChild(new DOMElement(
				'StreetNumberLow',
					$this->origin['street_number'])) : false;
		$create = (!empty($this->origin['street']))
			? $from_address_element->appendChild(new DOMElement(
				'StreetName', $this->origin['street'])) : false;
		$create = (!empty($this->origin['street_type']))
			? $from_address_element->appendChild(new DOMElement(
				'StreetType',
					$this->origin['street_type'])) : false;
		$create = (!empty($this->origin['city']))
			? $from_address_element->appendChild(new DOMElement(
				'PoliticalDivision2',
					$this->origin['city'])) : false;
		$create = (!empty($this->origin['state']))
			? $from_address_element->appendChild(new DOMElement(
				'PoliticalDivision1',
					$this->origin['state'])) : false;
		$create = (!empty($this->origin['zip_code'])) 
			? $from_address_element->appendChild(new DOMElement(
				'PostcodePrimaryLow',
					$this->origin['zip_code'])) : false;
		$create = (!empty($this->origin['country'])) 
			? $from_address_element->appendChild(new DOMElement(
				'CountryCode',
					$this->origin['country'])) : false;
		unset($create);
		
		
		/** create the children of the TransitTo Element **/
		// check if a city was entered
		$to_address_element = $transit_to_element->appendChild(
			new DOMElement('AddressArtifactFormat'));
		$create = (!empty($this->destination['name']))
			? $to_address_element->appendChild(new DOMElement(
				'Consignee', $this->destination['name'])) : false;
		$create = (!empty($this->destination['street_number']))
			? $to_address_element->appendChild(new DOMElement(
				'StreetNumberLow',
					$this->destination['street_number'])) : false;
		$create = (!empty($this->destination['street']))
			? $to_address_element->appendChild(new DOMElement(
				'StreetName', $this->destination['street'])) : false;
		$create = (!empty($this->destination['street_type']))
			? $to_address_element->appendChild(new DOMElement(
				'StreetType',
					$this->destination['street_type'])) : false;
		$create = (!empty($this->destination['city']))
			? $to_address_element->appendChild(new DOMElement(
				'PoliticalDivision2',
					$this->destination['city'])) : false;
		$create = (!empty($this->destination['state']))
			? $to_address_element->appendChild(new DOMElement(
				'PoliticalDivision1',
					$this->destination['state'])) : false;
		$create = (!empty($this->destination['zip_code'])) 
			? $to_address_element->appendChild(new DOMElement(
				'PostcodePrimaryLow',
					$this->destination['zip_code'])) : false;
		$create = (!empty($this->destination['country'])) 
			? $to_address_element->appendChild(new DOMElement(
				'CountryCode',
					$this->destination['country'])) : false;
		unset($create);
		
		
		/** create the rest of the child elements **/
		// create the PickupDate element
		$transit_element->appendChild(
			new DOMElement('PickupDate',
				$this->data['pickup_date']));
		
		// create the MaximumListSize element if a value was passd in
		if (!empty($this->data['max_list_size']))
		{
			$transit_element->appendChild(
				new DOMElement('MaximumListSize',
					$this->data['max_list_size']));
		} // end if a maximum list size was set
		
		// create the InvoiceLineTotal element if a value was passed in
		if (!empty($this->data['invoice']))
		{
			$invoice_element = $transit_element->appendChild(
				new DOMElement('InvoiceLineTotal'));
			
			// check if a currency code was passed in
			if (!empty($this->data['invoice']['currency_code']))
			{
				$invoice_element->appendChild(
					new DOMElement('CurrencyCode',
						$this->data['invoice']['currency_code']));
			} // end if a currency code was passed in
			
			// check if a monetary value was passed in
			if (!empty($this->data['invoice']['monetary_value']))
			{
				$invoice_element->appendChild(
					new DOMElement('MonetaryValue',
						$this->data['invoice']['monetary_value']));
			} // end if a monetary value was passed in
		} // end if invoice values were set
		
		// create the ShipmentWeight element if a value was passed in
		if (!empty($this->data['weight']))
		{
			$weight_element = $transit_element->appendChild(
				new DOMElement('ShipmentWeight'));
			
			// check if unit of measure data was passed in
			if (!empty($this->data['weight']['unit_of_measure']))
			{
				$um_element = $weight_element->appendChild(
					new DOMElement('UnitOfMeasurement'));
			} // end if unit of measure was passed in

			// check if a unit of measure code was passed in
			if (!empty($this->data['weight']['unit_of_measure']['code']))
			{
				$um_element->appendChild(
					new DOMElement('Code',
						$this->data['weight']['unit_of_measure']['code']));
			} // end if a unit of measure code was passed in
			
			// check if a monetary value was passed in
			if (!empty($this->data['weight']['unit_of_measure']))
			{
				$um_element->appendChild(
					new DOMElement('Description',
						$this->data['weight']['unit_of_measure']['code']));
			} // end if a monetary value was passed in
			
			// check if a monetary value was passed in
			if (!empty($this->data['weight']['weight']))
			{
				$weight_element->appendChild(
					new DOMElement('Weight',
						$this->data['weight']['weight']));
			} // end if a monetary value was passed in
		} // end if invoice values were set
		
		
		/** generate the XML **/
		$access_xml = $acces_dom->saveXML();
		$transit_xml = $transit_dom->saveXML();
		$return_value = $access_xml.$transit_xml;
		
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
