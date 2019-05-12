<?php

class Users_model extends MY_Model 
{
    protected $table = 'users';

    protected $primary = 'user_id';

    public $user_id = NULL;

    public $status = 'pending';

    public $email = null;

    public $password = null;

    public $firstname = null;

    public $lastname = null;

    public $role = null;

	function __construct()
	{
        parent::__construct();
        $this->load->model( 'Interfaces/Root' );
        $this->load->model( 'Interfaces/User' );
    }

    public function get_active()
    {
        $this->db->select($this->table . '.*');
        $this->db->from( $this->table );
        $this->db->where( $this->table . '.' . $this->primary, $this->user_id );

        $query = $this->db->get();
        return $query->row( 0, 'Root' );
    }

    public function set_status( $status )
    {
        return $this->modify( [
            'status' => $status
        ] );
    }

    public function set_auto_status( $status = 'active' )
    {
        if ( Rbac::$user->status == 'pending' && ! Rbac::$user->has_payment )
        {
            return $this->set_status( 'billing' );
        }

        if ( Rbac::$user->status == 'billing' && ! Rbac::$user->has_business )
        {
            return $this->set_status( 'details' );
        }

        if ( Rbac::$user->status !== $status )
        {
            return $this->set_status( $status );
        }

        return false;
    }

    public function verifyIdentitiy()
    {
        $user = $this->findOneBy( [
            'email' => $this->email
        ] );

        if ( $user ) 
        {
            if ( password_verify( $this->password, $user->password ) )
            {
                return $user;
            }
        }

        return false;
    }

    public function get_users_and_friends()
    {
        $select = [
            'u.user_id',
            'u.email',
            'u.firstname',
            'u.lastname'
        ];

        $fields = $this->merge_fields( $select, [
            'count(distinct f.friends_id) as received',
            'count(distinct f2.friends_id) as sent',
            'f.status as recievedStatus',
            'f2.status as sentStatus',
        ] );

        $this->db->select( implode( ',', $fields ) );
        $this->db->from( $this->table . ' u' );
        $this->db->join( 'friends f', 'f.user_id = u.user_id AND f.friend_id = ' . Rbac::$user->user_id, 'LEFT' );
        $this->db->join( 'friends f2', 'f2.friend_id = u.user_id AND f2.user_id = ' . Rbac::$user->user_id, 'LEFT' );
        $this->db->where( 'u.user_id !=', Rbac::$user->user_id );
        $this->db->where( 'u.status', 'active' );
        $this->db->group_by( 'u.user_id' );

        $query = $this->db->get();
        
        if ( $query->num_rows() > 0 ) 
        {
            return $query->result( 'User' );
        } 
        else 
        {
			return false;
		}
    }
}