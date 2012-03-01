<?php

/**
* A simple PHP wrapper class for the EmailDirect REST API with support to access raw responses in XML or JSON for custom processing.
*
* @author Matt Swan <matt.swan@criticaltool.com>
* @copyright 2012 CriticalTool, Inc. <http://www.criticaltool.com>
* @license http://www.opensource.org/licenses/mit-license.php MIT
* @link http://github.com/CriticalTool/EmailDirect-API-PHP-Wrapper
*/

class Database extends EmailDirectApi
{
	/**
	 * Attach your API key.
	 *
	 * @param string $api_key Can be accessed on the marketplaces via My Account
	 * -> My Settings -> API Key
	 */
	public function getAllColumns ()
	{
		return $this->doCurl('GET', $this->baseUrl . '/Database');
	}
	
	/**
	 * Attach your API key.
	 *
	 * @param string $api_key Can be accessed on the marketplaces via My Account
	 * -> My Settings -> API Key
	 */
	public function getColumnDetails ($columnName)
	{
		return $this->doCurl('GET', $this->baseUrl . '/Database/' . $columnName);
	}
	
	/**
	 * Attach your API key.
	 *
	 * @param string $api_key Can be accessed on the marketplaces via My Account
	 * -> My Settings -> API Key
	 */
	public function addCustomColumn ($name = '', $type = '', $size = 0)
	{
		$addColumn = array('ColumnName' => $name, 'ColumnType' => $type, 'ColumnSize' => $size);
		if ($this->contentType == 'application/xml') {
			$request = $this->getXml($addColumn,'DatabaseColumnAdd');	
		} elseif ($this->contentType == 'application/json') {
			$request = json_encode($addColumn);
		}
		return $this->doCurl('POST', $this->baseUrl . '/Database', $request);
	}
}

?>