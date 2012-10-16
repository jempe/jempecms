<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Jempe
 *
 * An open source application CMS derived from Codeigniter php framework
 *
 * @package		Jempe
 * @author		Sucio Kastro
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link		http://jempe.org
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Jempe Lang Library
 *
 * Class for multilanguage support
 *
 * @package		Jempe
 * @subpackage	Libraries
 * @category	Lang
 * @author		Sucio Kastro
 * @link		http://jempe.org/documentation/libraries/form.html
 */

class Jempe_lang {

	/**
	* Constructor
	*/

	function __construct()
	{
		$CI =& get_instance();
	}
	
	// --------------------------------------------------------------------
	
	/**
	* Line
	*
	* Get the translation
	*
	* @access	public
	* @param	string	the id
	* @return	string
	*/	
	function line($line, $field_type = 'text', $user = FALSE)
	{
		$CI =& get_instance();

		if($CI->jempe_cms->cache_enabled)
		{
			$cache_key = $CI->jempe_cms->cache_prefix.'jempe_lang_'.md5($line).'_'.$CI->config->item('language');

			if($user > 0)
			{
				$cache_key .= '_'.$user_id;
			}

			if(($cached_line = $CI->cache->get($cache_key)) !== FALSE)
			{
				$line_output = $cached_line;
			}
		}

		if( ! isset($line_output))
		{
			if($user > 0)
			{
				$CI->db->where('(lang_user = '.$user.' OR lang_user IS NULL)', NULL, FALSE);
			}
			else
			{
				$CI->db->where('lang_user', NULL);
			}

			$CI->db->select('lang_id, lang_line_'.$CI->config->item('language').' AS line, lang_type', FALSE);
			$output = $CI->db->get_where('jempe_lang', array('lang_key' => $line));

			if($output->num_rows() > 0)
			{
				$output = $output->row_array();

				if($output['lang_type'] != $field_type)
				{
					$CI->load->library('jempe_db');
					$CI->jempe_db->update_except('jempe_lang', 'lang_id', array('lang_id' => $output['lang_id'], 'lang_type' => $field_type));
				}

				if(strlen($output['line']))
				{
					if($CI->jempe_cms->cache_enabled)
					{
						$CI->cache->save($cache_key, $output['line'], $CI->jempe_cms->cache_time);
			
						$CI->jempe_cms->create_cache_key($output['lang_id'], 'jempe_lang', $cache_key, $CI->jempe_cms->cache_time);
					}
					
					$line_output = $output['line'];
				}
				else
				{
					if($CI->lang->line($line) !== FALSE)
					{
						$line = $CI->lang->line($line);
					}

					$line_output = $line;
				}
			}
			else
			{
				if($user === FALSE)
				{
					$CI->load->library('jempe_db');
					$CI->jempe_db->insert_except('jempe_lang', array('lang_key' => $line, 'lang_type' => $field_type));
				}

				if($CI->lang->line($line) !== FALSE)
				{
					$line = $CI->lang->line($line);
				}

				$line_output = $line;
			}
		}

		if($CI->session->userdata('user_id') > 0)
		{
			$CI->load->library('jempe_admin');

			if($CI->session->userdata('user_type') == 1 OR ($user > 0 && $CI->jempe_admin->user_id() == $user))
			{
				$line_output = str_replace("{page_id}",  $line, str_replace("{field_name}", "jempe_lang".$user, $CI->jempe_cms->edit_box_template["open"].str_replace("{button_name}", $CI->lang->line('jempe_button_edit_field'), $CI->jempe_cms->edit_element_button))).$line_output.$CI->jempe_cms->edit_box_template["close"];
			}
		}

		return $line_output;
	}
}

?>