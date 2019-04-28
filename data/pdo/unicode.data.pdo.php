<?php
	require_once ( 'data.pdo.php' );
	
	class unicode extends data {
		
		private $unicode_data;
		
		private $typing;
		
		private $rows;
		
		public function __construct ( $host, $database, $handle, $key, $table )
		{
			
			$this->unicode_data = new data ( $host, $database, $handle, $key, $table );
			
			$this->typing = new typing ( );
			
		}
		
		public function type_as_data ( $name, $lines = 1 )
		{
			
			$rows = [ ];
			
			$start_of_lines = $lines;
			
			$end_of_lines = file ( $name );
			
			for ( $start_of_line = 0, $end_of_line = count ( $end_of_lines ) - 1; $start_of_line < $end_of_line; $start_of_line++ )
			{
				
				if ( $lines !== 0 && $start_of_line >= $start_of_lines )
				{
					
					break;
					
				}
				
				$line = str_replace ( PHP_EOL, '', $end_of_lines [ $start_of_line ] );
				
				$row = $this->typing->type ( $line );
				
				$row [ 'line' ] = $line;
				
				$row [ 'limit' ] = $this->limit ( $row );
				
				$row [ 'segment' ] = $this->segment ( $row );
				
				$row [ 'point' ] = $this->point ( $row );
				
				$rows [ ] = $row;
				
			}
			
			$columns = [ ];
			
			foreach ( $rows as $row )
			{
				
				$points = $row [ 'point' ];
				
				foreach ( $points as $key => $value )
				{
					
					if ( !isset ( $columns [ $key ] ) )
					{
						
						$columns [ $key ] = [ $value, 'length' => strlen ( $value ) ];
						
					}
					else
					{
						
						$columns [ $key ] [ ] = $value;
						$columns [ $key ] [ 'length' ] = max ( $columns [ $key ] [ 'length' ], strlen ( $value ) );
						
					}
					
				}
				
			}
			
			var_dump ( $columns );
			
			return ( $this->rows = $rows );
			
		}
		
		public function point ( $row )
		{
			$point = [ ];
			
			$int = [ ];
			
			$line = $row [ 'line' ];
			
			$limit = $row [ 'limit' ];
			
			$scalar = $row [ 'scalar' ];
			
			$finite = $row [ 'finite' ];
			
			$now = null;
			
			foreach ( $finite as $position => $numeric )
			{
				
				if ( is_null ( $now ) || ( $position == $now + 1 ) )
				{
					
					$int [ $position ] = ( $numeric );
					
				}
				else
				{
					
					$now += 1;
					
					while ( isset ( $scalar [ $now ] ) && $scalar [ $now ] !== $limit )
					{
						
						$int [ $now ] = $scalar [ $now ];
						
						$now++;
						
					}
					
					for ( $canonical = "", $position -= 2; $position > 0; $position-- )
					{
						
						if ( $scalar [ $position ] == $limit )
						{
							
							$point [ ] = strrev ( $canonical );
							
							$canonical = "";
							
						}
						else
						{
							
							$canonical .= $scalar [ $position ];
							
						}
						
					}
					
				}
				
				$now = $position;
				
			}
			
			if ( count ( $int ) > 0 )
			{
				
				$point [ ] = hexdec ( implode ( '', $int ) );
				
				$int = null;
				
			}
			
			return array_values ( array_unique ( $point ) );
			
		}
		
		public function segment ( $row )
		{
			
			$segments = [ ];
			
			if ( isset ( $row [ 'writable' ] ) )
			{
				
				$writable = $row [ 'writable' ];
				
				$readable = $row [ 'line' ];
				
				$points = [ ];
				
				foreach ( $writable as $position => $scalar )
				{
					
					if ( count ( $points ) != 2 )
					{
						
						$points [ ] = $position;
						
						if ( count ( $points ) == 2 )
						{
							
							$segment_length = 1 + $points [ 1 ] - $points [ 0 ];
							
							$segments [ ] = substr ( $readable, $points [ 0 ], $segment_length );
							
							$points = [ ];
							
						}
						
					}
					
				}
			
			}
			
			return $segments;
			
		}
		
		public function limit (  $row )
		{
			$limit = array_count_values ( $row [ 'scalar' ] );
			asort ( $limit, SORT_NUMERIC );
			$limit = array_keys ( array_reverse ( $limit, true ) );
			$limit = array_shift ( $limit );
			return $limit;
			
		}
	}
?>