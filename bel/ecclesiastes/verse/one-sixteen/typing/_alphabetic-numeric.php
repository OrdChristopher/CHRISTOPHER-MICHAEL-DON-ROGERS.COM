<?php
	class typeArray {
		
		private static $boolean_functions = [ 'ctype_alpha', 'ctype_digit' ];
		private static $type_functions = [ 'strval', 'intval' ];
		
		public static function typed_array ( $value ) {
			
			$return = [ ];
			
			$alphabetic_numeric = ( self::boolean_position_array ( $value ) );
			
			foreach ( $alphabetic_numeric as $index => $pair )
			{
				
				$type = gettype ( $pair );
				
				if ( isset ( $return [ $type ] ) === false )
				{
					
					$return [ $type ] = [ ];
					
				}
				
				foreach ( $return as $seperate_value )
				{
					
					$return [ $type ] [ $index ] = $pair;
					
				}
				
			}
			
			return $return;
			
		}
		
		private static function boolean_position_array ( $values, $type = false ) {
			
			$return = [ ];
			
			//$values = strval ( $values );
			
			for ( $index = 0, $length = strlen ( $values ); $index < $length; $index++ ) {
				
				//TODO : Turn first to BIBLE ( data ), then TO ELDERS ( function ), and the TO CHILDREN ( method )
				/*
				if ( function_exists ( $boolean_function ) === true && function_exists ( $type_function ) === true ) {
					
					if ( $boolean_function ( $value [ $index ] ) === true ) {
						
						$return [ $index ] = $type_function ( $value [ $index ] );
					
					}
					
				}
				*/
				//if type or boolean is int, pop 0 index or compare string to list.
				
				$value = ( $values [ $index ] );
				
				for ( $position = 0, $count = count ( self::$boolean_functions ); $position < $count; $position++ ) {
					
					$boolean_function = self::$boolean_functions [ $position ];
					
					if ( function_exists ( $boolean_function ) && $boolean_function ( $value ) === true ) {
						
						if ( count ( self::$type_functions ) >= $position ) {
							
							$type_function = self::$type_functions [ $position ];
							
							$return [ $index ] = $type_function ( $value );
							
						}
						
					}
					
				}
				
			}
			
			return $return;
			
		}
		
	}
?>