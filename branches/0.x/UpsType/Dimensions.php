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
class UpsType_Dimensions extends UpsType {
	/**
	 * Height of the package
	 * 
	 * @var double
	 */
	protected $height;
	
	/**
	 * Length of the package
	 * 
	 * @var double
	 */
	protected $length;
	
	/**
	 * Unit of measure for the dimensions
	 * 
	 * @var string
	 */
	protected $units;
	
	/**
	 * Width of the package
	 * 
	 * @var double
	 */
	protected $width;
	
	/**
	 * Centimeter unit of measurement
	 * 
	 * @var string
	 */
	const UNITS_CENTIMETERS = 'CM';
	
	/**
	 * Centimeter unit of measurement
	 * 
	 * @var string
	 */
	const UNITS_INCHES = 'IN';
	
	/**
	 * Constructor. Values may be bassed here on by calling the individual set
	 * functions.
	 * 
	 * @param string $units
	 * @param double $length
	 * @param double $width
	 * @param double $height
	 */
	public function __construct($units = null, $length = null, $width = null,
		$height = null) {
		
		// set any of the values that were passed in
		$this->units = $units;
		$this->length = $length;
		$this->width = $width;
		$this->height = $height;
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
	 * Sets the height of the package
	 * 
	 * @param double $value
	 */
	public function setHeight($value) {
		$this->height = $value;
		
		return true;
	} // end function setHeight()
	
	/**
	 * Sets the length of the package
	 * 
	 * @param double $value
	 */
	public function setLength($value) {
		$this->length = $value;
		
		return true;
	} // end function setLength()
	
	/**
	 * Sets the unit of measurement for the dimensions
	 * 
	 * @param string $value
	 */
	public function setUnitOfMeasurement($value) {
		$this->units = $value;
		
		return true;
	} // end function setUnitOfMeasurement()
	
	/**
	 * Sets the width of the package
	 * 
	 * @param double $value
	 */
	public function setWidth($value) {
		$this->city = $value;
		
		return true;
	} // end function setWidth()
	
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
		$xml = new SimpleXMLElement('<Dimensions />');
		
		// check to see if we have a value for the unit of measurement
		if (isset($this->units)) {
			$xml->UnitOfMeasurement->Code = $this->units;
			
			// determine the description
			$description = 'Inches';
			if ($this->units == self::UNITS_CENTIMETERS) {
				$description = 'Centimeters';
			} // end if the units is centimeters
			
			$xml->UnitOfMeasurement->Description = $description;
		} // end if we have a value for the unit of measurement
		
		// check to see if we have a value for the length
		if (isset($this->length)) {
			$xml->Length = $this->length;
		} // end if we have a value for the length
		
		// check to see if we have a value for the width
		if (isset($this->width)) {
			$xml->Width = $this->width;
		} // end if we have a value for the width
		
		// check to see if we have a value for the height
		if (isset($this->height)) {
			$xml->Height = $this->height;
		} // end if we have a value for the height
		
		return $xml;
	} // end function toElement()
} // end class UpsType_Dimensions
