<?php
/**
 * UPS Address Type.  Makes handling of addresses easier.
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
 * UPS Address Type.  Makes handling of addresses easier.
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsType_Address extends UpsType {
	/**
	 * City for this address. Required only if postal code is empty.
	 * 
	 * @var string
	 */
	protected $city;
	
	/**
	 * Country for this address. Required but defaults to "US".
	 * 
	 * @var string
	 */
	protected $country = 'US';
	
	/**
	 * Postal code for this address. Required if the country utalizes postal
	 * codes (ie. United States and Puerto Rico)
	 * 
	 * @var string
	 */
	protected $postal;
	
	/**
	 * State or province for this address
	 * 
	 * @var string
	 */
	protected $state;
	
	/**
	 * First line of the address.
	 * 
	 * @var string
	 */
	protected $street1;
	
	/**
	 * Second line of the address.
	 * 
	 * @var string
	 */
	protected $street2;
	
	/**
	 * Third line of the address.
	 * 
	 * @var string
	 */
	protected $street3;
	
	/**
	 * Constructor. Values may be passed here or by calling the individual set
	 * functions.
	 * 
	 * @param string $street1
	 * @param string $street2
	 * @param string $street3
	 * @param string $city
	 * @param string $state
	 * @param string $postal
	 * @param string $country
	 */
	public function __construct($street1 = null, $street2 = null,
		$street3 = null, $city = null, $state = null, $postal = null,
		$country = null) {
		// set any of the values that were passed in
		$this->street1 = $street1;
		$this->street2 = $street2;
		$this->street3 = $street3;
		$this->city = $city;
		$this->state = $state;
		$this->postal = $postal;
		$this->country = (isset($country) ? $country : $this->country);
	} // end function __construct()
	
	/**
	 * Returns the XML for the type.
	 * 
	 * @return string
	 * 
	 * @todo remove preg_replace() once LIBXML_NOXMLDECL is working properly
	 * with SimpleXML Element
	 * @see UpsType_Address::toElement()
	 */
	public function __toString() {
		return preg_replace('/<\?xml.*?\?>\s*/', null, $this->toElement()->asXML());
	} // end function __toString()
	
	/**
	 * Sets the first line of the address
	 * 
	 * @param string $value
	 */
	public function setAddressLineOne($value) {
		$this->street1 = $value;
		
		return true;
	} // end function setAddressLineOne()
	
	/**
	 * Sets the second line of the address
	 * 
	 * @param string $value
	 */
	public function setAddressLineThree($value) {
		$this->street3 = $value;
		
		return true;
	} // end function setAddressLineThree()
	
	/**
	 * Sets the third line of the address
	 * 
	 * @param string $value
	 */
	public function setAddressLineTwo($value) {
		$this->street2 = $value;
		
		return true;
	} // end function setAddressLineTwo()
	
	/**
	 * Sets the city of the address
	 * 
	 * @param string $value
	 */
	public function setCity($value) {
		$this->city = $value;
		
		return true;
	} // end function setCity()
	
	/**
	 * Sets the country of the address
	 * 
	 * @param string $value
	 */
	public function setCountry($value) {
		$this->country = $value;
		
		return true;
	} // end function setCountry()
	
	/**
	 * Sets the postal code of the address
	 * 
	 * @param string $value
	 */
	public function setPostalCode($value) {
		$this->postal = $value;
		
		return true;
	} // end function setPostalCode()
	
	/**
	 * Sets the state or province of the address
	 * 
	 * @param string $value
	 */
	public function setStateProvince($value) {
		$this->state = $value;
		
		return true;
	} // end function setAddressLineOne()
	
	/**
	 * Returns a SimpleXML Element for the type
	 * 
	 * @return SimpleXMLElement
	 * 
	 * @todo look into why LIBXML_NOBLANKS is not working so we can get rid of
	 * all these conditionals
	 * @todo add LIBXML_NOXMLDECL as an option once it's supported
	 */
	public function toElement() {
		$xml = new SimpleXMLElement('<Address />');
		
		// check to see if we have a value for the first line
		if (isset($this->street1)) {
			$xml->AddressLine1 = $this->street1;
			
			// check to see if we have a value for the second line
			if (isset($this->street2)) {
				$xml->AddressLine2 = $this->street2;
				
				// check to see if we have a value for the third line
				if (isset($this->street3)) {
					$xml->AddressLine3 = null;
				} // end if we have a value for the third line
			} // end if we have a value for the second line
		} // end if we have a value for the first line
		
		// check to see if we have a value for the city
		if (isset($this->city)) {
			$xml->City = $this->city;
		} // end if we have a value for the city
		
		// check to see if we have a value for the state or province
		if (isset($this->state)) {
			$xml->StateProvinceCode = $this->state;
		} // end if we have a value for the state or province

		// check to see if we have a value for the postal code
		if (isset($this->postal)) {
			$xml->PostalCode = $this->postal;
		} // end if we have a value for the postal code

		// check to see if we have a value for the country code
		if (isset($this->country)) {
			$xml->CountryCode = $this->country;
		} // end if we have a values for the country code
		
		return $xml;
	} // end function toElement()
} // end class UpsType_Address
