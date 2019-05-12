<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class Data extends MY_Controller 
{
	/**
	 * posts_post
	 *
	 * Create a new post
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function posts_post()
	{
		// Input data validation
		$this->validate_input();

		$this->load->model( 'posts_model' );
		$this->posts_model->user_id = Rbac::$user->user_id;
		$this->posts_model->content = $this->post( 'content' );
		$post_id = $this->posts_model->create( true );
		
		if ( $post_id )
		{
			$this->response([
				'status' => true,
				'message' => 'Post is published'
			], MY_Controller::HTTP_OK);
		}
		else 
		{
			$this->platform_error();
		}
	}

	/**
	 * requests_post
	 *
	 * Create a friend requests from users
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function requests_post() {
		// Input data validation
		$this->validate_input();

		$this->load->model( 'friends_model' );
		$this->friends_model->user_id = Rbac::$user->user_id;
		$this->friends_model->friend_id = $this->post( 'id' );
		$created = $this->friends_model->create( true );
		
		if ( $created )
		{
			$this->response([
				'status' => true,
				'message' => 'Request was sent'
			], MY_Controller::HTTP_OK);
		}
		else 
		{
			$this->platform_error();
		}
	}

	/**
	 * requests_put
	 *
	 * Accept a friend requests from users
	 *
	 * @access    public
	 *
	 * @return    null
	 */
	public function requests_put() {
		// Input data validation
		$this->validate_input( $this->put() );

		$this->load->model( 'friends_model' );
		$this->friends_model->user_id = $this->put( 'id' );
		$this->friends_model->friend_id = Rbac::$user->user_id;
		$this->friends_model->status = 'active';
		$updated = $this->friends_model->upsert();
		
		if ( $updated )
		{
			$this->response([
				'status' => true,
				'message' => 'Request was accepted'
			], MY_Controller::HTTP_OK);
		}
		else 
		{
			$this->platform_error();
		}
	}

}