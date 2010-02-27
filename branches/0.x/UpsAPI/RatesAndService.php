<?php
/**
 * Handles the sending, receiving, and processing of rates and service data
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
 * Handles the sending, receiving, and processing of rates and service data
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsAPI_RatesAndService extends UpsAPI {
	/**
	 * Customer classifcation code for the "occastional" classification
	 * 
	 * @var string
	 */
	const CUSTOMER_CLASSIFICATION_CODE_OCCASIONAL = '03';
	
	/**
	 * Customer classifcation code for the "retail" classification
	 * 
	 * @var string
	 */
	const CUSTOMER_CLASSIFICATION_CODE_RETAIL = '04';
	
	/**
	 * Customer classifcation code for the "wholesale" classification
	 *
	 * @var string
	 */
	const CUSTOMER_CLASSIFICATION_CODE_WHOLESALE = '01';
	
	/**
	 * Node name for the Monetary Value
	 * 
	 * @var string
	 */
	const NODE_NAME_MONETARY_VALUE = 'MonetaryValue';
	
	/**
	 * Node name for the Rated Shipment Node
	 * 
	 * @var string
	 */
	const NODE_NAME_RATED_SHIPMENT = 'RatedShipment';
	
	/**
	 * Node name for the root node
	 * 
	 * @var string
	 */
	const NODE_NAME_ROOT_NODE = 'RatingServiceSelectionResponse';
	
	/**
	 * Pickup type code for the "Air Service Center" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_AIR_SERVICE_CENTER = '20';
	
	/**
	 * Pickup type code for the "Customer Counter" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_CUSTOMER_COUNTER = '03';
	
	/**
	 * Pickup type code for the "Daily Pickup" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_DAILY_PICKUP = '01';
	
	/**
	 * Pickup type code for the "Letter Center" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_LETTER_CENTER = '19';
	
	/**
	 * Pickup type code for the "On Call Air" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_ON_CALL_AIR = '07';
	
	/**
	 * Pickup type code for the "One Time Pickup" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_ONE_TIME_PICKUP = '06';
	
	/**
	 * Pickup type code for the "Retail Rates" type
	 * 
	 * @var string
	 */
	const PICKUP_CODE_SUGGESTED_RETAIL_RATES = '11';
	
	// domestic only
	const SERVICE_CODE_NEXT_DAY_AIR_EARLY_AM = '14';
	const SERVICE_CODE_NEXT_DAY_AIR = '01';
	const SERVICE_CODE_NEXT_DAY_AIR_SAVER = '13';
	const SERVICE_CODE_SECOND_DAY_AIR_AM = '59';
	const SERVICE_CODE_SECOND_DAY_AIR = '02';
	const SERVICE_CODE_THREE_DAY_SELECT = '12';
	const SERVICE_CODE_GROUND = '03';
	
	// international only
	const SERVICE_CODE_STANDARD = '11';
	const SERVICE_CODE_WORLDWIDE_EXPRESS = '07';
	const SERVICE_CODE_WORLDWIDE_EXPRESS_PLUS = '54';
	const SERVICE_CODE_WORLDWIDE_EXPEDITED = '08';
	const SERVICE_CODE_SAVER = '65';
	
	// required for rating, ignored for shopping. poland to poland same day
	const SERVICE_CODE_UPS_TODAY_STANDARD = '82';
	const SERVICE_CODE_UPS_TODAY_DEDICATED_COURIER = '83';
	const SERVICE_CODE_UPS_TODAY_INTERCITY = '84';
	const SERVICE_CODE_UPS_TODAY_EXPRESS = '85';
	const SERVICE_CODE_UPS_TODAY_EXPRESS_SAVER = '86';
	
	/**
	 * Resource for the Rates and Service resource
	 * 
	 * @var string
	 */
	const SERVICE_RESOURCE = 'Rate';
	
	/**
	 * Destination (ship to) data
	 * 
	 * Should be in the format:
	 * $destination = array(
	 * 	'name' => '',
	 * 	'attn' => '',
	 * 	'phone' => '1234567890',
	 * 	'address' => array(
	 * 		'street1' => '',
	 * 		'street2' => '',
	 * 		'city' => '',
	 * 		'state' => '**',
	 * 		'zip' => 12345,
	 * 		'country' => '',
	 * 	),
	 * );
	 * 
	 * @access protected
	 * @var array
	 */
	protected $destination = array();
	
	/**
	 * Shipment data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $shipment = array();
	
	/**
	 * Ship from data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $ship_from = array();
	
	/**
	 * Shipper data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $shipper = array();
	
	/**
	 * Array of values to be used for the xml
	 * 
	 * @var array
	 */
	protected $values = array();
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param array $shipment array of shipment data
	 * @param array $shipper array of shipper data
	 * @param array $ship_from array of ship from data
	 * @param array $desination array of destination data
	 */
	public function __construct($shipment, $shipper, $ship_from,
		$desination, $server = self::SERVER_TESTING) {
		parent::__construct($server);
		
		// set object properties
		$this->server = $server.'/ups.app/xml/'.self::SERVICE_RESOURCE;
		$this->shipment = $shipment;
		$this->shipper = $shipper;
		$this->ship_from = $ship_from;
		$this->destination = $desination;
	} // end function __construct()
	
	/**
	 * Sets the values for the customer classification node
	 * 
	 * @param string $code
	 */
	public function setCustomerClassification($code)
	{
		$this->values['CustomerClassification']['Code'] = $code;
	} // end function setCustomerClassification()
	
	/**
	 * Sets the values for the pickup type node
	 * 
	 * @param string $code one of the PICKUP_CODE_* constants
	 */
	public function setPickupType($code)
	{
		$this->values['PickupType']['Code'] = $code;
		
		return true;
	} // end function setPickupType()
	
	/**
	 * Sets the values for the shipment/service node
	 * 
	 * @param string $code one of the SERVICE_CODE_* constants
	 * @param boolean $include_description whether or not to include the
	 * description to make the request (and response) more human readable
	 */
	public function setShipment_Service($code, $include_description = true) {
		$this->values['Shipment']['Service']['Code'] = $code;
		$this->values['Shipment']['Service']['Description'] = '';
		
		return true;
	} // end function setShipment_Service()
	
	/**
	 * Sets the values for the shipment/shipper node
	 * 
	 * @param UpsType_Address $address
	 * @param string $name
	 * @param string $shipper_number
	 */
	public function setShipment_Shipper($address, $name = null,
		$shipper_number = null)
	{
		$this->values['Shipment']['Shipper']['Name'] = $name;
		$this->values['Shipment']['Shipper']['ShipperNumber'] = $shipper_number;
		$this->values['Shipment']['Shipper']['Address'] = $address;
		
		return true;
	} // end function setShipment_Shipper()
	
	/**
	 * Sets the values for the shipment/shipfrom node
	 * 
	 * @param UpsType_Address $address
	 * @param string $name
	 */
	public function setShipment_ShipFrom($address, $name = null)
	{
		$this->values['Shipment']['ShipFrom']['CompanyName'] = $name;
		$this->values['Shipment']['ShipFrom']['Address'] = $address;
		
		return true;
	} // end function setShipment_ShipFrom()
	
	/**
	 * Sets the values for the shipment/shipto node
	 * 
	 * @param UpsType_Address $address
	 * @param string $name
	 */
	public function setShipment_ShipTo($address, $name = null)
	{
		$this->values['Shipment']['ShipTo']['CompanyName'] = $name;
		$this->values['Shipment']['ShipTo']['Address'] = $address;
		
		return true;
	} // end function setShipment_ShipTo()
	
	/**
	 * Returns charges for each package
	 * 
	 * @return array
	 */
	public function getPackageCharges() {
		$return_value = array();
		
		// iterate over the packages
		$packages = $this->xpath->query(self::NODE_NAME_RATED_SHIPMENT.
			'/RatedPackage', $this->root_node);
		foreach ($packages as $package) {
			$return_value[] = array(
				'currency_code' => $this->xpath->query(
					'TotalCharges/CurrencyCode',
					$package)->item(0)->nodeValue,
				'transportation' => $this->xpath->query(
					'TransportationCharges/'.self::NODE_NAME_MONETARY_VALUE,
					$package)->item(0)->nodeValue,
				'service_options' => $this->xpath->query(
					'ServiceOptionsCharges/'.self::NODE_NAME_MONETARY_VALUE,
					$package)->item(0)->nodeValue,
				'total' => $this->xpath->query(
					'TotalCharges/'.self::NODE_NAME_MONETARY_VALUE,
					$package)->item(0)->nodeValue,
			); // end $return_value
		} // end for each package
		
		return $return_value;
	} // end function getPackageCharges()
	
	/**
	 * Returns charges for each package
	 * 
	 * @return array
	 */
	public function getPackageWeight() {
		$return_value = array();
		
		// iterate over the packages
		$packages = $this->xpath->query(self::NODE_NAME_RATED_SHIPMENT.
			'/RatedPackage', $this->root_node);
		foreach ($packages as $package) {
			$return_value[] = array(
				'weight' => $this->xpath->query(
					'BillingWeight/Weight', $package)
					->item(0)->nodeValue,
				'units' => $this->xpath->query(
					'BillingWeight/UnitOfMeasurement/Code', $package)
					->item(0)->nodeValue,
			); // end $return_value
		} // end for each package
		
		return $return_value;
	} // end function getPackageCharges()
	
	/**
	 * Returns charges for the entire shipment
	 * 
	 * @return array
	 */
	public function getShipmentCharges() {
		$rated_shipment = $this->xpath->query(
			self::NODE_NAME_RATED_SHIPMENT, $this->root_node)->item(0);
		
		$return_value = array(
			'currency_code' => $this->xpath->query(
				'TotalCharges/CurrencyCode',
				$rated_shipment)->item(0)->nodeValue,
			'transportation' => $this->xpath->query(
				'TransportationCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
			'service_options' => $this->xpath->query(
				'ServiceOptionsCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
			'total' => $this->xpath->query(
				'TotalCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
		); // end $return_value
		
		return $return_value;
	} // end function
	
	/**
	 * Returns billing weight for the entire shipment
	 * 
	 * @return array
	 */
	public function getShipmentWeight() {
		$rated_shipment = $this->xpath->query(
			self::NODE_NAME_RATED_SHIPMENT, $this->root_node)->item(0);
		
		$return_value = array(
			'weight' => $this->xpath->query(
				'BillingWeight/Weight', $rated_shipment)->item(0)->nodeValue,
			'units' => $this->xpath->query(
				'BillingWeight/UnitOfMeasurement/Code', $rated_shipment)
				->item(0)->nodeValue,
		); // end $return_value
		
		return $return_value;
	} // end function getShipmentWeight()
	
	/**
	 * Returns any warnings from the response
	 * 
	 * @return array
	 */
	public function getWarnings() {
		$warnings = $this->xpath->query(self::NODE_NAME_RATED_SHIPMENT.
			'/RatedShipmentWarning', $this->root_node);
		
		// iterate over the warnings
		$return_value = array();
		foreach ($warnings as $warning) {
			$return_value[] = $warning->nodeValue;
		} // end for each warning
		
		return $return_value;
	} // end function getWarnings()
	
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
	public function buildRequest($customer_context = null) {
/*		return parent::buildRequest().'<?xml version="1.0" ?>
<RatingServiceSelectionRequest>
<Request>
<TransactionReference>
<CustomerContext>Rating and Service</CustomerContext>
<XpciVersion>1.0</XpciVersion>
</TransactionReference>
<RequestAction>Rate</RequestAction>
<RequestOption>Rate</RequestOption>
</Request>
<PickupType>
<Code>01</Code>
<Description>Daily Pickup</Description>
</PickupType>
<Shipment>
<Description>Rate Shopping - Domestic</Description>
<Shipper>
<ShipperNumber>ISGB01</ShipperNumber>
<Address>
<AddressLine1>Southam Rd</AddressLine1>
<AddressLine2 />
<AddressLine3 />
<City>Dunchurch</City>
<StateProvinceCode>Warwickshire</StateProvinceCode>
<PostalCode>CV226PD</PostalCode>
<CountryCode>GB</CountryCode>
</Address>
</Shipper>
<ShipTo>
<CompanyName>Belgium</CompanyName>
<AttentionName>nanananan</AttentionName>
<PhoneNumber>7777778978</PhoneNumber>
<Address>
<AddressLine1>5, rue de la Bataille</AddressLine1>
<AddressLine2 />
<AddressLine3 />
<City>Neufchateau</City>
<PostalCode>6840</PostalCode>
<CountryCode>BE</CountryCode>
</Address>
</ShipTo>
<ShipFrom>
<CompanyName>Imani\'s Imaginarium</CompanyName>
<AttentionName>AT:United Kingdom</AttentionName>
<PhoneNumber>3057449002</PhoneNumber>
<FaxNumber>3054439293</FaxNumber>
<Address>
<AddressLine1>Southam Rd</AddressLine1>
<AddressLine2 />
<AddressLine3 />
<City>Dunchurch</City>
<StateProvinceCode>Warwickshire</StateProvinceCode>
<PostalCode>CV226PD</PostalCode>
<CountryCode>GB</CountryCode>
</Address>
</ShipFrom>
<Service><Code>65</Code></Service>
<Package>
<PackagingType>
<Code>04</Code>
<Description>UPS 25KG Box</Description>
</PackagingType>
<Description>Rate</Description>
<PackageWeight>
<UnitOfMeasurement>
<Code>KGS</Code>
</UnitOfMeasurement>
<Weight>23</Weight>
</PackageWeight>
</Package>
<ShipmentServiceOptions />
</Shipment>
</RatingServiceSelectionRequest>';*/
		// create the new dom document
		$xml = new DOMDocument('1.0', 'utf-8');
//		$xml->createElementNS($namespaceURI, $qualifiedName, $value)
		
		
		/** create the AddressValidationRequest element **/
		$rate = $xml->appendChild(
			new DOMElement('RatingServiceSelectionRequest'));
		$rate->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$requst = $this->buildRequest_RequestElement($rate,
			'Rate', 'Rate', $customer_context);
		
		
		/** build the pickup type node **/
		$pickup_type = $rate->appendChild(new DOMElement('PickupType'));
		$pickup_type->appendChild(new DOMElement('Code',
			$this->shipment['pickup_type']['code']));
		$pickup_type->appendChild(new DOMElement('Description',
			$this->shipment['pickup_type']['description']));
		var_dump($this->shipment['pickup_type']);
		
		$shipment = $rate->appendChild(new DOMElement('Shipment'));
		
		$this->buildRequest_Shipper($shipment);
		$this->buildRequest_Destination($shipment);
		$this->buildRequest_ShipFrom($shipment);
		$shipment = $this->buildRequest_Shipment($shipment);
		
		return parent::buildRequest().$xml->saveXML();
	} // end function buildRequest()
	
	/**
	 * Builds the destination elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Destination(&$dom_element) {
		/** build the destination element and its children **/
		$destination = $dom_element->appendChild(new DOMElement('ShipTo'));
		$destination->appendChild(new DOMElement('CompanyName',
			$this->destination['name']));
		$destination->appendChild(new DOMElement('PhoneNumber',
			$this->destination['phone']));
		$address = $destination->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->destination['street']));
		
		// check to see if there is a second steet line
		if (isset($this->destination['street2']) &&
			!empty($this->destination['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->destination['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->destination['street3']) &&
			!empty($this->destination['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->destination['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->destination['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->destination['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->destination['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->destination['country']));
		
		return $destination;
	} // end function buildRequest_Destination()
	
	/**
	 * Buildes the package elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @param array $package
	 * @return DOMElement
	 * 
	 * @todo determine if the package description is needed
	 */
	protected function buildRequest_Package(&$dom_element, $package) {
		/** build the package and packaging type **/
		$package_element = $dom_element->appendChild(new DOMElement('Package'));
		$packaging_type = $package_element->appendChild(
			new DOMElement('PackagingType'));
		$packaging_type->appendChild(new DOMElement('Code',
			$package['packaging']['code']));
		$packaging_type->appendChild(new DOMElement('Description',
			$package['packaging']['description']));
		
		// TODO: determine if we need this
		$package_element->appendChild(new DOMElement('Description',
			$package['description']));
		
		
		/** build the package weight **/
		$package_weight = $package_element->appendChild(
			new DOMElement('PackageWeight'));
		$units = $package_weight->appendChild(
			new DOMElement('UnitOfMeasurement'));
		$units->appendChild(new DOMElement('Code', $package['units']));
		$package_weight->appendChild(
			new DOMElement('Weight', $package['weight']));
		
		return $package_element;
	} // end function buildRequest_Package()
	
	/**
	 * Builds the service options node
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return boolean|DOMElement
	 */
	protected function buildRequest_ServiceOptions(&$dom_element) {
		// build our elements
		$service_options = $dom_element->appendChild(
			new DOMElement('ShipmentServiceOptions'));
		$on_call_air = $service_options->appendChild(
			new DOMElement('OnCallAir'));
		$schedule = $on_call_air->appendChild(new DOMElement('Schedule'));
		
		// check to see if this is a satruday pickup
		if (isset($this->shipment['saturday']['pickup']) &&
			$this->shipment['saturday']['pickup'] !== false) {
			$service_options->appendChild(new DOMElement('SaturdayPickup'));
		} // end if this is a saturday pickup
		
		// check to see if this is a saturday delivery
		if (isset($this->shipment['saturday']['delivery']) &&
			$this->shipment['saturday']['delivery'] !== false) {
			$service_options->appendChild(new DOMElement('SaturdayDelivery'));
		} // end if this is a saturday delivery
		
		// check to see if we have a pickup day
		if (isset($this->shipment['pickup_day'])) {
			$schedule->appendChild(new DOMElement('PickupDay',
				$this->shipment['pickup_day']));
		} // end if we have a pickup day
		
		// check to see if we have a scheduling method
		if (isset($this->shipment['scheduling_method'])) {
			$schedule->appendChild(new DOMElement('Method',
				$this->shipment['scheduling_method']));
		} // end if we have a scheduling method
		
		// check to see if we have on call air options
		if (!$schedule->hasChildNodes()) {
			$service_options->removeChild($on_call_air);
		} // end if we have on call air options
		
		// check to see if we have service options
		if (!$service_options->hasChildNodes()) {
			$dom_element->removeChild($service_options);
			return false;
		} // end if we do not have service options
		
		return $service_options;
	} // end function buildRequest_ServiceOptions()
	
	/**
	 * Builds the ship from elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_ShipFrom(&$dom_element) {
		/** build the destination element and its children **/
		$ship_from = $dom_element->appendChild(new DOMElement('ShipFrom'));
		$ship_from->appendChild(new DOMElement('CompanyName',
			$this->ship_from['name']));
		$ship_from->appendChild(new DOMElement('PhoneNumber',
			$this->ship_from['phone']));
		$address = $ship_from->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->ship_from['street']));
		
		// check to see if there is a second steet line
		if (isset($this->ship_from['street2']) &&
			!empty($this->ship_from['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->ship_from['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->ship_from['street3']) &&
			!empty($this->ship_from['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->ship_from['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->ship_from['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->ship_from['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->ship_from['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->ship_from['country']));
		
		return $ship_from;
	} // end function buildRequest_ShipFrom()
	
	/**
	 * Builds the shipment elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Shipment(&$shipment) {
		
		/** build the shipment node **/
		$service = $shipment->appendChild(new DOMElement('Service'));
		$service->appendChild(new DOMElement('Code',
			$this->shipment['service']));
		
		// iterate over the pacakges to create the package element
		foreach ($this->shipment['packages'] as $package) {
			$this->buildRequest_Package($shipment, $package);
		} // end for each package
		
		$this->buildRequest_ServiceOptions($shipment);
		
		return $shipment;
	} // end function buildRequest_Shipment()
	
	/**
	 * Builds the shipper elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Shipper(&$dom_element) {
		/** build the destination element and its children **/
		$shipper = $dom_element->appendChild(new DOMElement('Shipper'));
		$shipper->appendChild(new DOMElement('Name',
			$this->shipper['name']));
		$shipper->appendChild(new DOMElement('PhoneNumber',
			$this->shipper['phone']));
		
		// check to see if we have a shipper number
		if (isset($this->shipper['number']) &&
			!empty($this->shipper['number'])) {
			$shipper->appendChild(new DOMElement('ShipperNumber',
				$this->shipper['number']));
		} // end if we have a shipper number
		
		$address = $shipper->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->shipper['street']));
		
		// check to see if there is a second steet line
		if (isset($this->shipper['street2']) &&
			!empty($this->shipper['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->shipper['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->shipper['street3']) &&
			!empty($this->shipper['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->shipper['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->shipper['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->shipper['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->shipper['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->shipper['country']));
		
		return $shipper;
	} // end function buildRequest_Shipper()
	
	/**
	 * Returns the name of the servies response root node
	 * 
	 * @access protected
	 * @return string
	 * 
	 * @todo remove after phps self scope has been fixed
	 */
	protected function getRootNodeName() {
		return self::NODE_NAME_ROOT_NODE;
	} // end function getRootNodeName()
	
	/**
	 * Returns the description for the specified service code
	 * 
	 * @param string $code
	 * @return string
	 */
	protected function getServiceDescription($code) {
		$services = array();
		
		return (isset($services[$code]) ? $services[$code]
			: 'No Description Found');
	} // end function getServiceDescription()
} // end class UpsAPI_RatesAndService

?>
