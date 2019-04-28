<?php
	
	require_once ( "alphabetic-numeric.php" );
	
	$alphabetic_numeric_seperated_values = 'c1v1c2v2c3bb';
	
	$seperate_values = alphabeticNumeric::typed_array ( $alphabetic_numeric_seperated_values );
	
	
	print_r ( $seperate_values );
	
	print_r ( get_defined_functions ( ) );
	
?>