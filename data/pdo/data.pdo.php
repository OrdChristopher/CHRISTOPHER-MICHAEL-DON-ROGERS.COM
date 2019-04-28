<?php
  class data extends \PDO {
    private $primary_key = null;
    private $primary_value = null;
    private $database = null;
    private $handle = null;
    private $table = null;
    private $host = null;
    private $key = null;
    
    public static function exceptions ( $exception ) {
      die ( 'Uncaught exception: ' . var_dump ( $exception ) );
    }
    
    public function __construct ( $host, $handle, $key, $table, $database = 'foo' ) {
      ini_set ( 'max_execution_time', 0 );
      ini_set ( 'memory_limit', '512m' );
      set_exception_handler ( [ __CLASS__, 'exceptions' ] );
      $this->database = $database;
      $this->handle = $handle;
      $this->table = $table;
      $this->host = $host;
      $this->key = $key;
	  //"mysql:host={$this->host}"
	  //sqlite:/tmp/foo.db
      parent::__construct ( "sqlite:./data/db/{$database}.db", $this->handle, $this->key, [ \PDO::MYSQL_ATTR_FOUND_ROWS => true ] );
      restore_exception_handler ( );
      //$this->primary_key = $this->getPrimaryKey ( ); // find the primary key of table, and set it within.
      return $this;
    }
    
    public function query ( $chunks ) {
      $query = false;
      if ( is_array ( $chunks ) ) {
        $query = implode ( ' ', $chunks ) . ";";
      }
      return $query;
    }
	
	/*
	CREATE TABLE MyGuests (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP
)
	*/
	
	
	
	public function create_database ( $what ) {
		// WHAT: table or database
		// WHY: name
		return $this->query ( array_merge ( [ 'CREATE', 'DATABASE', $what ] ) );
	}
	
	public function create_table ( $what ) {
		// WHAT: table or database
		// WHY: name
		return $this->query ( array_merge ( [ 'CREATE', 'TABLE', $what ] ) );
	}
	
	public function show ( $what ) {
		return $this->query ( array_merge ( [ 'SHOW', $what ] ) );
	}
    
    public function select ( $what, ...$chunks ) {
      return $this->query ( array_merge ( [ 'SELECT', $this->what ( $what ) ], $chunks ) );
    }
    
    public function insert ( $what, $why, ...$chunks ) {
      return $this->query ( array_merge ( [ 'INSERT INTO', '( ' . $this->quoted_list ( '`', $what ) . ' )', ' VALUES ( ' . $this->quoted_list ( '\'', $why ) . ' )' ], $chunks ) );
    }
    
    public function update ( $what, $why, ...$chunks ) {
      return $this->query ( array_merge ( [ 'UPDATE', '`' . $what . '`', ' SET ( ' . $this->implode_comma_seperated_lists ( $this->quoted_list ( '`', array_keys ( $why ) ), $this->quoted_list ( '\'', array_values ( $why ) ) ) . ' )' ], $chunks ) );
    }
    
    public function what ( $column ) {
      $what = '( ';
      if ( is_array ( $column ) ) {
        foreach ( $column as $key => $name ) {
          $what .= "`{$name}`, ";
        }
        $what = rtrim ( $what, ',' );
      } elseif ( is_string ( $column ) ) {
        $what .= "`{$column}` ";
      }
      return "{$what})";
    }
    
    public function where ( $column ) {
      $where = 'WHERE ';
      if ( is_array ( $column ) ) {
        if ( count ( $column ) == count ( $column, COUNT_RECURSIVE ) ) {
          $column = [ $column ];
        }
        foreach ( $column as $index => $column_set ) { 
          foreach ( $column_set as $key => $name ) {
            if ( is_array ( $name ) ) {
              $where .= ' (';
              $type = is_string ( $key ) ? strtoupper ( $key ) : 'AND';
              foreach ( $name as $column_name => $column_value ) {
                $where .= " `{$column_name}` = '{$column_value}' {$type} ";
              }
              $where = rtrim ( $where, "{$type} " ) . ' ) AND ';
            }
          }
        }
        $where = rtrim ( $where, ' AND ' );
      }
      return "{$where}";
    }
    
    public function string_list ( $prefix, $suffix, $delimiter, $list ) {
      return $prefix . implode ( $delimiter, $list ) . $suffix;
    }
    
    public function quoted_list ( $quote, $list ) {
      return is_array ( $list ) ? rtrim ( $quote . implode ( "{$quote},{$quote}", $list ), ",{$quote}" ) . $quote : "{$quote}{$list}{$quote}";
    }
    
    public function implode_comma_seperated_lists ( $list_key, $list_value, $delimiter = '=' ) {
      if ( !is_array ( $list_key ) ) {
        $list_key = explode ( ',', $list_key );
      }
      if ( !is_array ( $list_value ) ) {
        $list_value = explode ( ',', $list_value );
      }
      $result = '';
      if ( ( $count_key = count ( $list_key ) ) == count ( $list_value ) ) {
        for ( $index = 0, $size = $count_key; $index < $size; $index++ ) {
          $result .= "{$list_key [ $index ]} {$delimiter} {$list_value [ $index ]}, ";
        }
        $result = rtrim ( $result, ', ' );
      }
      return empty ( $result ) ? false : $result;
    }
    
    public function primary_key ( ) {
      //this function should recurse this object
      if ( is_null ( $this->primary_key ) ) {
        $statement = $this->prepare ( "DESCRIBE {$this->table};" );
        $statement->execute ( );
        $rows = $statement->fetchAll ( \PDO::FETCH_ASSOC );
        if ( count ( $rows ) > 0 ) {
          foreach ( $rows as $key => $value ) {
            foreach ( $value as $column_key => $column_value ) {
              if ( strtoupper ( $column_key ) == 'KEY' ) {
                return $rows [ $key ] [ 'Field' ];
              }
            }
          }
        }
      }
      return $this->primary_key;
    }
    
    public function primary_value ( ) {
      //if null, locate
      if ( is_null ( $this->primary_value ) ) {
        
      }
      return $this->primary_value;
    }
	
	public function prepare_execute_object ( $query ) {
		$statement = $this->prepare ( $query );
		$result = $statement->execute ( );
		if ( is_bool ( $result ) ) {
			if ( $result === true ) {
				return $statement;
			}
			return null;
		}
		return $statement;
	}
  }