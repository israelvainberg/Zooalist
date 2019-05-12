<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class App extends MY_Controller 
{
	/**
	 * index_get
	 *
	 * Load the main view of the dashboard
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function index_get() {
		// Load the dashboard template
		$this->load->template('dashboard');

		// Load the list of platform users (extend with posts of friends)
		$this->load->model( 'users_model' );
		$this->users_model->user_id = Rbac::$user->user_id;
		$users_and_friends = $this->users_model->get_users_and_friends();

		// Sort the list of platform users by friends first
		usort( $users_and_friends, function( $a, $b ) {
			return strcmp( $b->is_friend, $a->is_friend );
		});

		// Filter the friends
		$friends_only = array_filter( $users_and_friends, function( $user ) {
			return $user->is_friend === true;
		} );

		// Get the posts of the user friends
		$this->load->model( 'posts_model' );
		$posts = $this->posts_model->get_posts_of_users( $friends_only );

		// Construct the front end model
		$this->template->set('model', [
			'user' => Rbac::$user,
			'users' => [
				'data' => $users_and_friends
			],
			'posts' => [
				'data' => $posts
			]
		]);

		$this->load->js('assets/js/views/main');

		$this->load->view('main');
	}

}
