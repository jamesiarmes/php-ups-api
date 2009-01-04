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
		$rate_element = $xml->appendChild(
			new DOMElement('RatingServiceSelectionRequest'));
		$rate_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		$requst_element = $this->buildRequest_RequestElement($rate_element,
			'Rate', 'Rate', $customer_context);
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
} // end class UpsAPI_RatesAndService

?>
