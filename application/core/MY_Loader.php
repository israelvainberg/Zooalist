<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class MY_Loader extends CI_Loader {

	private $_javascript = array();

	private $_css = array();

	/**
	 * Stylesheet Loader
	 *
	 * @access    public
	 *
	 * @params    string    relative paths to stylesheet files
	 *
	 * @return    object
	 */
	public function css() {
		foreach ( func_get_args() as $file ) {
			$file = substr( $file, 0, 1 ) == '/' ? substr( $file, 1 ) : $file;
			if ( is_bool( $file ) ) {
				continue;
			}

			$file .=  (strpos($file, '.css') === FALSE) ? '.css' : '';
			$is_external = preg_match( "/^https?:\/\//", trim( $file ) ) > 0 ? true : false;
			if ( ! $is_external ) {
				if ( ! file_exists( FCPATH . $file ) ) {
					show_error( "Cannot locate stylesheet file: {$file}." );
				}
			}
			$file = $is_external == false ? base_url() . $file . '?' . filemtime( $file ) : $file;
			if ( ! in_array( $file, $this->_css ) ) {
				$this->_css[] = $file;
			}
		}

		return $this;
	}

	/**
	 * Stylesheet getter
	 *
	 * @access    public
	 *
	 * @return    array
	 */
	public function get_css_files() {
		return $this->_css;
	}

	/**
	 * Javascript Loader
	 *
	 * @access    public
	 *
	 * @params    string    relative paths to javascript files
	 *
	 * @return    object
	 */
	public function js() {
		foreach ( func_get_args() as $file ) {
			$filename = $file;
			$props = [];
			if ( is_array( $file ) ) {
				$filename = key( $file );
				$props = $file[$filename];
			}

			$filename = substr( $filename, 0, 1 ) == '/' ? substr( $filename, 1 ) : $filename;
			if ( is_bool( $filename ) ) {
				continue;
			}
			$filename .=  (strpos($filename, '.js') === FALSE) ? '.js' : '';
			$is_external = preg_match( "/^https?:\/\//", trim( $filename ) ) > 0 ? true : false;
			if ( ! $is_external ) {
				if ( ! file_exists( FCPATH . $filename ) ) {
					show_error( "Cannot locate javascript file: {$filename}." );
				}
			}
			$filename = $is_external == false ? base_url() . $filename . '?' . filemtime( $filename ) : $filename;
			if ( ! in_array( $filename, array_keys( $this->_javascript ) ) ) {
				$this->_javascript[$filename] = $props;
			}
		}

		return $this;
	}

	/**
	 * Javascript getter
	 *
	 * @access    public
	 *
	 * @return    array
	 */
	public function get_js_files() {
		return $this->_javascript;
	}

	/**
	 * Template loader
	 *
	 * @access    public
	 *
	 * @return    void
	 */
	public function template( $name = '', $data = [] ) {
		$this->library('Template', array_merge( $data, [ 'template' => $name ] ) );
	}

	/**
	 * View Loader
	 *
	 * @access    public
	 *
	 * @return    void
	 */
	public function view($view, $vars = array(), $return = FALSE) {
		if ( isset( get_instance()->template ) && is_null( get_instance()->template->content ) ) {
			$view = parent::view($view, $vars, TRUE);
			get_instance()->template->set('js', $this->_javascript);
			get_instance()->template->set('content', $view)->build();
		} else {
			parent::view($view, $vars, $return);
		}
	}

	/**
	 * Database Loader
	 *
	 * @access    public
	 *
	 * @param    string    the DB credentials
	 * @param    bool    whether to return the DB object
	 * @param    bool    whether to enable active record (this allows us to override the config setting)
	 *
	 * @return    object
	 */
	public function database( $params = '', $return = false, $query_builder = null ) {
		// Grab the super object
		$CI =& get_instance();

		// Do we even need to load the database class?
		if ( $return === false && $query_builder === null && isset( $CI->db ) && is_object( $CI->db ) && ! empty( $CI->db->conn_id ) ) {
			return false;
		}

		require_once( BASEPATH . 'database/DB.php' );

		$db =& DB( $params, $query_builder );

		$my_driver      = config_item( 'subclass_prefix' ) . 'DB_' . $db->dbdriver . '_driver';
		$my_driver_file = APPPATH . 'libraries/' . $my_driver . '.php';

		if ( file_exists( $my_driver_file ) ) {
			require_once( $my_driver_file );
			$db_obj = new $my_driver( get_object_vars( $db ) );
			$db     =& $db_obj;
		}

		if ( $return === true ) {
			return $db;
		}

		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$CI->db = '';

		// Load the DB class
		$CI->db = $db;

		return $this;
	}
	
}