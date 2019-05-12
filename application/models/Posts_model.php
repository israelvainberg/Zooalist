<?php

class Posts_model extends MY_Model 
{
    protected $table = 'posts';

    protected $primary = 'post_id';

    public $post_id = null;

    public $user_id = null;

    public $content = null;

	function __construct()
	{
        parent::__construct();
    }

    public function get_posts_of_users( $users = [] )
    {
        if ( ! count( $users ) )
        {
            return $users;
        }

        $ids = array_map( function( $user ) {
			return $user->user_id;
        }, $users );

        $u = [];
        foreach( $users as $user )
        {
            $u['u' . $user->user_id] = $user;
        }
        
        $select = [
            'user_id',
            'content',
            'created'
        ];

        $this->db->select( implode( ',', $select ) );
        $this->db->from( $this->table );
        $this->db->where_in( 'user_id', $ids );
        $this->db->order_by( 'created', 'DESC' );
        $this->db->limit( 20 );

        $query = $this->db->get();

        foreach( $query->result() as &$item )
        {
            $item->firstname = $u['u' . $item->user_id]->firstname;
            $item->lastname = $u['u' . $item->user_id]->lastname;
        }

        return $query->result();
    }

}