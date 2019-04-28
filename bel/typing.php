<?php
	
	class typing {
		
		private $pearl_hypertext_processing;
		
		public function __construct ( )
		{
			
			$this->pearl_hypertext_processing = new pearlHyptertextProcessing ( );
			
		}
		
		public function type ( $values )
		{
			
			$return = [ ];
			
			for ( $index = 0, $length = strlen ( $values ); $index < $length; $index++ )
			{
				
				//TODO : Turn first to BIBLE ( data ), then TO ELDERS ( function ), and the TO CHILDREN ( method )
				//if type or boolean is int, pop 0 index or compare string to list.
				
				$value = $values [ $index ];
				
				$type_prefix = 'is_';
				
				$is_type = $this->pearl_hypertext_processing->compare_call_name ( $type_prefix );
				
				for ( $position = 0, $count = count ( $is_type ); $position < $count; $position++ )
				{
					
					$type = $is_type [ $position ];
					
					if ( @$type ( $value ) )
					{
					
						$type_name = substr ( $type, strlen ( $type_prefix ) );
						
						if ( isset ( $return [ $type_name ] ) === false ) {
							
							$return [ $type_name ] = [ ];
							
						}
						
						$return [ $type_name ] [ $index ] = $value;
						
					}
					
				}
				
			}
			
			return $return;
			
		}
		
	}
	
?>