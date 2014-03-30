<?php
/*
 * This class is a dictionary and all the keyword is in this dict.
 * 
 * 1.The dict is a 'Hash-Map' and the format is : (term_name, (trem_id, term_type)).
 * 2.The term type is : area, county, rent, sale, bed, price.
 * 3.Build dict and get term makes in this class.
 *
 */

class Dict {
	const RENT = "rent";
	const SALE = "sale";
    const AREA = "area";
    const COUNTY = "county";
    const BED = "bed";
    const PRICE = "price";
    
	const TERM_ID = "id";
	const TERM_TYPE = "keyword_type"; 
	
	public $dict = array ();
	
	public function __construct() {
		self::build_dict ();
	}
	
	/* build the dict */
	function build_dict() {
		
		/* add area list to the dict */
		$DaftAPI = new SoapClient ( "http://api.daft.ie/v2/wsdl.xml" );
		
		$parameters = array (
				'api_key' => "67c7bc238dce710e9c3f3f4a8e301d30bc46cf30",
				'area_type' => "area" 
		);
		
		$response = $DaftAPI->areas ( $parameters );
		
		foreach ( $response->areas as $area ) {
			$this->dict [$area->name] = array (
					self::TERM_ID => $area->id,
					self::TERM_TYPE => self::AREA 
			);
		}
		
		/* add the county list to the dict */		
		$parameters = array (
				'api_key' => "67c7bc238dce710e9c3f3f4a8e301d30bc46cf30",
				'area_type' => "county"
		);
		
		$response = $DaftAPI->areas ( $parameters );
		
		foreach ( $response->areas as $county ) {
			$connty_name = str_replace("Co. ", "", $county->name);
				
			$this->dict [$connty_name] = array (
					self::TERM_ID => $county->id,
					self::TERM_TYPE => self::COUNTY
			);
		}
		
		/* add keyword to list from file */
		$lines = file ( 'terms.txt' );
		
		foreach ( $lines as $line ) {
			$line = str_replace ( "\n", "", $line );
			$tokens = explode ( " ", $line );
			$this->dict [$tokens [0]] = array (
					self::TERM_TYPE => $tokens [1] 
			);
		}
	}
	
	/* get term from dict if the term is in dict */
	function get_term($term) {
		if ($this->dict [$term]) {
			return $this->dict [$term];
		} else {
			return null;
		}
	}
}