<?php

class Root extends MY_Model
{
	public $user_id = null;
	public $status = null;
	public $email = null;
	public $password = null;
	public $firstname = null;
	public $lastname = null;
	public $role = null;
	public $friends = [];
	
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

	public function get_friends()
	{
		$this->db->select('friends.friend_id, users.name');
		$this->db->from('friends');
		$this->db->join('users', 'users.user_id = friends.friend_id');
		$this->db->where('friends.user_id', $this->user_id);
		$this->db->where('friends.status', 'active');
		$query = $this->db->get();

		$this->friends = $query->result();
	}
}