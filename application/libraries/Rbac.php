<?php

class Rbac {
	// exit statuses
	const NEED_LOGIN 		= 'login';
	const NOT_AUTHORIZED 	= 'unauthorized';
	const NEED_VERIFY		= 'need_verify';

	// user statuses
	const PENDING 			= 'pending';
	const ACTIVE			= 'active';
	const BANNED			= 'banned';
	const DELETED			= 'deleted';

	var $router;
	var $route;
	var $class;
	var $method;
	var $action;
	var $return;

	public static $user = [];
	public static $logged = false;

	public static $crud = array(
		'post'   => 'create',
		'get'    => 'read',
		'put'    => 'update',
		'delete' => 'delete'
	);

	public static $public_access = array(
		'auth/login'           	=> array( 'read', 'create' ),
		'auth/signup' 			=> array( 'read', 'create' ),
		'auth/activate' 		=> array( 'read' ),
		'auth/recover' 			=> array( 'create' ),
		'auth/reset' 			=> array( 'read', 'create' )
	);

	// Define the restricted endpoints and operations for each role
	public static $permissions = array(
		'user' => array(
			'auth/logout'                 	=> array( 'read' ),
			'auth/verify'					=> array( 'read', 'create' ),
			'auth/signup'                 	=> array( 'read', 'post' ),
			'app/index'			            => array( 'read' ),
			'data/posts'			        => array( 'read', 'create' ),
			'data/users'					=> array( 'read' ),
			'data/requests'					=> array( 'read', 'create', 'update', 'delete' )
		),
		'admin' => array(
			'auth/logout'                 	=> array( 'read' ),
			'app/index'			            => array( 'read' ),
			'data/posts'			        => array( 'read', 'create' ),
			'data/users'					=> array( 'read' ),
			'data/requests'					=> array( 'read', 'create', 'update', 'delete' )
		)
	);

	/**
	 * __construct
	 *
	 * @access    public
	 *
	 * @param array|object|null $user
	 * 
	 * @return    RBAC instance
	 */
	function __construct() {
		$this->router    =& load_class( 'Router' );
		$this->route	 = strtolower($this->router->class . '/' . $this->router->method);
		$this->action    = self::$crud[$this->input->method( false )];

		$this->has_access();
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * @access    public
	 *
	 * @param    $name
	 *
	 * @return    mixed
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		} else {
			return get_instance()->$name;
		}
	}

	/**
	 * __set
	 *
	 * Enables the set public properties of the root user object
	 *
	 * @access    public
	 *
	 * @param    $name
	 * 
	 * @param    $value
	 *
	 * @return    void
	 */
	public function __set( $name, $value )
	{
		if ( isset( self::$user->$name ) )
		{
			self::$user->$name = $value;
		}
	}

	/**
	 * has_access
	 *
	 * Check the role's access to access endpoints and perform CRUD operations
	 *
	 * @access    public
	 *
	 * @return    boolean
	 */
	public function has_access() {
		// Public access endpoints
		if ( in_array( $this->route, array_keys( self::$public_access ) ) && in_array( $this->action, self::$public_access[ $this->route ] )) 
		{
			return true;
		}

		// Check if the user needs is logged in
		if ( is_null( $this->session->userdata('zoo_user') ) )
		{
			$this->return = self::NEED_LOGIN;
			return false;
		}

		// Check if the user's status requires a special routing redirection
		$user = $this->session->userdata('zoo_user');
		$this->load->model('users_model');
		$this->users_model->user_id = $user['user_id'];
		if ( ! self::$user = $this->users_model->get_active() )
		{
			$this->return = self::NEED_LOGIN;
			return false;
		}

		self::$logged = true;
		
		if ( self::$user->status == self::PENDING && $this->allowed_route() )
		{
			$this->return = self::NEED_VERIFY;
			return false;
		}

		// Check if the user's group is allowed to perform the requested CRUD operation on the requested endpoint
		$valid 	= false;

		if ( in_array( $this->route, array_keys( self::$permissions[ self::$user->role ] ) ) && in_array( $this->action, self::$permissions[ self::$user->role ][ $this->route ] ) )
		{
			// Valid group access permission and CRUD operation for the requested endpoint
			$valid = true;
		}

		if ( ! $valid )
		{
			$this->return = self::NOT_AUTHORIZED;
			return false;
		}

		return $valid;
	}

	private function allowed_route() 
	{
		$route = $this->current_route();

		switch (true)
		{
			case self::$user->status == self::PENDING:
				return ! in_array( $route, ['verify', 'logout'] );
				break;
		}

		return false;
	}

	private function current_route()
	{
		$routes = array_filter( array_reverse( $this->router->routes ), function( $value ) 
		{
			return strpos( $value, $this->route ) !== FALSE;
		} );

		if ( count( $routes ) > 0 )
		{
			return key($routes);
		}
		else 
		{
			return 'default_route';
		}
	}
}

?>