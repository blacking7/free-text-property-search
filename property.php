<?php
/*
 * This class is to send request to Daft with key variable.
 *
 */

include_once 'dict.php';

class Property {
	public $key_variable;
	
	public function __construct($key_variable) {
		$this->key_variable = $key_variable;
	}
	
	/*get property from Daft API*/
	function get_property() {
		$DaftAPI = new SoapClient ( "http://api.daft.ie/v2/wsdl.xml" );
		
		$parameters = array (
				'api_key' => "67c7bc238dce710e9c3f3f4a8e301d30bc46cf30",
				'query' => $this->key_variable ["query_params"] 
		);
		
		/* sent request to Daft through different interface */
		if ($this->key_variable ["search_type"] == Dict::SALE) {
			$response = $DaftAPI->search_sale ( $parameters );
		} elseif ($this->key_variable ["search_type"] == Dict::RENT) {
			$response = $DaftAPI->search_rental ( $parameters );				
		}
				
		$results = $response->results;
		
		return $results;	
	}
}