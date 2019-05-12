<?php
defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

class MY_Form_validation extends CI_Form_validation 
{
	/**
	 * Initialize Form_Validation class
	 *
	 * @param	array	$rules
	 * @return	void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->lang->load('validation');
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
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @param	string	$group
	 * @return	bool
	 */
	public function run($group = '')
	{
		$validation_array = empty($this->validation_data)
			? $_POST
			: $this->validation_data;

		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) === 0)
		{
			if (empty($group))
			{
				// Is there a validation rule for the particular URI being accessed?
				$group = trim($this->CI->uri->ruri_string(), '/') . '_'.$this->input->method();
				isset($this->_config_rules[$group]) OR $group = $this->CI->router->class.'/'.$this->CI->router->method;
			}

			$this->set_rules(isset($this->_config_rules[$group]) ? $this->_config_rules[$group] : $this->_config_rules);

			// Were we able to set the rules correctly?
			if (count($this->_field_data) === 0)
			{
				log_message('debug', 'Unable to find validation rules');
				return FALSE;
			}
		}

		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field and match the corresponding $validation_data item
		foreach ($this->_field_data as $field => &$row)
		{
			// Fetch the data from the validation_data array item and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
			if ($row['is_array'] === TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($validation_array, $row['keys']);
			}
			elseif (isset($validation_array[$field]))
			{
				$this->_field_data[$field]['postdata'] = $validation_array[$field];
			}
		}

		// Execute validation rules
		// Note: A second foreach (for now) is required in order to avoid false-positives
		//	 for rules like 'matches', which correlate to other validation fields.
		foreach ($this->_field_data as $field => &$row)
		{
			// Don't try to validate if we have no rules set
			if (empty($row['rules']))
			{
				continue;
			}

			$this->_execute($row, $row['rules'], $row['postdata']);
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);
		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		empty($this->validation_data) && $this->_reset_post_array();

		return ($total_errors === 0);
	}


	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, any custom error messages, validates the info,
	 * and stores it
	 *
	 * @param	mixed	$field
	 * @param	string	$label
	 * @param	mixed	$rules
	 * @param	array	$errors
	 * @return	CI_Form_validation
	 */
	public function set_rules($field, $label = '', $rules = array(), $errors = array())
	{
		// If an array was passed via the first parameter instead of individual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				
				// If the field label wasn't passed we use the field name
				$label = isset($row['label']) ? $row['label'] : $row['field'];
				
				// Add the custom error message array
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				foreach($errors as $key => &$error)
				{
					$_rules_filtered = current(
						array_values(
							array_filter(
								preg_split('/\|(?![^\[]*\])/', $row['rules']), function($v) use($key){
									return strpos($v, $key) !== FALSE;
								}
							)
						)
					);

					$param = '';
					if(preg_match('/(.*?)\[(.*)\]/', $_rules_filtered, $match)) {
						$param = count($match) > 2 ? $match[2] : '';
					}

					$error = $this->_translate_fieldname($error, $row['label'], $param);
				}

				// Here we go!
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
		}

		// No fields or no rules? Nothing to do...
		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			// BC: Convert pipe-separated rules string to an array
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		// If the field label wasn't passed we use the field name
		$label = ($label === '') ? $field : $label;

		$indexes = array();

		// Is the field name an array? If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

		// Build our master array
		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}
	
	public function error_messages() {
		$messages = [];
		foreach($this->_error_array as $key => $value) {
			$messages[str_replace(['[',']'], ['_',''], $key)] = $value;
		}
		
		return $messages;
	}

	/**
	 * Translate a field name
	 *
	 * @param	string	the field name
	 * @return	string
	 */
	protected function _translate_fieldname($fieldname, $field = false, $param = '')
	{
		// Do we need to translate the field name? We look for the prefix 'lang:' to determine this
		// If we find one, but there's no translation for the string - just return it
		if (sscanf($fieldname, 'lang:%s', $line) === 1 && FALSE !== ($fieldname = $this->CI->lang->line($line, FALSE)))
		{
			if ( $field === false ) 
			{
				return $fieldname;
			} 
			else 
			{
				return $this->compile( $field, $fieldname, $param );
			}
		}

		return $fieldname;
	}


	/**
	 * compile
	 *
	 * Compile an error message
	 *
	 * @access    public
	 *
	 * @param    $field
	 *
	 * @param    $rule
	 *
	 * @return    mixed
	 */
	public function compile( $field, $rule, $param ) {
		$compiled = str_replace( '{field}', $this->_translate_fieldname($field), $rule );
		$compiled = str_replace( '{param}', $param, $compiled);
		return $compiled;
	}

	/**
	 * Required
	 *
	 * @param    string
	 *
	 * @return    bool
	 */
	public function required( $str ) {
		if ( is_bool( $str ) ) {
			return true;
		} else if ( is_array( $str ) ) {
			return empty( $str );
		} else {
			return trim( $str ) !== '';
		}
	}

	/**
	 * alpha_and_numeric
	 *
	 * Verify that the string provided contain alphabetical and numeric characters
	 *
	 * @param   string $string
	 *
	 * @return  bool
	 */
	public function alpha_and_numeric( $string ) {
		if ( preg_match( '#[0-9]#', $string ) && preg_match( '#[a-zA-Z]#', $string ) ) {
			return true;
		}
		$this->set_message( 'alpha_and_numeric', $this->lang->line( 'alpha_and_numeric' ) );
		return false;
	}

	/**
	 * is_boolean
	 *
	 * Verify that the string provided contain alphabetical and numeric characters
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function is_boolean( $str ) {
		if ( in_array( $str, array( "true", "false", "1", "0", true, false ), true ) ) {
			return true;
		}
		$this->set_message( 'is_boolean', $this->lang->line( 'is_boolean' ) );

		return false;
	}

	/**
	 * is_binary
	 *
	 * Verify that the string provided contain alphabetical and numeric characters
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function is_binary( $str ) {
		if ( in_array( $str, array( 1, 0, "1", "0" ), true ) ) {
			return true;
		}
		$this->set_message( 'is_binary', $this->lang->line( 'is_binary' ) );

		return false;
	}

	/**
	 * date_time
	 *
	 * Verify that the string provided is a valid date time
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function datetime( $str ) {
		$exp = '/^([0-9]{4})([\-])([0-9]{2})([\-])([0-9]{2})[\ ]'
		       . '([0-9]{2})[\:]([0-9]{2})[\:]([0-9]{2})$/';

		$match = array();
		if ( ! preg_match( $exp, $str, $match ) ) {
			$this->set_message( 'datetime', $this->lang->line( 'datetime' ) );

			return false;
		}

		return true;
	}

	/**
	 * us_number
	 *
	 * Verify that the string provided is a valid us number
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function us_number( $str ) {
		$sPattern = "/^
        (?:                                 # Area Code
            (?:                            
                \(                          # Open Parentheses
                (?=\d{3}\))                 # Lookahead.  Only if we have 3 digits and a closing parentheses
            )?
            (\d{3})                         # 3 Digit area code
            (?:
                (?<=\(\d{3})                # Closing Parentheses.  Lookbehind.
                \)                          # Only if we have an open parentheses and 3 digits
            )?
            [\s.\/-]?                       # Optional Space Delimeter
        )?
        (\d{3})                             # 3 Digits
        [\s\.\/-]?                          # Optional Space Delimeter
        (\d{4})\s?                          # 4 Digits and an Optional following Space
        (?:                                 # Extension
            (?:                             # Lets look for some variation of 'extension'
                (?:
                    (?:e|x|ex|ext)\.?       # First, abbreviations, with an optional following period
                |
                    extension               # Now just the whole word
                )
                \s?                         # Optionsal Following Space
            )
            (?=\d+)                         # This is the Lookahead.  Only accept that previous section IF it's followed by some digits.
            (\d+)                           # Now grab the actual digits (the lookahead doesn't grab them)
        )?                                  # The Extension is Optional
		$/x";                               // /x modifier allows the expanded and commented regex
		
		if ( ! preg_match($sPattern, $str, $aMatches) ) {
			$this->set_message( 'us_number', $this->lang->line( 'us_number' ) );
			return false;
		}

		return true;
	}

	/**
	 * datetime_or_null
	 *
	 * Verify that the string provided is a valid date time or a null value
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function datetime_or_null( $str ) {
		return $str === 'null' || $this->datetime( $str );
	}

	/**
	 * db_exists
	 *
	 * Verify the given string against a database column value
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function db_exists( $str, $field ) {
		sscanf( $field, '%[^.].%[^.]', $table, $field );

		return isset( $this->CI->db )
			? ( $this->CI->db->limit( 1 )->get_where( $table, array( $field => $str ) )->num_rows() > 0 )
			: false;
	}

	/**
	 * db_not_exists
	 *
	 * Verify the given string against a database column value
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function db_not_exists( $str, $field ) {
		sscanf( $field, '%[^.].%[^.]', $table, $field );

		return isset( $this->CI->db )
			? ( $this->CI->db->limit( 1 )->get_where( $table, array( $field => $str ) )->num_rows() < 1 )
			: false;
	}

	/**
	 * valid_json
	 *
	 * Verify that the given JSON string is a valid json format
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function valid_json( $str ) {
		$result = json_decode( $str );

		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * is_password_strong
	 *
	 * Verify that the given password contain at least one integer and one character
	 *
	 * @param   string $str
	 *
	 * @return  bool
	 */
	public function is_password_strong($password)
	{
		if(preg_match("#[0-9]+#", $password) && (preg_match("#[a-z]+#", $password) || preg_match("#[A-Z]+#", $password) || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+!-]/", $password)))
			return TRUE;
		
		return FALSE;
	}
}
