<?php
/*
 * This is for test.
 */

include_once 'dict.php';
include_once 'parser.php';
include_once 'property.php';

$search_input = array (
		"5 bed rent",
		"Castleknock 3 bedroom for sale",
		"2 bed apartment to let Dublin",
		"3 or 4 bed house to rent in Dundrum for 1000 per month",
		
		"Castleknock 3 bedroom for sale from 1000 to 10000",
		"3 bed to sale in Dublin",
);

/* initial the dict */
$dict = new Dict ();

foreach($search_input as $input){
	
	/* paring the input */
	$parser = new Parser ( $input, $dict );
	$key_variable = $parser->parse_string ();
	
	/* get the property from Daft */
	$property = new Property ( $key_variable );
	$results = $property->get_property ();
	
	/* show the result from Daft*/
	if($results != null){
		print "The search sentence is : " . $results->search_sentence . "\n";
		
		if($results->ads != null ){
			foreach ( $results->ads as $ad ) {
				print "Daft url : " . $ad->daft_url . " Daft full address : " . $ad->full_address . "\n";
			}
		}else{
			print "There is no ads. Try another search, please.\n";
		}
		
		print "\n";
	}
}