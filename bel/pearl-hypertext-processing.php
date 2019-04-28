<?php
	
	class pearlHyptertextProcessing {
		
		public function compare_call_name ( $call_name )
		{
			
			$return = [ ];
		
			$defined_functions = call_user_func_array ( 'array_merge', get_defined_functions ( ) );
			
			foreach ( $defined_functions as $define_function )
			{
				
				if ( strncmp ( $define_function, $call_name, strlen ( $call_name ) ) === 0 )
				{
					
					$return [ ] = $define_function;
					
				}
				
			}
			
			return $return;
			
		}
		
	}
	
?>