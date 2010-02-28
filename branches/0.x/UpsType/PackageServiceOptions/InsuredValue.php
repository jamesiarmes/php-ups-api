<?php
/**
 * UPS Address Type.  Makes handling of the insured package service option
 * easier.
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
 * UPS Address Type.  Makes handling of the insured package service option
 * easier.
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsType_PackageServiceOptions_InsuredValue
	extends UpsType_PackageServiceOptions {
	/**
	 * IATA currency code as defined by ISO 4217
	 * 
	 * @var string
	 * @link http://en.wikipedia.org/wiki/ISO_4217#Active_codes
	 */
	protected $currency;
	
	/**
	 * Insured value
	 * 
	 * @var double
	 */
	protected $value;
	
	/**
	 * Constructor. Values may be passed here or by calling the individual set
	 * functions.
	 * 
	 * @param string $currency
	 * @param double $value
	 */
	public function __construct($currency = null, $value = null) {
		// set any of the values that were passed in
		$this->currency = $currency;
		$this->value = $value;
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
	 * Sets the currency code
	 * 
	 * @param string $value
	 */
	public function setCurrencyCode($value) {
		$this->currency = $value;
		
		return true;
	} // end function setCurrencyCode()
	
	/**
	 * Sets the monetary value
	 * 
	 * @param double $value
	 * @link http://en.wikipedia.org/wiki/ISO_4217#Active_codes
	 */
	public function setMonetaryValue($value) {
		$this->value = $value;
		
		return true;
	} // end function setMonetaryValue()
	
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
		$xml = new SimpleXMLElement('<InsuredValue />');
		
		// check to see if we have a value for the currency
		if (!empty($this->currency)) {
			$xml->CurrencyCode = $this->currency;
		} // end if we have a value for the currency
		
		// check to see if we have a value for the value
		if (!empty($this->value)) {
			$xml->MonetaryValue = $this->value;
		} // end if we have a value for the value
		
		return $xml;
	} // end function toElement()
} // end class UpsType_PackageServiceOptions_InsuredValue
