<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class MY_Model extends CI_Model 
{
  protected $table = false;

  protected $primary = null;

  protected $_public = [];

  protected $_public_select = '';

  protected $_default_model = [];

  protected $protected = [];

  public function __construct() 
  {
    parent::__construct();

    if ( ! $this->table ) 
    {
      $this->table = str_replace( '_model', '', strtolower( get_called_class() ) );
    }

    $this->setPublic();
  }

  public function findOneBy( $params = null ) 
  {
    $query = $this->db->getOne( $this->table, $params );
    return $query->num_rows() > 0 ? $query->row() : false;
  }

  public function get_for_user() 
  {
    $reflection = new ReflectionClass( get_called_class() );
    $public = $reflection->getProperties( ReflectionProperty::IS_PUBLIC );
    $select = [];

    foreach($public as $prop) 
    {
      $select[] = $prop->name;
    }

    $this->db->select( implode( ',', $select) );
    $this->db->from( $this->table );
    $this->db->limit( 1 );
    $this->db->where( 'user_id', $this->user_id );

    $query = $this->db->get();

    if ( $query->num_rows() > 0 )
    {
      return $query->row(0);
    }

    return false;
  }

  public function innerJoin( $table = '', $key = false ) 
  {
    if ( ! $key ) $key = $this->primary;
    $this->db->customJoin( $table, $this->table, $key );
  }

  public function leftJoin( $table = '', $key = false )
  {
    if ( ! $key ) $key = $this->primary;
    $this->db->customJoin( $table, $this->table, $key, 'left' );
  }

  public function create($last_id = false) 
  {
    $reflection = new ReflectionClass( get_called_class() );
    $public = $reflection->getProperties( ReflectionProperty::IS_PUBLIC );
    $insert = [];

    foreach($public as $prop) 
    {
      $insert[$prop->name] = $this->{$prop->name};
    }

    if( ! $this->db->insert( $this->table, $insert ) ) 
    {
      return false;
    }

    if ($last_id)
    {
      return $this->db->insert_id();
    }

    return true;
  }

  public function get() 
  {
    $query = $this->db->get( $this->table );
    return $query->result();
  }

  public function modify( $data = [], $by_primary = false )
  {
    if ( $by_primary )
    {
      $this->db->where($this->primary, $this->{$this->primary});
    }
    else
    {
      $this->db->where( $this->table . '.user_id', $this->user_id );
    }

    $this->db->update( $this->table, $data );

    if ( $this->db->affected_rows() > 0 )
    {
      $this->merge( $data );
      return true;
    }
    
    return false;
  }

  public function extend( $filter = false )
  {
    if ( $filter ) {
      $public = array_filter( $this->_public, function( $key ){
        return !in_array( $key, ['user_id', 'password', 'email'] ) && strpos( $key, '_id' ) === FALSE;
      } );
  
      $this->db->select( implode( ',', $public ) );
    }
    else 
    {
      $this->db->select( $this->_public_select );
    }
    $this->db->from( $this->table );
    $this->db->where( $this->table . '.user_id', $this->user_id );
    $query = $this->db->get();

    if ( $query->num_rows() > 0 ) {
      $row = $query->row();
      $this->merge( $row );
      return $row;
    }
    return false;
  }

  public function upsert( $data = [] )
  {
    $values = [];
    foreach( $this->_public as $public )
    {
      $values[] = $this->db->escape( $this->{$public} );
    }

    $filtered_public_keys = array_filter( $this->_public, function( $key ){
      return !in_array( $key, ['user_id', 'password', 'email'] ) && strpos( $key, '_id' ) === FALSE;
    } );

    $on_update = [];
    foreach( $filtered_public_keys as $filtered_public_key )
    {
      $on_update[] = $filtered_public_key . '=VALUES('. $filtered_public_key .')';
    }

    $sql = 'INSERT INTO ' . $this->table . ' ('. $this->_public_select .') VALUES ('. implode( ",", $values ) .') ';
    $sql .= ' ON DUPLICATE KEY UPDATE ' . implode( ',', $on_update );

    $query = $this->db->query( $sql, [$this->_public_select, implode( ',', $on_update )] );

    return $query;
  }

  private function setPublic()
  {
    $reflection = new ReflectionClass( get_called_class() );
    $public = $reflection->getProperties( ReflectionProperty::IS_PUBLIC );

    foreach( $public as $prop ) 
    {
      $this->_public[] = $prop->name;
      $this->_default_model[$prop->name] = '';
    }

    $this->_public_select = implode( ',', $this->_public );
  }

  /**
	 * Merge the data record into the current model
	 *
	 * @params   mixed $merge
	 *
	 * @return  void
	 */
  public function merge( $merge ) 
  {
    foreach ( $merge as $key => $value ) 
    {
			$this->$key = $value;
		}
  }
  
  /**
	 * merge_fields
	 *
	 * @params   n -length arrays
	 *
	 * @return  array
	 */
	protected function merge_fields() {
		$args = func_get_args();
		$num  = func_num_args();
		for ( $i = 1; $i < $num; $i ++ ) {
			$args[0] = array_merge( (array) $args[0], (array) $args[ $i ] );
		}
		return $args[0];
	}

}