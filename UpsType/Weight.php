<?php
/**
 * UPS Address Type.  Makes handling of package weight easier.
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
 * UPS Address Type.  Makes handling of package weight eaiser.
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
class UpsType_Weight extends UpsType {
	/**
	 * Unit of measure for the weight
	 * 
	 * @var string
	 */
	protected $units;
	
	/**
	 * Package weight
	 * 
	 * @var string
	 */
	protected $weight;
	
	/**
	 * Kilogram unit of measurement
	 * 
	 * @var string
	 */
	const UNITS_KILOGRAMS = 'KGS';
	
	/**
	 * Pound unit of measurement
	 * 
	 * @var string
	 */
	const UNITS_POUNDS = 'LBS';
	
	/**
	 * Constructor. Values may be passed here or by calling the individual set
	 * functions.
	 * 
	 * @param string $units
	 * @param double $weight
	 */
	public function __construct($units = null, $weight = null) {
		// set any of the values that were passed in
		$this->units = $units;
		$this->weight = $weight;
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
	public function setUnitOfMeasurement($value) {
		$this->units = $value;
		
		return true;
	} // end function setUnitOfMeasurement()
	
	/**
	 * Sets the second line of the address
	 * 
	 * @param double $value
	 */
	public function setWeight($value) {
		$this->weight = $value;
		
		return true;
	} // end function setWeight()
	
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
		$xml = new SimpleXMLElement('<PackageWeight />');
		
		// check to see if we have a value for the unit of measurement
		if (!empty($this->units)) {
			$xml->UnitOfMeasurement->Code = $this->units;
			
			// determine the description
			$description = 'Pounds';
			if ($this->units == self::UNITS_KILOGRAMS) {
				$description = 'Kilograms';
			} // end if the units is centimeters
			
			$xml->UnitOfMeasurement->Description = $description;
		} // end if we have a value for the unit of measurement
		
		// check to see if we have a value for the weight
		if (!empty($this->weight)) {
			$xml->Weight = $this->weight;
		} // end if we have a value for the weight
		
		return $xml;
	} // end function toElement()
} // end class UpsType_Weight
