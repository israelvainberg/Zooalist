<?php

class User
{
	public $user_id = null;
	public $firstname = null;
	public $lastname = null;
	public $sent = null;
	public $received = null;
	public $is_friend = false;
	public $is_pending = false;
	
	public function __get( $name ) 
	{
		if ( isset( $this->$name ) ) 
		{
			return $this->$name;
		} 
		else 
		{
			return get_instance()->$name;
		}
	}

	public function __set( $name, $value )
	{
		if ( $name == 'sentStatus' && $this->sent == '1' ) 
		{
			if ( $value == 'pending' )
			{
				$this->is_pending = true;
			}
			
			if ( $value == 'active' )
			{
				$this->is_friend = true;
			}
		}

		if ( $name == 'recievedStatus' && $this->received == '1'  ) 
		{
			if ( $value == 'pending' )
			{
				$this->is_pending = true;
			}
			
			if ( $value == 'active' )
			{
				$this->is_friend = true;
			}
		}
	}
}