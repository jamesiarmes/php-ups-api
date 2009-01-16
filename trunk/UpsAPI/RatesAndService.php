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
	 * Shipper data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $shipper = array();
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 */
	public function __construct() {
		parent::__construct();
		
		// set object properties
		$this->server = $GLOBALS['ups_api']['server'].'/ups.app/xml/Rate';
	} // end function __construct()
	
	/**
	 * Returns charges for each package
	 * 
	 * @return array
	 */
	public function getPackageCharges() {
		$return_value = array();
		
		// iterate over the packages
		$packages = $this->xpath->query(self::NODE_NAME_RATED_SHIPMENT.
			'//RatedPackage', $this->root_node);
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
	 * Returns charges for the entire shipment
	 * 
	 * @return array
	 */
	public function getShipmentCharges() {
		$rated_shipment = $this->xpath->query(
			'//'.self::NODE_NAME_RATED_SHIPMENT, $this->root_node)->item(0);
		
		$return_value[] = array(
			'currency_code' => $this->xpath->query(
				'//TotalCharges/CurrencyCode',
				$rated_shipment)->item(0)->nodeValue,
			'transportation' => $this->xpath->query(
				'//TransportationCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
			'service_options' => $this->xpath->query(
				'//ServiceOptionsCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
			'total' => $this->xpath->query(
				'//TotalCharges/'.self::NODE_NAME_MONETARY_VALUE,
				$rated_shipment)->item(0)->nodeValue,
		); // end $return_value
		
		return $return_value;
	} // end function
	
	/**
	 * Returns any warnings from the response
	 * 
	 * @return array
	 */
	public function getWarnings() {
		$warnings = $this->xpath->query('//'.self::NODE_NAME_RATED_SHIPMENT.
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
	public function buildRequest($customer_context = null)
	{
		// create the new dom document
		$xml = new DOMDocument('1.0', 'utf-8');
		/** create the AddressValidationRequest element **/
		$rate = $xml->appendChild(
			new DOMElement('RatingServiceSelectionRequest'));
		$rate->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$requst = $this->buildRequest_RequestElement($rate,
			'Rate', 'Rate', $customer_context);
		$shipment = $rate->appendChild(new DOMElement('Shipment'));
		
		$return_value =
			'<?xml version="1.0"?>
<RatingServiceSelectionRequest xml:lang="en-US">
  <Request>
    <TransactionReference>
      <CustomerContext>Rating and Service</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
	<RequestAction>Rate</RequestAction>
	<RequestOption>Rate</RequestOption>
  </Request>
    <PickupType>
  	<Code>07</Code>
  	<Description>Rate</Description>
    </PickupType>
  <Shipment>
    	<Description>Rate Description</Description>
    <Shipper>
      <Name>Name</Name>
      <PhoneNumber>1234567890</PhoneNumber>
      <ShipperNumber>Ship Number</ShipperNumber>
      <Address>
        <AddressLine1>Address Line</AddressLine1>
        <City>City</City>
        <StateProvinceCode>NJ</StateProvinceCode>
        <PostalCode>07430</PostalCode>
        <CountryCode>US</CountryCode>
      </Address>
    </Shipper>
    <ShipTo>
      <CompanyName>Company Name</CompanyName>
      <PhoneNumber>1234567890</PhoneNumber>
      <Address>
        <AddressLine1>Address Line</AddressLine1>
        <City>Corado</City>
        <PostalCode>00646</PostalCode> 
        <CountryCode>PR</CountryCode>
      </Address>
    </ShipTo>
    <ShipFrom>
      <CompanyName>Company Name</CompanyName>
      <AttentionName>Attention Name</AttentionName>
      <PhoneNumber>1234567890</PhoneNumber>
      <FaxNumber>1234567890</FaxNumber>
      <Address>
        <AddressLine1>Address Line</AddressLine1>
		<City>Boca Raton</City>
        <StateProvinceCode>FL</StateProvinceCode>
        <PostalCode>33434</PostalCode> 
        <CountryCode>US</CountryCode>
      </Address>
    </ShipFrom>
  	<Service>
    		<Code>03</Code>
  	</Service>
  	<PaymentInformation>
	      	<Prepaid>
        		<BillShipper>
          			<AccountNumber>Ship Number</AccountNumber>
        		</BillShipper>
      		</Prepaid>
  	</PaymentInformation>
  	<Package>
      		<PackagingType>
	        	<Code>02</Code>
        		<Description>Customer Supplied</Description>
      		</PackagingType>
      		<Description>Rate</Description>
      		<PackageWeight>
      			<UnitOfMeasurement>
      			  <Code>LBS</Code>
      			</UnitOfMeasurement>
	        	<Weight>10</Weight>
      		</PackageWeight>   
   	</Package>
   	<Package>
      		<PackagingType>
	        	<Code>02</Code>
        		<Description>Customer Supplied</Description>
      		</PackagingType>
      		<Description>Rate</Description>
      		<PackageWeight>
      			<UnitOfMeasurement>
      			  <Code>LBS</Code>
      			</UnitOfMeasurement>
	        	<Weight>100</Weight>
      		</PackageWeight>   
   	</Package>
    <ShipmentServiceOptions>
      <OnCallAir>
		<Schedule> 
			<PickupDay>02</PickupDay>
			<Method>02</Method>
		</Schedule>
      </OnCallAir>
    </ShipmentServiceOptions>
  </Shipment>
</RatingServiceSelectionRequest>';
		
		return parent::buildRequest().$return_value;
	} // end function buildRequest()
	
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
} // end class UpsAPI_RatesAndService

?>
