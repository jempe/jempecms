<?php

Class 	jempe_Input extends CI_Input{

	/**
	* Fetch from array
	*
	* This is a helper function to retrieve values from global arrays
	*
	* @access	private
	* @param	array
	* @param	string
	* @param	bool
	* @return	string
	*/
	function _fetch_from_array(&$array, $index = '', $xss_clean = FALSE)
	{

		if( strpos( $index , "[" ) !== false && strpos( $index , "]" ) !== false ){
	
			$name_parts = explode( "[" , $index );
			$var_name = $name_parts[0];
			$array_item = str_replace( "]" , "" , $name_parts[1] );

			if( !isset( $array[$var_name][$array_item] ) )
				return FALSE;

			if ($xss_clean === TRUE)
			{
				return $this->xss_clean($array[$var_name][$array_item]);
			}
	
			return $array[$var_name][$array_item];	
	
		}else{
			if ( ! isset($array[$index]))
			{
				return FALSE;
			}
	
			if ($xss_clean === TRUE)
			{
				return $this->xss_clean($array[$index]);
			}
	
			return $array[$index];	
		}
	}

}

?>