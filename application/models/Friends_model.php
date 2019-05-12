<?php

class Friends_model extends MY_Model 
{
    protected $table = 'friends';

    protected $primary = 'friends_id';

    public $friends_id = null;

    public $user_id = null;

    public $friend_id = null;

    public $status = 'pending';

	function __construct()
	{
        parent::__construct();
    }
    
}