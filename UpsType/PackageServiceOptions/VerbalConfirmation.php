<?php
/**
 * UPS Address Type.  Makes handling of the verbal confirmation package service
 * option easier.
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
 * UPS Address Type.  Makes handling of the verbal confirmation package service
 * option easier.
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsType_PackageServiceOptions_VerbalConfirmation
	extends UpsType_PackageServiceOptions {
	/**
	 * Shipper's phone number country code
	 * 
	 * @var integer
	 */
	protected $coutry_code;
	
	/**
	 * Shipper's phone number dial plan
	 * 
	 * @var integer
	 */
	protected $dial_plan;
	
	/**
	 * Shipper's phone number extension
	 * 
	 * @var integer
	 */
	protected $extension;
	
	/**
	 * Shipper's phone number line number
	 * 
	 * @var integer
	 */
	protected $line_number;
	
	/**
	 * Name of the person to provide confirmation
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * Constructor. Values may be passed here or by calling the individual set
	 * functions.
	 * 
	 * @param string $name
	 * @param integer $country_code
	 * @param integer $dial_plan
	 * @param integer $line_number
	 * @param integer $extension
	 */
	public function __construct($name = null, $country_code = null,
		$dial_plan = null, $line_number = null, $extension = null) {
		// set any of the values that were passed in
		$this->name = $name;
		$this->coutry_code = $country_code;
		$this->dial_plan = $dial_plan;
		$this->line_number = $line_number;
		$this->extension = $extension;
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
	 * Sets the shipper's phone number country code
	 * 
	 * @param integer $value
	 */
	public function setCountryCode($value) {
		$this->coutry_code = $value;
		
		return true;
	} // end function setCountryCode()
	
	/**
	 * Sets the shipper's phone number dial plan
	 * 
	 * @param integer $value
	 */
	public function setDialPlan($value) {
		$this->dial_plan = $value;
		
		return true;
	} // end function setDialPlan()
	
	/**
	 * Sets the shipper's phone number extension
	 * 
	 * @param integer $value
	 */
	public function setExtension($value) {
		$this->extension = $value;
		
		return true;
	} // end function setExtension()
	
	/**
	 * Sets the shipper's phone number line number
	 * 
	 * @param integer $value
	 */
	public function setLineNumber($value) {
		$this->line_number = $value;
		
		return true;
	} // end function setLineNumber()
	
	/**
	 * Sets the name of the person to provide the confirmation
	 * 
	 * @param string $value
	 */
	public function setName($value) {
		$this->name = $value;
		
		return true;
	} // end function setCountryCode()
	
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
		$xml = new SimpleXMLElement('<VerbalConfirmation />');
		
		// check to see if we have a value for the name
		if (isset($this->name)) {
			$xml->Name = $this->name;
		} // end if we have a value for the name
		
		// check to see if we have a value for the country code
		if (isset($this->coutry_code)) {
			$xml->StructuredPhoneNumber->PhoneCountryCode = $this->coutry_code;
		} // end if we have a value for the country code
		
		// check to see if we have a value for the dial plan number
		if (isset($this->dial_plan)) {
			$xml->StructuredPhoneNumber->PhoneDialPlanNumber = $this->dial_plan;
		} // end if we have a value for the dial plan number
		
		// check to see if we have a value for the line number
		if (isset($this->line_number)) {
			$xml->StructuredPhoneNumber->PhoneLineNumber = $this->line_number;
		} // end if we have a value for the line number
		
		// check to see if we have a value for the extension
		if (isset($this->extension)) {
			$xml->StructuredPhoneNumber->PhoneExtension = $this->extension;
		} // end if we have a value for the extension
		
		return $xml;
	} // end function toElement()
} // end class UpsType_PackageServiceOptions_VerbalConfirmation
