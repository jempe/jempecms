<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Jempe
 *
 * An open source application CMS derived from Codeigniter php framework
 *
 * @package             Jempe
 * @author              Sucio Kastro
 * @license             http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link                http://jempe.org
 * @since               Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Jempe DB Library
 *
 * Database functions
 *
 * @package             Jempe
 * @subpackage  Libraries
 * @category    DB
 * @author              Sucio Kastro
 * @link                http://jempe.org/documentation/controllers/admin.html
 */
class Jempe_db {
/**
	* Constructor - Sets Jempe Preferences
	*
	* The constructor can be passed an array of config values
	*/	
	function __construct()
	{		
		$CI =& get_instance();
		$this->write_db = $CI->load->database('write', TRUE);
	}
	// ------------------------------------------------------------------------
	
	/**
		* Merge two arrays
		*
		* @access	public
		* @param	array	first_array
		* @param	array	second array
		* @return	array
		*/
	
	function merge_arrays($array1, $array2){
	
		$results = $array2;
	
		foreach($array1 as $key=>$data){
			$results[$key] = $data;
		}
	
		return $results;
	}

	// ------------------------------------------------------------------------

	/**
	 * Results to list
	 *
	 * Return key/pair array 
	 *
	 * @access	public
	 * @param	array	results
	 * @param	string	index column name
	 * @param	string	data column name
	 * @param	array	initial values of array
	 * @return	array
	 */

	function results_to_list($results, $index, $data, $select = FALSE)
	{
		if($select && is_array($select))
		{
			$list = $select;
		}
		else
		{
			$list = array();
		}
	
		if(count($results))
		{
			foreach($results as $result)
			{
				$list[$result[$index]] = $result[$data];
			}
		}

		return $list;
	}

	// ------------------------------------------------------------------------

