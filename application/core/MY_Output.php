<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class MY_Output extends CI_Output {
	const OUTPUT_MODE_NORMAL = 10;
	const OUTPUT_MODE_TEMPLATE = 11;

	// Templates directory
	private $template_dir = null;

	// Default Template in all views
	private $default_template = null;

	// The template in the current view
	private $template_name = null;

	// Internal - mode
	private $mode = self::OUTPUT_MODE_NORMAL;

	// Internal - template
	private $template = null;

	// Template parameters
	private $title = ""; //title tag value
	private $language = "en-us"; //html tag attribute
	private $meta = []; // meta tags

	// Addional parameters which can be passed to the view
	private $output_data = [];

	// Commons assets (shared between all views)
	private $template_commons = [];

	function __construct() {
		parent::__construct();
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
	 * set_template
	 *
	 * Set the template that should be contain the output <br /><em><b>Note:</b> This method set the output mode to MY_Output::OUTPUT_MODE_TEMPLATE</em>
	 *
	 * @access public
	 *
	 * @param string $template_view
	 *
	 * @return object
	 *
	 * @throws Exception
	 */
	public function set_template( $template_view ) {
		if ( ! isset( $this->template_dir ) ) {
			show_error( "Template directory is not set." );
		} else {
			$this->set_mode( self::OUTPUT_MODE_TEMPLATE );
			$template_view   = str_replace( ".php", "", $template_view );
			$this->template_name = $template_view;
			$this->template = $this->template_dir . $this->template_name;
		}
		return $this;
	}

	/**
	 * set_mode
	 *
	 * Set the template that should be contain the output <br /><em><b>Note:</b> This method set the output mode to MY_Output::OUTPUT_MODE_TEMPLATE</em>
	 *
	 * @access public
	 *
	 * @param integer $mode one of the constants MY_Output::OUTPUT_MODE_NORMAL or MY_Output::OUTPUT_MODE_TEMPLATE
	 *
	 * @return object
	 *
	 * @throws Exception
	 */
	public function set_mode( $mode ) {
		switch ( $mode ) {
			case self::OUTPUT_MODE_NORMAL:
			case self::OUTPUT_MODE_TEMPLATE:
				$this->mode = $mode;
				break;
			default:
				show_error( "Unknown output mode." );
		}
		return $this;
	}

	/**
	 * set_output_data
	 *
	 * Set additional data which is being passed to the template view
	 *
	 * @access public
	 *
	 * @param string $key the name of the variable in the output template
	 *
	 * @return object
	 */
	public function set_output_data( $key, $value = false ) {
		if ( $value === false && is_array( $key ) ) {
			$this->output_data = $key;
		} else {
			$this->output_data[ $key ] = $value;
		}
		
		return $this;
	}

	/**
	 * set_title
	 *
	 * Set the title of a page
	 *
	 * @access public
	 *
	 * @param string $title
	 *
	 * @return object
	 */
	public function set_title( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * append_title
	 *
	 * Append to the title additional string
	 *
	 * @access public
	 *
	 * @param string $title
	 *
	 * @return object
	 */
	public function append_title( $title, $delimiter = '|' ) {
		$this->title .= " " . $delimiter . " {$title}";
		return $this;
	}

	/**
	 * prepend_title
	 *
	 * Prepend to the title additional string
	 *
	 * @access public
	 *
	 * @param string $title
	 *
	 * @return object
	 */
	public function prepend_title( $title, $delimiter = '|' ) {
		$this->title = "{$title} " . $delimiter . " {$this->title}";
		return $this;
	}

	/**
	 * set_meta
	 *
	 * Set the meta tags for the template renderer
	 *
	 * @access public
	 *
	 * @param string $name the name of the meta tag key
	 *
	 * @param string|array $content the content of the meta tag
	 *
	 * @return object
	 */
	public function set_meta($name, $content){
		$this->meta[$name] = $content;
		return $this;
	}

	/**
	 * get_template_output
	 *
	 * Render the collected data into the template
	 *
	 * @access public
	 *
	 * @param string $output
	 *
	 * @return void
	 */
	public function _display( $output = '' ) {
		if ( $output == '' ) {
			$output = $this->get_output();
		}
		switch ( $this->mode ) {
			case self::OUTPUT_MODE_TEMPLATE:
				$output = $this->get_template_output( $output );
				break;
		}
		parent::_display( $output );
	}

	/**
	 * get_template_output
	 *
	 * Render the collected data into the template
	 *
	 * @access private
	 *
	 * @param string $output
	 *
	 * @return string
	 */
	private function get_template_output( $output ) {
		if ( ! is_null( get_instance() ) ) {
			
			// Load commons assets
			$this->set_commons();

			// Define the compiled output data
			$data = [
				'output'     	 => $output,
				'title'      	 => $this->title,
				'language'   	 => $this->language,
				'js'         	 => $this->load->get_js_files(),
				'css'        	 => $this->load->get_css_files(),
				'meta'       	 => $this->meta,
				'template'   	 => $this->template_name,
				'ci'         	 => get_instance()
			];

			$data   = array_merge_recursive( $data, $this->output_data );
			$output = $this->load->view( $this->template, $data, true );
		}

		return $output;
	}

	/**
	 * set_commons
	 *
	 * Set the common configuration of the view in a specific template
	 *
	 * @access private
	 *
	 * @param string|null $template_name the template for which the commons should be returned
	 *
	 * @return array
	 */
	private function set_commons( $template_name = null ) {
		if ( is_null( $template_name ) ) {
			$template_name = $this->template_name;
		}

		if ( array_key_exists( $template_name, $this->template_commons ) && is_array( $this->template_commons[$template_name] ) ) {
			if ( array_key_exists( 'js', $this->template_commons[$template_name] ) && is_array( $this->template_commons[$template_name]['js'] ) ) {
				foreach( $this->template_commons[$template_name]['js'] as $key => $file ) {
					if ( is_string( $file ) )
						$this->load->js( $file );
					else {
						$this->load->js( [ $key => $file ] );
					}
				}
			}

			if ( array_key_exists( 'css', $this->template_commons[$template_name] ) && is_array( $this->template_commons[$template_name]['css'] ) ) {
				foreach( $this->template_commons[$template_name]['css'] as $file ) {
					$this->load->css( $file );
				}
			}
		}

		return [];
	}
}