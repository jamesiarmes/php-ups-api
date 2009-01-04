<?php
/**
 * PHP API for use with UPS OnLine Tools.  This is the main class that
 * all other classes will extend.
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
 * Include the configuration file
 */
require_once 'inc/config.php';

/**
 * Parent class for the UpsAPI
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
abstract class UpsAPI {
	/**
	 * Access key provided by UPS
	 * 
	 * @param string
	 * @access protected
	 */
	protected $access_key;
	
	/**
	 * Developer key provided by UPS
	 * 
	 * @param string
	 * @access protected
	 */
	protected $developer_key;
	
	/**
	 * Password used to access UPS Systems
	 * 
	 * @param string
	 * @access protected
	 */
	protected $password;
	
	/**
	 * Response from the server as an array
	 * 
	 * @var array
	 * @access protected
	 */
	protected $response_array;
	
	/**
	 * UPS Server to send Request to
	 * 
	 * @param string
	 * @access protected
	 */
	protected $server;
	
	/**
	 * Username used to access UPS Systems
	 * 
	 * @param string
	 * @access protected
	 */
	protected $username;
	
	/**
	 * Sets up the API Object
	 * 
	 * @access public
	 */
	public function __construct() {
		/** Set the Keys on the Object **/
		$this->access_key = $GLOBALS['ups_api']['access_key'];
		$this->developer_key = $GLOBALS['ups_api']['developer_key'];
		
		
		/** Set the username and password on the Object **/
		$this->password = $GLOBALS['ups_api']['password'];
		$this->username = $GLOBALS['ups_api']['username'];
	} // end funciton __construct()
	
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
		// create the access request element
		$access_dom = new DOMDocument('1.0');
		$access_element = $access_dom->appendChild(
			new DOMElement('AccessRequest'));
		$access_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$access_element->appendChild(
			new DOMElement('AccessLicenseNumber', $this->access_key));
		$access_element->appendChild(
			new DOMElement('UserId', $this->username));
		$access_element->appendChild(
			new DOMElement('Password', $this->password));
		
		return $access_dom->saveXML();
	}
	
	/**
	 * Send a request to the UPS Server using xmlrpc
	 * 
	 * @params string $request_xml XML request from the child objects
	 * buildRequest() method
	 * @params boool $return_raw_xml whether or not to return the raw XML from
	 * the request
	 */
	public function sendRequest($request_xml, $return_raw_xml = false) {
		require_once 'XML/Unserializer.php';
		
		// create the context stream and make the request
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: text/xml',
				'content' => $request_xml,
			),
		));
		$response = file_get_contents($this->server, false, $context);
		
		// create an array from the raw XML data
		$unserializer = new XML_Unserializer(array('returnResult' => true));
		$this->response_array = $unserializer->unserialize($response);
		
		// check if we should return the raw XML data
		if ($return_raw_xml) {
			return $response;
		} // end if we should return the raw XML
		
		// return the response as an array
		return $this->response_array;
	} // end function sendRequest()
	
	/**
	 * Builds the Request element
	 * 
	 * @param DOMElement $dom_element
	 * @param string $action
	 * @param string $option
	 * @param string|array $customer_context
	 * @return DOMElement
	 */
	protected function buildRequest_RequestElement(&$dom_element, $action,
		$option = null, $customer_context = null) {
		// create the child element
		$request = $dom_element->appendChild(
			new DOMElement('Request'));
		
		// create the children of the Request element
		$transaction_element = $request->appendChild(
			new DOMElement('TransactionReference'));
		$request->appendChild(
			new DOMElement('RequestAction', $action));
		
		// check to see if an option was passed in
		if (!empty($option)) {
			$request->appendChild(
				new DOMElement('RequestOption', $option));
		} // end if an option was passed in
		
		// create the children of the TransactionReference element
		$transaction_element->appendChild(
			new DOMElement('XpciVersion', '1.0001'));
		
		// check if we have customer data to include
		if (!empty($customer_context)) {
			// check to see if the customer context is an array
			if (is_array($customer_context)) {
				$customer_element = $transaction_element->appendChild(
					new DOMElement('CustomerContext'));

				// iterate over the array of customer data
				foreach ($customer_context as $element => $value)
				{
					$customer_element->appendChild(
						new DOMElement($element, $value));
				} // end for each customer data
			} // end if the customer data is an array
			else {
				$transaction_element->appendChild(
					new DOMElement('CustomerContext', $customer_context));
			} // end if the customer data is a string
		} // end if we have customer data to include
		
		return $request;
	} // end function buildRequest_RequestElement()
} // end class UpsAPI

?>