	/**
	 * Insert row in table
	 *
	 * Insert all columns except the columns in exclude array
	 *
	 * @access	public
	 * @param	string	table
	 * @param	array	row to insert
	 * @param	array	fields to exclude
	 * @return	int
	 */
	function insert_except($table, $insert, $exclude = array())
	{
		$CI =& get_instance();
	
		$fields = $this->write_db->list_fields($table);
		
		foreach ($fields as $field)
		{
			$table_columns[]["Field"] = $field ;
		}
	
		foreach($table_columns as $column)
		{
			if(isset($insert[$column["Field"]]) && strlen($insert[$column["Field"]]) && (array_search($column["Field"], $exclude) === FALSE))
			{
				$accepted_columns[$column["Field"]] = trim($insert[$column["Field"]]);
			}
		}

		if($CI->db->dbdriver == "postgre")
		{
			$insert_id_field = $this->write_db->query("SELECT column_name, column_default FROM information_schema.columns WHERE table_name ='" .$this->write_db->dbprefix .$table ."' AND column_default LIKE 'nextval%'");
			if($insert_id_field->num_rows() > 0)
			{
				$insert_id_field = $insert_id_field->row_array();
				$next_val = $this->write_db->query("SELECT ".$insert_id_field["column_default"]." AS next_val ");
	
				if($next_val->num_rows() > 0)
				{
					$next_val = $next_val->row_array();
					$accepted_columns[$insert_id_field["column_name"]] = $next_val["next_val"];
				}
			}
			else
			{
				$insert_id = 0;
			}
		}

		if(isset($accepted_columns))
		{
			$this->write_db->insert($table, $accepted_columns);
		}
	
		if(isset($insert_id))
		{
			return $insert_id;
		}
		else
		{
			return $this->write_db->insert_id();
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Update table row
	 *
	 * Update all columns except the columns in exclude array
	 *
	 * @access	public
	 * @param	string	table
	 * @param	string	index column
	 * @param	array	row data
	 * @param	array	columns to exclude
	 * @return	bool
	 */
	function update_except($table, $index, $row, $exclude = array())
	{
		$CI =& get_instance();
	
		//check table columns
		$fields = $this->write_db->list_fields($table);
		
		foreach ($fields as $field)
		{
			$table_columns[]["Field"] = $field;
		}
	
		//check if column can be updated
		foreach($table_columns as $column)
		{
			if(array_key_exists($column["Field"], $row)  && (array_search($column["Field"], $exclude) === FALSE) && $column["Field"] != $index)
			{
				$accepted_columns[$column["Field"]] = $row[$column["Field"]];
			}
		}
	
		// if there is data to update returns TRUE
		if(isset($accepted_columns))
		{
			$row_exists = $this->write_db->get_where($table, array($index => $row[$index]));

			if($row_exists->num_rows() == 1)
			{	
				$this->write_db->where($index, $row[$index]);
				$this->write_db->update($table, $accepted_columns);
	
				return $row[$index];
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete table rows
	 *
	 * @access	public
	 * @param	string	table
	 * @param	array	filter
	 * @return	void
	 */
	function delete($table, $filter){
	
		$CI =& get_instance();
	
		$this->write_db->delete($table, $filter);
	}

	// ------------------------------------------------------------------------

	/**
	 * Return default values of a table
	 *
	 * @access	public
	 * @param	string	table
	 * @return	array
	 */
	function blank_fields($table)
	{
		$CI =& get_instance();
	
		if($CI->db->dbdriver == "postgre")
		{
			$columns = $this->write_db->query("SELECT column_name, column_default FROM information_schema.columns WHERE table_name = '".$CI->db->dbprefix.$table."'");
			$field_name = 'column_name';
			$default = 'column_default';
		}
		else
		{
			$columns = $this->write_db->query("SHOW COLUMNS FROM ".$CI->db->dbprefix.$table);
			$field_name = 'Field';
			$default = 'Default';
		}

		$default_values = $columns->result_array();

		if(count($default_values))
		{
			foreach($default_values as $default_value)
			{
				$blank_fields[$default_value[$field_name]] = $default_value[$default];
			}
		}
		else
		{
			$blank_fields = array();
		}
	
		return $blank_fields;
	}

	// ------------------------------------------------------------------------

	/**
	 * Update table columns
	 *
	 * @access	public
	 * @param	string	table
	 * @param	array	columns
	 * @param	mixed	keys
	 * @return	void
	 */
	function update_table($table, $fields, $keys)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->dbforge();
	
		if($CI->db->dbdriver == "postgre")
		{
			foreach(array_keys($fields) as $column_name)
			{
				if($fields[$column_name]["type"] == "INT")
				{
					if(isset($fields[$column_name]["auto_increment"]) && $fields[$column_name]["auto_increment"] === TRUE)
					{
						$fields[$column_name]["type"] = "SERIAL";
					}
	
					unset( $fields[$column_name]["constraint"]);
					unset( $fields[$column_name]["unsigned"]);
					unset( $fields[$column_name]["auto_increment"]);
				}

				if($fields[$column_name]["type"] == "TIMESTAMP")
				{
					$fields[$column_name]["null"] = TRUE;
					$fields[$column_name]["default"] = "current_timestamp";
				}
			}
	
			if(is_array($keys))
			{
				foreach($keys as $key_name => $key_value)
				{
					if($key_value === TRUE)
					{
						$keys = $key_name;
					}
				}

				if(is_array($keys))
				{
					$keys = array();
				}
			}
		}
	
		if($CI->db->table_exists($table))
		{
			$columns_data = $CI->db->field_data($table);
	
			$fields_data = array();
	
			foreach($columns_data as $column)
			{
				$fields_data[$column->name] = array(
					'type'		=>	$column->type,
					'max_length'	=>	$column->max_length
				);
			}
	
			foreach($fields as $column_name => $column_data)
			{
				if( ! $CI->db->field_exists($column_name, $table))
				{
					$CI->dbforge->add_column($table, array($column_name => $column_data));
				}
				else
				{
					if($fields_data[$column_name]["type"] == "INT" && $column_data["type"] == "VARCHAR")
					{
						$CI->dbforge->modify_column($table, 
							array(
								$column_name => array(
									'name'		=> 	$column_name,
									'type' 		=>	$column_data["type"],
									'constraint'	=>	$column_data["constraint"]
								) 
							) 
						);
					}
				}
			}
		}
		else
		{
			if(is_array($keys))
			{
				foreach($keys as $key_name => $primary)
				{
					$CI->dbforge->add_key($key_name, $primary);
				}
			}
			else
			{
				$CI->dbforge->add_key($keys, TRUE);
			}
	
			$CI->dbforge->add_field($fields);
			$CI->dbforge->create_table($table);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * is unique record
	 *
	 * @access	public
	 * @param	string	table
	 * @param	array	columns
	 * @param	mixed	keys
	 * @return	void
	 */
	function is_unique($name, $table, $field, $filter = '', $not_equal_field = '', $not_equal_value = '')
	{
		$CI =& get_instance();

		if($not_equal_value > 0 && strlen($not_equal_field))
		{
			$CI->db->where($not_equal_field." !=", $not_equal_value);
		}

		if(isset($filter) && strlen($filter))
		{
			$CI->db->where($filter, NULL, FALSE);
		}

		$unique = $CI->db->get_where($table, array($field => $name));

		if($unique->num_rows() > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}

// END Jempe_DB class

/* End of file jempe_db.php */
/* Location: ./application/libraries/jempe_db.php */