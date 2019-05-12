<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class MY_Controller extends REST_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		date_default_timezone_set( 'UTC' );
		$this->access_control();
	}

	public function access_control() 
	{
		// Handle login requirements
		if ( $this->rbac->return === Rbac::NEED_LOGIN )
		{
			if ( $this->input->is_ajax_request() )
			{
				$this->response( [
					'status'  => false,
					'message' => 'Unauthorized request'
				], MY_Controller::HTTP_UNAUTHORIZED );
			}
			else 
			{
				// Save the current request url in a cookie and redirect to login
				$this->input->set_cookie( [
					'name'   => 'redirect',
					'value'  => current_url(),
					'expire' => '0'
				] );
				redirect( 'login' );
			}
		}

		// Handle special routes required by different user statuses
		if ( $this->rbac->return === Rbac::NEED_VERIFY )
		{
			if ( $this->input->is_ajax_request() )
			{
				$this->response( [
					'status'  => false,
					'message' => 'Verification required'
				], MY_Controller::HTTP_UNAUTHORIZED );
			}
			else 
			{
				// Redirect to verification page
				redirect('verify');
			}
		}

		// Handle regular unauthorized request
		if ( $this->rbac->return === Rbac::NOT_AUTHORIZED )
		{
			if ( $this->input->is_ajax_request() )
			{
				$this->response( [
					'status'  => false,
					'message' => 'Unauthorized request'
				], MY_Controller::HTTP_METHOD_NOT_ALLOWED );
			}
			else 
			{
				show_404();
			}
		}
	}

	public function validate_input( $data = false )
	{
		if ( ! is_array( $data ) ) 
		{
			$data = $this->post();
		}

		// Input data validation
		$this->load->library( 'form_validation' );
		$this->form_validation->set_data( $data );

		// Extend validation rules for complex multidumentional (indexed) arrays
		$this->route     =& load_class( 'Router' );
		$this->config->load('form_validation', TRUE );
		$route = $this->route->fetch_class() . '/' . $this->route->fetch_method() . '_' . $this->input->method( false );
		$rules = $this->config->item($route, 'form_validation');

		if ( ! is_null( $rules ) ) {
			$rules = array_values( array_filter( $rules, function($rule) {
				return strpos( $rule['field'], '[]' ) !== false;
			} ) );

			if (count( $rules ) > 0) {
				foreach( $rules as $index => $rule ) {
					$key = str_replace( array('[]'), '', $rule['field'] );
					$parent = substr( $key, 0, strpos( $key, '[' ) );
					$child = substr( $key, strpos( $key, '[') + 1, -1 );

					if ( array_key_exists( $parent, $data ) ) {
						foreach( $data[$parent] as $id => $field ) {
							$this->form_validation->set_rules( $parent . '[' . $id . '][' . $child . ']', $rule['label'], $rule['rules'] );
						}
					}
				}
			}
		}

		if ( $this->form_validation->run() == FALSE )
		{
			$this->response( [
				'status'  => false,
				'message' => $this->form_validation->error_messages()
			], MY_Controller::HTTP_NOT_ACCEPTABLE );

			return false;
		}

		return true;
	}

	public function checkRoleAccess( $user = null )
	{
		if ( ! $this->rbac->has_permission( $user ) ) 
		{
			$this->response( [
				'status'  => false,
				'message' => 'Unauthorized'
			], MY_Controller::HTTP_UNAUTHORIZED );

			return false;
		}

		return true;
	}

	public function platform_error() 
	{
		$error = (ENVIRONMENT !== 'production') ? $this->db->error() : [];

		$this->response( [
			'status'  => false,
			'message' => $error
		], MY_Controller::HTTP_INTERNAL_SERVER_ERROR );
	}

	public function mailgun_error()
	{
		$error = (ENVIRONMENT !== 'production') ? 'Mailgun error' : [];

		$this->response( [
			'status'  => false,
			'message' => $error
		], MY_Controller::HTTP_INTERNAL_SERVER_ERROR );
	}
}

?>