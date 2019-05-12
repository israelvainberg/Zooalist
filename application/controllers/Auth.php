<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Auth extends MY_Controller 
{
	/**
	 * __construct
	 *
	 * Set response headers for cross origin requests
	 *
	 * @access    public
	 *
	 * @return    null
	 */
    public function __construct() 
	{
        parent::__construct();
	}

	/**
	 * login_get
	 *
	 * Load the signup view
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function signup_get() 
	{
		if ( Rbac::$logged ) 
		{
			redirect(base_url(), 'refresh');
		}

		// Load the dashboard template
		$this->load->template('auth');

		// Construct the front end model
		$this->template->set('model', [
			'signup' => [
				'data' => [
					'firstname' => '',
					'lastname' => '',
					'email' => '',
					'password' => ''
				]
			]
		]);

		$this->load->js('assets/js/views/signup');

		// Load the signup page
		$this->load->view('signup');
	}
	
	/**
	 * signup_post
	 *
	 * Create a new Taptac account
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function signup_post() {
		// Input data validation
		$this->validate_input();

		// Create a user record
		$this->load->model( 'users_model' );
		$this->users_model->merge( $this->post() );
		$this->users_model->role = 'user';
		$this->users_model->status = 'active'; // Verification mechanism should come here
		$this->users_model->password = password_hash( $this->post( 'password' ), PASSWORD_BCRYPT );
		$user_id = $this->users_model->create( true );

		if ( ! $user_id ) 
		{
			$this->platform_error();
		} 
		else 
		{
			// Add the current user object to the session
			$this->session->set_userdata( 'zoo_user', [ 
				'user_id' => $user_id,
				'firstname' => $this->post( 'firstname' ),
				'lastname' => $this->post( 'lastname' ),
				'email' => $this->post( 'email' ),
				'status' => 'active',
				'role' => 'user'
			] );

			$this->response([
				'status' => true,
				'message' => 'Signed up'
			], MY_Controller::HTTP_OK);
		}
	}

	/**
	 * login_get
	 *
	 * Load the login view
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function login_get() 
	{
		if ( Rbac::$logged ) 
		{
			redirect(base_url(), 'refresh');
		}

		// Load the dashboard template
		$this->load->template('auth');

		// Construct the front end model
		$this->template->set('model', [
			'login' => [
				'data' => [
					'email' => '',
					'password' => '',
					'remember' => false
				]
			],
			'recover' => [
				'data' => [
					'email' => ''
				]
			]
		]);

		$this->load->js('assets/js/views/login');

		// Load the login page
		$this->load->view('login');
	}
	
	/**
	 * login_post
	 *
	 * Login in using user credentials
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function login_post() {
		// Input data validation
		$this->validate_input();

		$email = $this->post( 'email' );
		$password = $this->post( 'password' );
		$remember = $this->post( 'remember' );

		$this->load->model( 'users_model' );
		$this->users_model->merge( $this->post() );
		if ( $user = $this->users_model->verifyIdentitiy() ) {

			// Add the current user object to the session
			$this->session->set_userdata( 'zoo_user', [ 
				'user_id' => $user->user_id,
				'firstname' => $user->firstname,
				'lastname' => $user->lastname,
				'email' => $user->email,
				'status' => $user->status,
				'role' => $user->role
			] );

			$this->response([
				'status' => true,
				'message' => 'Logged in'
			], MY_Controller::HTTP_OK);
		}
		else 
		{
			$this->response([
				'status' => true,
				'message' => 'Email or password are invalid'
			], MY_Controller::HTTP_UNAUTHORIZED);
		}
	}

	/**
	 * recover_post
	 *
	 * Request to receive a recover password instructions
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function recover_post() {
		// Input data validation
		$this->validate_input();

		if ( $forgotten ) 
		{
			$this->response([
				'status' => true,
				'message' => $this->post('email') . ', Help is on the way!'
			], MY_Controller::HTTP_OK);
		} 
		else 
		{
			$this->response([
				'status' => true,
				'message' => 'Invalid email was supplied'
			], MY_Controller::HTTP_BAD_REQUEST);
		}
	}

	/**
	 * logout_get
	 *
	 * Logout from the platform
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function logout_get() 
	{
		$this->session->sess_destroy();
		redirect( base_url() . 'login', 'refresh' );
	}
}