<?php

Class jempe_Form_validation extends CI_Form_validation{

	public $jquery_validation = FALSE;
	public $javascript_trim = FALSE;
	public $javascript_valid_email = FALSE;
	public $jquery_open = FALSE;
	public $_error_prefix = '<p>';
	public $_error_suffix = '</p>';

	function jquery_rules($field, $label = '', $rule = ''){

		$CI =& get_instance();
		$CI->load->library('jempe_form');
		$CI->lang->load('form_validation');

		if( $this->jquery_open === false ){
			$CI->jempe_form->jquery_validation_functions[] = '
					$("#' .$this->jquery_validation .'").submit(function(){
			';
			$this->jquery_open = true;
		}

		if( $rule == "trim" ){

			if( !$this->javascript_trim ){
				$CI->jempe_form->jquery_validation_functions[] = '
					function trim(str, chars) {
						return ltrim(rtrim(str, chars), chars);
					}
					
					function ltrim(str, chars) {
						chars = chars || "\\\\s";
						return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
					}
					
					function rtrim(str, chars) {
						chars = chars || "\\\\s";
						return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
					}
				';
				$this->javascript_trim = true;
			}
			$CI->jempe_form->jquery_validation_functions[] = '
					var field_value = $(this).find("*[name=\'' .$field .'\']").val();
					$(this).find("*[name=\'' .$field .'\']").val( trim( field_value , " " ) );
			';
			
		}

		if( $rule == "required" ){
			$CI->jempe_form->jquery_validation_functions[] = '
					var field_value = $(this).find("*[name=\'' .$field .'\']").val();

					if( field_value == "" || field_value == " " ){
						alert( "' .str_replace("%s" , $label , $CI->lang->line('required') ) .'" );
						return false;
					}
			';
		}

		if( $rule == "valid_email" ){

			if( !$this->javascript_valid_email ){
				$CI->jempe_form->jquery_validation_functions[] = '
					function valid_email(email) {
						if( email == "" )
							return true;

						var exp = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
						if(exp.test(email) == false) {
							return false;
						}else{
							return true;
						}
					}
				';
			}

			$CI->jempe_form->jquery_validation_functions[] = '
					var field_value = $(this).find("*[name=\'' .$field .'\']").val();
					if( !valid_email(field_value) ){
						alert( "' .str_replace("%s" , $label , $CI->lang->line('valid_email') ) .'" );
						return false;
					}
			';
		}

		if( strpos( $rule , "matches[") === 0 ){

			$match_value = explode( "[" , $rule );
			$match_value = str_replace( "]" , "" , $match_value[1] );

			$CI->jempe_form->jquery_validation_functions[] = '
					var field_value = $(this).find("*[name=\'' .$field .'\']").val();
					var match_value = $(this).find("*[name=\'' .$match_value .'\']").val();
					if( field_value != match_value ){
						alert( "' .str_replace("%s" , $label , $CI->lang->line('matches') ) .'" );
						return false;
					}
			';
		}

	}


	// --------------------------------------------------------------------
	
	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @return	bool
	 */		
	function run($group = '')
	{

		if( $this->jquery_validation !== false ){
			$CI =& get_instance();
			$CI->load->library('jempe_form');

			if( count( $CI->jempe_form->jquery_validation_functions ) ){
				$CI->jempe_form->jquery_validation_functions[] = '
						});
				';
			}
		}

		// Do we even have any data to process?  Mm?
		if (count($_POST) == 0)
		{
			return FALSE;
		}
		
		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) == 0)
		{
			// No validation rules?  We're done...
			if (count($this->_config_rules) == 0)
			{
				return FALSE;
			}
			
			// Is there a validation rule for the particular URI being accessed?
			$uri = ($group == '') ? trim($this->CI->uri->ruri_string(), '/') : $group;
			
			if ($uri != '' AND isset($this->_config_rules[$uri]))
			{
				$this->set_rules($this->_config_rules[$uri]);
			}
			else
			{
				$this->set_rules($this->_config_rules);
			}
	
			// We're we able to set the rules correctly?
			if (count($this->_field_data) == 0)
			{
				log_message('debug', "Unable to find validation rules");
				return FALSE;
			}
		}
	
		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');
							
		// Cycle through the rules for each field, match the 
		// corresponding $_POST item and test for errors
		foreach ($this->_field_data as $field => $row)
		{		
			// Fetch the data from the corresponding $_POST array and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.
			
			if ($row['is_array'] == TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($_POST, $row['keys']);
			}
			else
			{
				if (isset($_POST[$field]) AND $_POST[$field] != "")
				{
					$this->_field_data[$field]['postdata'] = $_POST[$field];
				}
			}
		
			$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);		
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);

		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array();
		
		// No errors, validation passes!
		if ($total_errors == 0)
		{
			return TRUE;
		}

		// Validation fails
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input, validates the info, and stores it
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_rules($field, $label = '', $rules = '')
	{

		if( $this->jquery_validation !== false ){
			if( strpos( $rules , '|' ) !== false ){
				$jquery_rules = explode( '|' , $rules );

				foreach( $jquery_rules as $jquery_rule ){
					$this->jquery_rules($field, $label, $jquery_rule);
				}

			}else{
				$this->jquery_rules($field, $label, $rules);
			}
		}

		// No reason to set rules if we have no POST data
		if (count($_POST) == 0)
		{
			return;
		}
	
		// If an array was passed via the first parameter instead of indidual string
		// values we cycle through it and recursively call this function.
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem...
				if ( ! isset($row['field']) OR ! isset($row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name
				$label = ( ! isset($row['label'])) ? $row['field'] : $row['label'];

				// Here we go!
				$this->set_rules($row['field'], $label, $row['rules']);
			}
			return;
		}
		
		// No fields? Nothing to do...
		if ( ! is_string($field) OR  ! is_string($rules) OR $field == '')
		{
			return;
		}

		// If the field label wasn't passed we use the field name
		$label = ($label == '') ? $field : $label;

		// Is the field name an array?  We test for the existence of a bracket "[" in
		// the field name to determine this.  If it is an array, we break it apart
		// into its components so that we can fetch the corresponding POST data later		
		if (strpos($field, '[') !== FALSE AND preg_match_all('/\[(.*?)\]/', $field, $matches))
		{	
			// Note: Due to a bug in current() that affects some versions
			// of PHP we can not pass function call directly into it
			$x = explode('[', $field);
			$indexes[] = current($x);

			for ($i = 0; $i < count($matches['0']); $i++)
			{
				if ($matches['1'][$i] != '')
				{
					$indexes[] = $matches['1'][$i];
				}
			}
			
			$is_array = TRUE;
		}
		else
		{
			$indexes 	= array();
			$is_array	= FALSE;		
		}
		
		// Build our master array		
		$this->_field_data[$field] = array(
											'field'				=> $field, 
											'label'				=> $label, 
											'rules'				=> $rules,
											'is_array'			=> $is_array,
											'keys'				=> $indexes,
											'postdata'			=> NULL,
											'error'				=> ''
											);
	}


	/**
	 * Greater than zero
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function greater_than_zero($str)
	{
		if($str > 0)
			return true;
		else
			return false;
	}

	/**
	 * is available
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function is_unique($name, $data)
	{

		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_db');

		$params = explode(",", $data);

		if($params[3] > 0)
		{
			$not_equal_field = $params[2];
			$not_equal_value = $params[3];
		}
		else
		{
			$not_equal_field = $not_equal_value = '';
		}

		if(isset($params[4]) && strlen($params[4]))
		{
			$filter = $params[4];
		}
		else
		{
			$filter = '';
		}

		if($CI->jempe_db->is_unique($name, $params[1], $params[0], $filter, $not_equal_field, $not_equal_value))
		{
			return TRUE;
		}
		else
		{
			$this->set_message('is_unique', $CI->lang->line('jempe_is_unique'));
			return FALSE;
		}
	}

	/**
	 * is a valid url name
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function url($str)
	{
		if( ! preg_match("/^([-a-z0-9_-])+$/i", str_replace('.', '', $str)))
		{
			$CI =& get_instance();
			$this->set_message('url', $CI->lang->line('jempe_valid_url'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function valid_date($date){
		$CI =& get_instance();

		if( strtotime($date) === false ){
			$this->set_message('valid_date', $CI->lang->line('jempe_valid_date'));
			return false;
		}else
			return true;


			

	}

	function valid_time($time)
	{
		$CI =& get_instance();

		if(preg_match('/[\s0]*(\d|1[0-2]):(\d{2})\s*([AaPp][Mm])/xms', $time) == 0)
		{
			$this->set_message('valid_time', $CI->lang->line('jempe_valid_time'));
			return FALSE;
		}
		else
		{
			if(strtotime(date('Y-m-d').' '.$time) === FALSE)
			{
				$this->set_message('valid_time', $CI->lang->line('jempe_valid_time'));
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}

	/**
	 * valid credit expiration
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_credit_card_expiration($month,$year=false){

		$CI =& get_instance();

		if( !$year ){
			$time = strtotime($month);
		}else{
			$time = strtotime($year."-" .$month ."-01");
		}

		if( $time > time() ){
			return true;
		}else{
			$this->set_message('valid_credit_card_expiration', $CI->lang->line('jempe_valid_credit_card_expiration'));
			return false;
		}
	
	}

	/**
	 * valid credit card
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function valid_credit_card($ccnum,$type=""){

		$CI =& get_instance();

		$creditcard=array(  "visa"=>"/^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/",
					"mc"=>"/^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/",
					"discover"=>"/^6011-?\d{4}-?\d{4}-?\d{4}$/",
					"amex"=>"/^3[4,7]\d{13}$/",
					"diners"=>"/^3[0,6,8]\d{12}$/",
					"bankcard"=>"/^5610-?\d{4}-?\d{4}-?\d{4}$/",
					"jcb"=>"/^[3088|3096|3112|3158|3337|3528]\d{12}$/",
					"enroute"=>"/^[2014|2149]\d{11}$/",
					"switch"=>"/^[4903|4911|4936|5641|6333|6759|6334|6767]\d{12}$/");
		if(empty($type))
		{
			$match=false;
			foreach($creditcard as $type=>$pattern)
			if(preg_match($pattern,$ccnum)==1)
			{
				$match=true;
				break;
			}
	
			if(!$match){
				$this->set_message('valid_credit_card', $CI->lang->line('jempe_valid_credit_card'));
				return false;
			}else{
				return $this->_checkSum($ccnum);
			}
		
		}else{
			if(@preg_match($creditcard[strtolower(trim($type))],$ccnum)==0){
				$this->set_message('valid_credit_card', $CI->lang->line('jempe_valid_credit_card'));
				return false;
			}else{
				return $this->_checkSum($ccnum);
			}
		}
	
	}
	
	
	function _checkSum($ccnum){

	$CI =& get_instance();

		$checksum = 0;
		for ($i=(2-(strlen($ccnum) % 2)); $i<=strlen($ccnum); $i+=2){
			$checksum += (int)($ccnum{$i-1});
		}
		// Analyze odd digits in even length strings or even digits in odd length strings.
		for ($i=(strlen($ccnum)% 2) + 1; $i<strlen($ccnum); $i+=2){
			$digit = (int)($ccnum{$i-1}) * 2;
			if ($digit < 10)
				{ $checksum += $digit; }
			else
				{ $checksum += ($digit-9); }
		}
		if (($checksum % 10) == 0)
			return TRUE;
		else{
			$this->set_message('valid_credit_card', $CI->lang->line('jempe_valid_credit_card'));
			return FALSE;
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Text captcha validation
	 *
	 *
	 * @access	public
	 * @return	bool
	 */
	function textcaptcha($answer)
	{
		$CI =& get_instance();
		
		if($CI->session->userdata("jempe_textcaptcha_id") > 0)
		{
			$answers = $CI->db->get_where("jempe_text_captcha_answers", array("tc_answer_".$CI->config->item("language") => md5($answer), "tc_answer_question" => $CI->session->userdata("jempe_textcaptcha_id")));

			if($answers->num_rows() > 0)
			{
				return TRUE;
			}
		}

		$this->set_message('textcaptcha', $CI->lang->line('jempe_textcaptcha_error'));
		return FALSE;
		
	}

	 // ------------------------------------------------------------------------

	/**
	* recaptcha validation
	*
	* validate recaptcha answer
	*
	* @access	public
	* @return	bool
	*/
	function recaptcha()
	{
		$CI =& get_instance();
		require_once(APPPATH.'libraries/recaptchalib.php');

		$resp = recaptcha_check_answer ($CI->jempe_form->recaptcha_private_key,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

		if( ! $resp->is_valid)
		{
			$this->set_message('recaptcha', str_replace('{error}', $resp->error, $CI->lang->line('jempe_recaptcha_error')));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	 // ------------------------------------------------------------------------

	 /**
	 * Validate USA Zip Code
	 *
	 * @access	public
	 * @return	bool
	 */
	function USAZip($zip_code)
	{
		$CI =& get_instance();

		if(preg_match("/^([0-9]{5})(-[0-9]{4})?$/i", $zip_code))
		{
			return TRUE;
		}
		else
		{
			$this->set_message('USAZip',  $CI->lang->line('jempe_USAZip_error'));
			return FALSE;
		}
	}
	 // ------------------------------------------------------------------------

	 /**
	 * Validate USA Phone Number
	 *
	 * @access	public
	 * @return	bool
	 */
	function USAPhone($phone)
	{
		$CI =& get_instance();

		if(preg_match('/^\(?(\d{3})\)?[-\. ]?(\d{3})[-\. ]?(\d{4})$/', $phone))
		{
			return TRUE;
		}
		else
		{
			$this->set_message('USAPhone',  $CI->lang->line('jempe_USAPhone_error'));
			return FALSE;
		}
	}
}

?>