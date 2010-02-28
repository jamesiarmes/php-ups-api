<?php
/**
 * UPS Address Type.  Makes handling of the delivery confirmation package
 * service option easier.
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
 * UPS Address Type.  Makes handling of the delivery confirmation package
 * service option easier.
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsType_PackageServiceOptions_DeliveryConfirmation
	extends UpsType_PackageServiceOptions {
	/**
	 * Delivery confirmation type
	 * 
	 * @var integer
	 */
	protected $type;
	
	/**
	 * Confirmation type for adult signature required
	 * 
	 * @var integer
	 */
	const CONFIRMATION_TYPE_ADULT_SIGNATURE_REQUIRED = 3;
	
	/**
	 * Confirmation type for standard delivery confirmation
	 * 
	 * @var integer
	 */
	const CONFIRMATION_TYPE_DELIVERY_CONFIRMATION = 1;
	
	/**
	 * Confirmation type for signature required
	 * 
	 * @var integer
	 */
	const CONFIRMATION_TYPE_SIGNATURE_REQUIRED = 2;
	
	/**
	 * Constructor. Values may be passed here or by calling the individual set
	 * functions.
	 * 
	 * @param string $type
	 */
	public function __construct($type = null) {
		// set any of the values that were passed in
		$this->type = $type;
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
	 * Sets the delivery confirmation type
	 * 
	 * @param integer $value
	 */
	public function setDCISType($value) {
		$this->type = $value;
		
		return true;
	} // end function setDCISType()
	
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
		$xml = new SimpleXMLElement('<DeliveryConfirmation />');
		
		// check to see if we have a value for the confirmation type
		if (isset($this->type)) {
			$xml->DCISType = $this->type;
		} // end if we have a value for the confirmation type
		
		return $xml;
	} // end function toElement()
} // end class UpsType_PackageServiceOptions_DeliveryConfirmation
