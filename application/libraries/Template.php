<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template Class
 *
 * Template loader & parawe
 *
 * @author			Israel Vainberg
 */
class Template
{
    // Templates path
    public $template_dir = 'templates/';

    // Template variables
    private $data = [
		'language' => 'en',
        'metadata' => [
			'charset' => 'UTF-8'
		],
		'title' => 'Zooalist',
		'template' => 'dashboard',
		'css' => [],
		'js' => [],
		'model' => []
    ];

    /**
	 * constructor
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($config = array())
	{
		if ( ! empty( $config ) )
		{
            $this->set( $config );
		}

        log_message('debug', 'Template Class Initialized');
    }

    /**
	 * Magic Get function to get data
	 *
	 * @access	public
	 * @param	  string
	 * @return	mixed
	 */
	public function __get($name)
	{
        if ( isset( $this->data[$name] ) ) {
			return $this->data[$name];
		} else if ( isset( get_instance()->$name ) ){
			return get_instance()->$name;
        }
        
        return null;
	}

	// --------------------------------------------------------------------

	/**
	 * Magic Set function to set data
	 *
	 * @access	public
	 * @param	  string
	 * @return	mixed
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * Set data using a chainable metod. Provide two strings or an array of data.
	 *
	 * @access	public
	 * @param	string $name - property name
	 * @param   mixed $value - property value(s)
	 * @return	object
	 */
	public function set($name, $value = NULL)
	{
		if (is_array($name) || is_object($name))
		{
			foreach ($name as $item => $value)
			{
				$this->data[$item] = $value;
			}
		}
		else
		{
			if ( array_key_exists($name, $this->data) && is_array( $this->data[$name] ) && is_array( $value ) ) {
				$this->data[$name] = array_merge( $this->data[$name], $value );
			} else {
				$this->data[$name] = $value;
			}
        }
		return $this;
	}
    
    public function set_template($template = NULL)
	{
        if ( file_exists($this->template_dir.$this->template . '.php') ) {
            $this->template = $template;
        }

        return $this;
    }
    
    public function build() {
        $this->load->view( $this->template_dir.$this->template , $this->data );
    }
}