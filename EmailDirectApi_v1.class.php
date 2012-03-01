<?php

/**
* A simple PHP wrapper class for the EmailDirect REST API with support to access raw responses in XML or JSON for custom processing.
*
* @author Matt Swan <matt.swan@criticaltool.com>
* @copyright 2012 CriticalTool, Inc. <http://www.criticaltool.com>
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link http://github.com/CriticalTool/EmailDirect-API-PHP-Wrapper
*/

class EmailDirectApi
{
	protected $apiKey;
	protected $content;
	protected $output;
	protected $baseUrl;
	protected $response;
	protected $responseInfo;
	
	function __construct ( $apiKey = NULL, $content = 'json', $output = 'array', $baseUrl = 'https://rest.emaildirect.com/v1' )
	{
		$this->apiKey = $apiKey;
		$this->baseUrl = $baseUrl;
		if ( strtolower($output) == 'array' ) {
			$this->contentType = 'application/json'; // if ARRAY is selected, content must be JSON
			$this->outputType = 'array';
		} else {
			$this->outputType = 'raw';
		}
		if ( strtolower($content) == 'xml' ) {
			$this->contentType = 'application/xml';
			$this->outputType = 'raw'; // if XML is selected, output must be RAW
		} else {
			$this->contentType = 'application/json';
		}
	}

	/**
	 * Attach your API key.
	 *
	 * @param string $apiKey can be found after logging into your EmailDirect account
	 */
	public function setApiKey ($apiKey) { $this->apiKey = $apiKey; }

	/**
	 * Select the content type.
	 *
	 * @param string $content Can be either 'json', or 'xml'
	 */
	public function setContent ($content)
	{
		if ( strtolower($type) == 'xml' ) {
			$this->contentType = 'application/xml';
			$this->outputType = 'raw';
		} else {
			$this->contentType = 'application/json';
		}
	}

	/**
	 * Select the output type.
	 *
	 * @param string $type Can be either 'array', 'json', or 'xml'
	 */
	public function setOutput ($type)
	{
		if ( strtolower($type) == 'xml' ) {
			$this->contentType = 'application/xml';
			$this->outputType = 'raw';
		} else {
			$this->contentType = 'application/json';
		}
	}
	
	/**
	 * Fetch the currently selected content type.
	 *
	 * @return string The content type, either application/json or application/xml
	 */
	public function getContentType () { return $this->contentType; }
	
	/**
	 * Fetch the currently selected output type.
	 *
	 * @return string The output type, either array, json, or xml
	 */
	public function getOutputType () { return $this->outputType; }
	
	/**
	 * Retrieves the response from the most recent cURL call.
	 *
	 * @return xml/object Depending on output type, this could be in XML or JSON formats.
	 */
	public function getResponse () { return $this->response; }	
	
	/**
	 * Retrieves details about the most recent cURL call.
	 *
	 * @return array
	 */
	public function getResponseInfo () { return $this->responseInfo; }

	/**
	 * Returns an object reflecting the current permissions allowed for the provided API Key
	 *
	 * @return array
	 */
	public function ping ()
	{
		return $this->doCurl( 'GET', $this->baseUrl . '/Ping' );
	}
	
	/**
	 * Returns an object reflecting the current permissions allowed for the provided API Key
	 *
	 * @return array
	 */
	public function fetch ($verb = 'GET', $url = 'https://rest.emaildirect.com/v1/Ping', $request = '')
	{
		return $this->doCurl($verb, $url, $request);
	}
	
	/**
	 * Attach your API key.
	 *
	 * @param string $api_key Can be accessed on the marketplaces via My Account
	 * -> My Settings -> API Key
	 */
	protected function doCurl($verb = 'GET', $url = 'https://rest.emaildirect.com/v1/Ping', $request = '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
		curl_setopt($ch, CURLOPT_URL, $url);  
		
		switch (strtoupper($verb)) {
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				break;
			case 'PUT':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
				break;
		}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: $this->contentType", "ApiKey: $this->apiKey", "Accept: $this->contentType"));
						
		$this->response = curl_exec($ch);
		$this->responseInfo = curl_getinfo($ch);
		curl_close($ch);
		
		if ( $this->outputType == 'array' ) { $this->response = json_decode($this->response); }
		
		return $this->response;
	}
	
	protected function getXml($data,$root)
	{
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement($root);
		$this->writeXml($xml, $data);
		$xml->endElement();
		return $xml->outputMemory(true);
	}
	
	protected function writeXml(XMLWriter $xml, $data)
	{
		foreach($data as $key => $value){
			if(is_array($value)){
				$xml->startElement($key);
				$this->writeXml($xml, $value);
				$xml->endElement();
				continue;
			}
			$xml->writeElement($key, $value);
		}
	}

}
?>