<?php
/* 
 * This class is to parse the input sentence to research key variable.
 * 
 * 1.Base the terms in dict during parsing process.
 * 2.The value before and after the term may be the number of term. 
 *   The value postion is (term_postion - window, term_position + window)
 *   So it support flip sentence.
 * 3.Return the key variable
 *  
 */

include_once 'dict.php';

class Parser {
	const DELIMETER = " ";
	
	public $search_query;
	public $dict;
	
	public function __construct($search_query, $dict) {
		$this->search_query = $search_query;
		$this->dict = $dict;
	}
	
	/* parses a string into key variables */
	public function parseString() {
		$key_variable = array(); 
		$query_params = array(); 
		
		$array_index = 0;
		
		$min_bedrooms = 0;
		$max_bedrooms = 0;
		
		$min_price = 0;
		$max_price = 0;
		
		$bed_num_regex = "/\\b[0-9]{1,1}\\b/i";
		$price_regex = "/\\b[0-9]{3,}\\b/";
				
		/* split the string */
		$tokens = self::tokenizeString ( $this->search_query, self::DELIMETER );
		
		foreach ( $tokens as $token ) {
			
			/* get term from dict if it is in dict */
			$term =  $this->dict->getTerm ($token);
			
			if ($term != null) {
				/* search the rent type key word */
				if($term[Dict::TERM_TYPE] === Dict::RENT){
					$key_variable["search_type"] = Dict::RENT;
					
				/* search the sale type key word */
				}elseif ($term[Dict::TERM_TYPE] === Dict::SALE){
					$key_variable["search_type"] = Dict::SALE;
					
					/* get the sale price */
					$sale_price = self::getSalePrice($tokens);
					
					$query_params["min_price"] = $sale_price["min_price"];
					$query_params["max_price"] = $sale_price["max_price"]; 
									
				/* search the area key word */
				}elseif ($term[Dict::TERM_TYPE] === Dict::AREA){
					$query_params["areas"] = $term[Dict::TERM_ID];
					
				/* search the county key word */
				}elseif ($term[Dict::TERM_TYPE] === Dict::COUNTY){
					$query_params["counties"] = $term[Dict::TERM_ID];
					
				/* search the bedroom number */
				}elseif ($term[Dict::TERM_TYPE] === Dict::BED){
					
					/* get the bedrooms number */
					$bedroom_amount = self::getNumberVariable($array_index, $tokens, $bed_num_regex);
					
					$query_params["min_bedrooms"] = $bedroom_amount["min"];
					$query_params["max_bedrooms"] = $bedroom_amount["max"];
					 
				/* serach the rent price */	
				}elseif ($term[Dict::TERM_TYPE] === Dict::PRICE){
					$rent_price = self::getNumberVariable($array_index, $tokens, $price_regex);
					
					$query_params["min_price"] = $rent_price["min"];
					$query_params["max_price"] = $rent_price["max"];
				}
			}
			
			$array_index++;
		}
		
		$key_variable["query_params"] = $query_params;
		
		return $key_variable;
	}
	
	/* search the number before and after the keyword
	 * the position is (keyword_position - window_size, keyword_postion + window_size)
	*/
	public function getNumberVariable($array_index, $tokens, $price_regex){
		$window_size = 4;
	
		$min_num = 0;
		$max_num = 0;
	
		/* search the number before and after the keyword, the range is window_size */
		$base_index = $array_index - $window_size;
			
		for($i = 0; $i <= $window_size * 2; $i++){
			$pre_term_index = $base_index + $i;
	
			/* extract the number if the number exsits*/
			if($pre_term_index >= 0 && $pre_term_index < count($tokens) &&
			preg_match($price_regex, $tokens[$pre_term_index])){
	
				$num = intval($tokens[$pre_term_index]);
					
				if($max_num === 0){
					$max_num = $num;
				}elseif ($num > $max_num) {
					$min_num = $max_num;
					$max_num = $num;
				}else{
					$min_num = $num;
				}
			}
		}
			
		$number_variable["min"] = $min_num;
		$number_variable["max"] = $max_num;
	
		return $number_variable;
	}
	
    /* get the sale price :
     * 1.there is no price keyword when the search type is sale.
	 * 2.the sale price is very large
	 * 3.so we search the large number in the sentence and believe it is the price
	 */
	public function getSalePrice($tokens){
			
		$min_price = 0;
		$max_price = 0;
			
		$price_regex = "/\\b[0-9]{3,}\\b/";
				
		foreach ( $tokens as $token ) {
			if(preg_match($price_regex, $token)){
				$num = intval($token);
				if($max_price === 0){
					$max_price = $num;
				}elseif ($num > $max_price) {
					$min_price = $max_price;
					$max_price = $num;
				}else{
					$min_price = $num;
				}
			}
		}
			
		$sale_price["min_price"] = $min_price;
		$sale_price["max_price"] = $max_price;
			
		return $sale_price;
	}
	
	/* tokenize and nominalize string */
	public function tokenizeString($search_query, $delimeter) {
		$strs = explode ( $delimeter, $search_query );
		$tokens = array();
		
		/*remove the empty element*/
		foreach ($strs as $str){
			if(trim($str) !=""){
				array_push($tokens, $str);
			}
		}
		
		return $tokens;
	}
}