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
 * Jempe CMS Library
 *
 * CMS functions
 *
 * @package             Jempe
 * @subpackage  Libraries
 * @category    DB
 * @author              Sucio Kastro
 * @link                http://jempe.org/documentation/controllers/admin.html
 */

// ------------------------------------------------------------------------

/**
 * Static URL
 *
 * Returns the "static_url" item from your config file (root url of all jempe static files)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('static_url'))
{
	function static_url()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('static_url');
	}
}

// ------------------------------------------------------------------------

/**
 * Upload Path
 *
 * Returns the "upload_path" item from your config file (system path were files are uploaded)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('upload_path'))
{
	function upload_path()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('upload_path');
	}
}

// ------------------------------------------------------------------------

/**
 * Upload URL
 *
 * Returns the "upload_url" item from your config file (url to get uploaded files)
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('upload_url'))
{
	function upload_url()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('upload_url');
	}
}

class Jempe_cms {

	public $structure = array(array("structure_id" => 0));
	public $user_type = "public";
	public $cms_fields = array
	(
		'url'	=>	array("name" => "url", "type" => "url"),
		'text' 	=>	array("name" => "title", "type" => "text"),
		'text' 	=>	array("name" => "text", "type" => "htmlarea")
	);
	public $template = "";
	public $page_data = "";
	public $default_template = "base.php";
	public $theme = "delejos";
	public $presets = array();
	public $selected_preset = FALSE;
	public $cache_time = 10080;
	public $cache = FALSE; //save pages in cache
	public $image_library = 'GD2';
	public $paginate_keyword = 'paginate';
	public $paginate_uri_segment = 3;
	public $file_manager_folder = "admin/uploads/";
	public $jquery_field_types = array('htmlarea', 'date', 'file');
	public $max_levels = 0;
	public $page_list_condition = FALSE;
	public $jempe_thumb_width = 200;
	public $jempe_thumb_height = 200;
	public $upload_images_config = array(
		'upload_path' => "",
		'allowed_types' => 'gif|jpg|png',
		'max_size' => '5000',
		'max_width' => '4000',
		'max_height' => '4000',
		'max_filename' => '120'
	);
	public $upload_docs_config = array(
		'upload_path' => "",
		'allowed_types' => 'pdf|doc|docx|xls|xlsx|zip',
		'max_size' => '10000',
		'max_width' => '4000',
		'max_height' => '4000',
		'max_filename' => '120'
	);
	public $images_thumbs = array(
		"jempe" => array(
			"type" => "cropped",
			"width" => 50,
			"height" => 50
		)
	);

	public $edit_box_template = array(
		'open'=>'<span class="jempe_edit_box" onmouseover="jempe_show_edit_link($(this))" onmouseout="jempe_edit_timer()" rel="{page_id}" title="{field_name}" >',
		'close'=>'</span>'
	);

	public $edit_element_background_style = "#CCC";
	public $edit_element_button = '<span class="jempe_edit_button" href="javascript:void(0)" onmouseover="jempe_cancel_timer();" onclick="jempe_show_edit_field( $(this).parent())">{button_name}</span>';

	public $upload_files_config = array(
		'jempe_images' => array(
			'upload_path' => "uploads/",
			'allowed_types' => 'gif|jpg|png',
			'max_size' => '5000',
			'max_width' => '4000',
			'max_height' => '4000',
			'max_filename' => '120',
			'onComplete' => 'jempe_show_thumb',
			'require_login' => TRUE,
			'jempe_permission' => 'upload_images'
		)
	);

	public $execute_after_upload = FALSE;
	public $cache_driver = FALSE;
	public $cache_enabled = FALSE;
	public $cache_prefix = FALSE;
	public $additional_cache_keys = FALSE;

	public $date_format = 'm/d/Y';
	public $date_format_js = 'mm/dd/yy';

	public $first_page_is_root = TRUE;

	public $menu_spacer = '<div style="width:100%;height:40px;"></div>';

/**
	* Constructor - Sets Jempe Preferences
	*
	* The constructor can be passed an array of config values
	*/	
	function __construct()
	{		
		$CI =& get_instance();
	
		$jempe_vars = array('cms_fields','default_template','theme','cache_time','cache','paginate_keyword','presets' ,'file_manager_folder','max_levels','page_list_condition', 'execute_after_upload', 'cache_driver', 'additional_cache_keys', 'first_page_is_root', 'cache_prefix', 'tinymce_buttons', 'tinymce_plugins', 'date_format', 'date_format_js');
	
		foreach ($jempe_vars as $var)
		{
			if($CI->config->item('jempe_'.$var) !== FALSE)
			{
				$this->$var = $CI->config->item('jempe_'.$var);
			}
		}

		if( ! $this->cache_prefix)
		{
			if(isset($_SERVER['HTTP_HOST']))
			{
				$this->cache_prefix = md5($_SERVER['HTTP_HOST']);
			}
			else
			{
				$this->cache_prefix = md5(JEMPEPATH);
			}
		}
	
		if($CI->config->item('jempe_images_thumbs'))
		{
			$images_thumbs = $CI->config->item('jempe_images_thumbs');
	
			foreach($images_thumbs as $thumb_name => $thumb_config)
			{
				if($thumb_name != "jempe" && url_title($thumb_name) == $thumb_name)
				{
					$this->images_thumbs[$thumb_name] = $thumb_config;
				}
			}
		}
	
		if($CI->config->item('jempe_upload_files_config'))
		{
			$upload_configs = $CI->config->item('jempe_upload_files_config');
	
			foreach($upload_configs as $config_name => $upload_config)
			{
				$this->upload_files_config[$config_name] = $upload_config;
			}
		}
	
		$fields_list = array();
	
		foreach($this->cms_fields as $cms_field)
		{
			$fields_list[] = $cms_field['name'];
		}
	
		$this->fields_list = $fields_list;
	
		if($this->cache_driver !== FALSE)
		{
			$CI->load->driver('cache', $this->cache_driver);

			$this->cache_enabled = TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Create Structure
	*
	* Create page tree
	*
	* @access	public
	* @return	array
	*/	
	function create_structure()
	{
		$CI =& get_instance();
		$CI->load->database();

		$paginate_segment = "/".$this->paginate_keyword."/";

		if(strpos($CI->input->server('REQUEST_URI'), $paginate_segment) !== FALSE)
		{
			$paginate_uri_parts = explode($paginate_segment, $CI->input->server('REQUEST_URI'));

			$_POST[$this->paginate_keyword] = str_replace("/", "", end($paginate_uri_parts));
		}

		if($this->cache_enabled)
		{
			$cache_key = $this->cache_prefix.'jempe_page_data_'.md5($CI->uri->uri_string).'_'.$CI->config->item('language');

			if(($page_structure = $CI->cache->get($cache_key)) !== FALSE)
			{
				$this->structure = $page_structure;
			}
		}

		$cache_page_ids = array();

		if( ! isset($page_structure) OR $page_structure === FALSE)
		{
			if($CI->uri->total_segments() > 0)
			{
			// check url segments
				for($i = 1; $i <= $CI->uri->total_segments(); $i++)
				{
					if($CI->uri->segment($i) != $this->paginate_keyword)
					{
						$CI->db->select($this->db_field_names($this->fields_list, TRUE).', content_template, content_template_general, content_is_category, structure_id, structure_blocked', FALSE);
						$CI->db->from('jempe_content, jempe_structure, jempe_structure_bind');
						$CI->db->where('content_structure = structure_id'); 
						$CI->db->where('content_structure = sb_structure'); 
						if(isset($this->cms_fields['url']["languages"]))
						{
							$CI->db->where($this->cms_fields['url']["languages"][$CI->config->item('language')], $CI->uri->segment($i)); 
						}
						else
						{
							$CI->db->where('content_url', $CI->uri->segment($i));
						} 
			
						$CI->db->where('sb_parent', $this->structure[$i-1]["structure_id"]); 
						$page = $CI->db->get();
			
						//is there page with that url?
						if($page->num_rows() > 0)
						{
							$page_fields = $page->row_array();

							$cache_page_ids[] = $page_fields['structure_id'];

							$this->structure[$i] = $page_fields;
							$CI->output->set_header("HTTP/1.1 200 OK");
						}
						else
						{
							show_404('', FALSE);
						}
		
					}
					else
					{
						$this->paginate_uri_segment = $i+1;
						$i = $CI->uri->total_segments();
					}
				}
		
			}
			else
			{
				$cache_page_ids[] = 1;
				$this->structure = $this->structure_from_id(1);
				array_unshift($this->structure, array("structure_id" => 0));
			}
		}

		if($this->cache_enabled)
		{
			$CI->cache->save($cache_key, $this->structure, $this->cache_time);

			foreach($cache_page_ids as $cache_page_id)
			{
				$this->create_cache_key($cache_page_id, 'jempe_page', $cache_key, $this->cache_time);
			}
		}
	
		$this->page_data();
	}

	// ------------------------------------------------------------------------

	/**
	* Field Info
	*
	* 
	*
	* @access	public
	* @return	array
	*/
	function field_info($field)
	{	
		if(strpos($field, "content_") == 0)
		{
			$field = str_replace("content_", "", $field);
		}
	
		foreach($this->cms_fields as $cms_field)
		{
			if($cms_field["name"] == $field)
			{
				$field_info = $cms_field;
			}
		}
	
		return $field_info;
	}

	// ------------------------------------------------------------------------

	/**
	* Page Data
	*
	* Check page data
	*
	* @access	public
	* @return	array
	*/	
	function page_data()
	{
		$CI =& get_instance();
	
		//is there a page tree
		if(count($this->structure) > 0)
		{
			//if doesnt have template load default main template
			$this->template = $this->structure[count($this->structure) - 1]["content_template_general"];
			if($this->template == "")
			{
				$this->template = $this->default_template;
			}
	
			//show proper field names
			$this->page_data = $this->field_names($this->structure[count($this->structure) - 1]);
	
			//search page menu
	// 			$this->page_data["menu"] = $this->template_menu($this->template);
	
			if(file_exists(APPPATH."models/".$this->template))
			{
				$this->page_data["model"] = str_replace(".php", "", $this->template);
			}
			
			if(file_exists(APPPATH."models/".$this->page_data["template"]))
			{
				$this->page_data["model_template"] = ucfirst(str_replace(".php", "", $this->page_data["template"]));
			}
	
			//select website theme
			$this->template = $this->theme."/".$this->template;
			$template_interior = $this->page_data["template"];
			$this->page_data["template"] = $this->theme."/".$this->page_data["template"];
	
			//page data
			$jempe_pages = array();
			foreach($this->structure as $structure_data)
			{
				$jempe_pages[] = $this->field_names($structure_data);
			}
	
			$this->page_data["jempe_pages"] = $jempe_pages;
			$data["jempe_pages"] = $jempe_pages;	
	
			//send page data to template
			$data = $this->page_data;
			//if we are in the admin site, show form fields instead of data
			if($this->user_type == "admin" && strlen($CI->uri->segment(4)) && $CI->uri->segment(4) == "edit")
			{
				$data = $this->editable_fields($data);
			}
	
			$this->page_data["jempe_content"] = $data;
		}
	}

	/**
	* Structure from id
	*
	* create page tree with page id
	*
	* @access	public
	* @param	array	campos que se desean
	* @return	array
	*/	
	function structure_from_id($id, $fields = FALSE, $return_level = FALSE)
	{
		$CI =& get_instance();
		$CI->load->database();
	
		if ($fields != FALSE)
		{
			$select_fields = $fields;	
		}
		else
		{
			$select_fields = $this->db_field_names($this->fields_list, TRUE).', content_template, content_template_general, content_is_category, structure_id, structure_name';
		}

		if($this->cache_enabled && $fields === FALSE)
		{
			if($return_level)
			{
				$cache_key = $this->cache_prefix.'st_from_id_'.$id.'_lvl';
			}
			else
			{
				$cache_key = $this->cache_prefix.'st_from_id_'.$id.'_'.$CI->config->item('language');
			}

			if(($st_from_id = $CI->cache->get($cache_key)) !== FALSE)
			{
				return $st_from_id;
			}
		}

		$CI->db->select($select_fields, FALSE);
	
		$path = array();
		// data of first page
		$CI->db->limit(1);
		$CI->db->order_by('structure_id', 'desc');
		$CI->db->from('jempe_content, jempe_structure, jempe_structure_bind');
		$CI->db->where('content_structure = structure_id'); 
		$CI->db->where('content_structure = sb_structure'); 

		if(is_numeric($id))
		{
			$CI->db->where('content_structure', $id);
		}
		else
		{
			$CI->db->where('structure_name', $id);
		}
		$page = $CI->db->get();
	
		// there is no page return false
		if( ! ($page->num_rows() > 0))
		{
			return FALSE;
		}
	
		$page = $page->row_array();
	
		$path[] = $page;
	
		$levels_limit = 10;
		while(isset($page["sb_parent"]) && $page["sb_parent"] > 0 && $levels_limit > 0 )
		{
			$CI->db->select($select_fields, FALSE);
	
			$CI->db->from('jempe_content, jempe_structure, jempe_structure_bind');
			$CI->db->where('content_structure = structure_id'); 
			$CI->db->where('content_structure = sb_structure'); 
			$CI->db->where('content_structure', $page["sb_parent"]); 
			$page = $CI->db->get();
			$page = $page->row_array();
	
			$path[] = $page;
			$levels_limit--;
		}
	
		$path = array_reverse($path);
	
	
		if( ! $return_level)
		{
			if($this->cache_enabled && $fields === FALSE)
			{
				$CI->cache->save($cache_key, $path, $this->cache_time);
			}

			return $path;
		}
		else
		{
			$level = 10 - $levels_limit + 1;

			if($this->cache_enabled && $fields === FALSE)
			{
				$CI->cache->save($cache_key, $level, $this->cache_time);
			}

			return $level;
		}
	}

	/**
	* page link
	*
	* create page link
	*
	* @access	public
	* @param	array	
	* @param	boolean
	* @param	boolean
	* @return	string
	*/	
	function page_link($page_id, $titles = FALSE, $language = FALSE)
	{
		$CI =& get_instance();
		$CI->load->helper('url');

		if($language == FALSE)
		{
			$selected_language = $CI->config->item('language');
		}
		else
		{
			$selected_language = $language;
		}

		if($this->cache_enabled && ! is_array($page_id))
		{
			if($titles)
			{
				$cache_key = $this->cache_prefix.'jempe_page_titles_'.$page_id.'_'.$selected_language;
			}
			else
			{
				$cache_key = $this->cache_prefix.'jempe_page_link_'.$page_id.'_'.$selected_language;
			}

			if(($jempe_page_link = $CI->cache->get($cache_key)) !== FALSE)
			{
				return $jempe_page_link;
			}
		}

		if($page_id == 1 && $titles == FALSE && $this->first_page_is_root)
		{
			return site_url();
		}

		// create tree from id or pages id array?
		if( ! is_array($page_id))
		{
			$page_structure = $this->structure_from_id($page_id, $this->db_field_names(array('url', 'title')).', structure_id, sb_parent, structure_name');
		}
		else
		{
			$link = '';
			for($i = 0; $i < count($page_id); $i++)
			{
				if($link != '')
				{
					$link .= '/';
				}

				if($language == FALSE)
				{
					$link .= $this->clean_tags($page_id[$i]["url"]);
				}
				else
				{
					if(isset($this->cms_fields['url']['languages'][$language]))
					{
						$lang_field = $this->cms_fields['url']['languages'][$language];
						$CI->db->select($lang_field);
						$url_part = $CI->db->get_where('jempe_content', array('content_id' => $page_id[$i]["structure_id"]));
						$url_part = $url_part->row_array();
						$link .= $url_part[$lang_field];
					}
				}
			}

			return site_url($link);
		}

		if($titles)
		{
			$link = "";
			foreach($page_structure as $page)
			{
				$page = $this->field_names($page);

				if($link != "")
				{
					$link.=" | ";
				}

				$link.= $page["title"];
			}

			if($this->cache_enabled && ! is_array($page_id))
			{
				$CI->cache->save($cache_key, $link, $this->cache_time);
			}

			return $link;
		}
		else
		{
			// process url
			$link = '';
			foreach($page_structure as $page)
			{
				$page = $this->field_names($page);
				if($link != '')
				{
					$link.="/";
				}
				$link.= $page["url"];
			}
		}

		$page_link =  site_url($this->clean_tags($link));

		if($this->cache_enabled && ! is_array($page_id))
		{
			$CI->cache->save($cache_key, $page_link, $this->cache_time);
		}
	
		return $page_link;
	}

	// ------------------------------------------------------------------------

	/**
	* Child pages
	*
	* find all child pages
	*
	* @access	public
	* @param	integer	
	* @param	array
	* @param	integer	
	* @param	integer	
	* @param	array	
	* @return	array
	*/
	function child_pages($page_id, $fields = array("url", "title", "link_name"), $from = 0, $limit = 10, $order = array("sb_order" => "asc"), $filter = FALSE)
	{
		$CI =& get_instance();

		if($filter !== FALSE)
		{
			foreach($filter as $filter_field => $filter_value)
			{
				if(is_numeric($filter_field))
				{
					$CI->db->where($filter_value, NULL, FALSE);
				}
				else
				{
					$CI->db->where($filter_field, $filter_value);
				}
			}
		}
	
		foreach($order as $order_field => $direction)
		{
			$CI->db->order_by($order_field, $direction);
		}

		$CI->db->group_by('structure_id');
		$CI->db->limit($limit, $from);
		$CI->db->select('structure_id, structure_name, structure_user, sb_parent, content_template,  '.$this->db_field_names($fields), FALSE);
		$CI->db->from('jempe_structure_bind');
		$CI->db->from('jempe_structure');

		if($page_id !== 'all')
		{

			$CI->db->where('sb_parent', $page_id);
		}

		$CI->db->where('sb_structure = structure_id');
		$CI->db->where('structure_id = content_id');
		$pages = $CI->db->get('jempe_content');
	
		$child_pages = array();
	
		if($pages->num_rows() > 0)
		{
			foreach($pages->result_array() as $page)
			{	
				$page = $CI->jempe_cms->field_names($page);
		
				$child_pages[] = $page;
			}
		}
	
		return $child_pages;
	}

	// ------------------------------------------------------------------------

	/**
	* Page Fields
	*
	* Show the selected fields of a page
	*
	* @access	public
	* @param	integer	
	* @param	array	
	* @return	array
	*/
	function page_fields($structure_id, $fields = array("url", "title", "link_name"))
	{
		$page = $this->child_pages('all', $fields, 0, 1, array("sb_order" => "asc"), array('structure_id' => $structure_id));

		return $page[0];
	}

	// ------------------------------------------------------------------------

	/**
	* db field names
	*
	* prepare column names for query
	*
	* @access	public
	* @param	array	
	* @param	bool
	* @return	string
	*/	
	function db_field_names($fields, $as = FALSE)
	{
		$CI =& get_instance();
		$db_field_names = array();
	
		foreach($this->cms_fields as $field)
		{
			if(in_array($field["name"], $fields))
			{
				if(isset($field["languages"]))
				{
					$db_field_name = $field["languages"][$CI->config->item('language')];
	
					if($as == TRUE)
					{
						$db_field_name .= " AS content_".$field["name"];
					}
				}
				else
				{
					$db_field_name = "content_".$field["name"];
				}
	
				$db_field_names[] = $db_field_name;
			}
		}
	
		return implode(",", $db_field_names);
	}

	// ------------------------------------------------------------------------

	/**
	* field names
	*
	* process db column names to have a proper name
	*
	* @access	public
	* @param	array	
	* @return	array
	*/	
	
	function field_names($results){
		$CI =& get_instance();
		if($CI->session->userdata('user_id'))
		{
			$CI->load->library('jempe_admin');
			$CI->load->library('jempe_form');
		}

		// store the name of the html fields to fix the image url
		$html_fields = array();
	
		if(count($results))
		{
			foreach($this->cms_fields as $field)
			{
				if(isset($field["languages"]))
				{
					if( ! array_key_exists($field["languages"][$CI->config->item('language')], $results) && array_key_exists("content_".$field["name"], $results))
					{
						$results[$field["languages"][$CI->config->item('language')]] = $results["content_".$field["name"]];
					}

					$field_key_name = $field["languages"][$CI->config->item('language')];

					if(array_key_exists($field_key_name, $results) && ! isset($results[$field_key_name]))
					{
						$results[$field_key_name] = '';
					}

					if(isset($results[$field_key_name]))
					{
						if($field['type'] == 'htmlarea')
						{
							$html_fields[] = $field_key_name;
						}

						$language_field = $field_key_name;
						$language_field_text = $results[$language_field];

						if($field['type'] == 'htmlarea' && (strpos($language_field_text, 'jempe_url') !== FALSE OR strpos($language_field_text, 'jempe_base_url') !== FALSE))
						{
							$language_field_text = $this->process_jempe_urls($language_field_text);
						}

						if($field['type'] == 'image')
						{
							$results["content_".$field["name"]] = $this->thumbs_info($language_field_text);
						}
						else
						{
							if($CI->session->userdata("user_id") && $CI->uri->segment(1) != "admin" && $CI->jempe_admin->page_permission($results['structure_id'], $CI->session->userdata('user_id')))
							{
								if(in_array($field['type'], $this->jquery_field_types))
								{
									$CI->jempe_form->create_form_field($field['type'], $language_field, $results, FALSE, FALSE);
								}

								$results["content_".$field["name"]] = str_replace("{page_id}",  $results["structure_id"], str_replace("{field_name}", $field["languages"][$CI->config->item('language')], $this->edit_box_template["open"].str_replace("{button_name}", $CI->lang->line('jempe_button_edit_field'), $this->edit_element_button))).$language_field_text.$this->edit_box_template["close"];
							}
							else
							{
								$results["content_".$field["name"]] = $language_field_text;
							}
						}
					}
				}
				else
				{
					$field_key_name = "content_".$field["name"];

					if(array_key_exists($field_key_name, $results) && ! isset($results[$field_key_name]))
					{
						$results[$field_key_name] = '';
					}

					if(isset($results[$field_key_name]))
					{
						if($field['type'] == 'htmlarea')
						{
							$html_fields[] = $field_key_name;
						}

						$field_text = $results[$field_key_name];
	
						if($field['type'] == 'htmlarea' && (strpos($field_text, 'jempe_url') !== FALSE OR strpos($field_text, 'jempe_base_url') !== FALSE))
						{
							$field_text = $this->process_jempe_urls($field_text);
						}
		
						// if it is an image create an array with image thumbs urls
						if($field['type'] == 'image')
						{
							$results[$field_key_name] = $this->thumbs_info($field_text);
		
						}
						else
						{
							if($CI->session->userdata("user_id") && isset($results[$field_key_name]) && $CI->uri->segment(1) != "admin" && $CI->jempe_admin->page_permission($results['structure_id'], $CI->session->userdata('user_id')))
							{
								if(in_array($field['type'], $this->jquery_field_types))
								{
									$CI->jempe_form->create_form_field($field['type'], $field_key_name, $results, FALSE, FALSE);
								}

								$results[$field_key_name] = str_replace("{page_id}", $results["structure_id"], str_replace("{field_name}", $field_key_name, $this->edit_box_template["open"].str_replace("{button_name}", $CI->lang->line('jempe_button_edit_field'), $this->edit_element_button))).$field_text.$this->edit_box_template["close"];
							}
							else
							{
								$results[$field_key_name] = $field_text;
							}
						}
					}
				}
			}
	
			// change field names
			foreach($results as $key => $value)
			{
				$key_name = str_replace("content_", "", $key);

				if(in_array($key, $html_fields))
				{
					$value = $this->process_image_urls($value);
				}
	
				$fields[$key_name] = $value;
			}

			if(isset($fields))
			{
				return $fields;
			}
		}
	}

	// ------------------------------------------------------------------------
	
	/**
	* Convert jempe urls to absolute urls
	*
	* @access	public
	* @param	string	
	* @return	string
	*/
	function process_jempe_urls($text)
	{
		$text = str_replace('jempe_base_url', base_url(), $text);

		$page_ids = $jempe_urls = $absolut_urls = array();

		$text_parts = explode('jempe_url', $text);

		foreach($text_parts as $part)
		{
			$url_parts = explode('"', $part);

			if($url_parts[0] > 0)
			{
				$page_ids[] = $url_parts[0];
			}
		}

		krsort($page_ids);

		if(count($page_ids))
		{
			foreach($page_ids as $page_id)
			{
				$jempe_urls[] = 'jempe_url'.$page_id;
				$absolut_urls[] = $this->page_link($page_id);
			}
		}

		return str_replace($jempe_urls, $absolut_urls, $text);
	}

	// ------------------------------------------------------------------------
	
	/**
	* Convert image urls to absolute urls
	*
	* @access	public
	* @param	string	
	* @return	string
	*/
	function process_image_urls($text)
	{
		$CI =& get_instance();
		$tags_to_check = array(
			'img' 	=> 	'src',
			'a' 	=>	'href'
		);

		require_once(APPPATH.'libraries/phpQuery.php');

		$doc = phpQuery::newDocumentHTML($text);

		foreach($tags_to_check as $tag => $attribute)
		{
			if(pq($tag)->length > 0)
			{
				for($i = 0; $i < pq($tag)->length; $i++)
				{
					$attr_value = pq($tag)->eq($i)->attr($attribute);

					if( ! (strpos($attr_value, "/") === 0 OR strpos($attr_value, "://") !== FALSE))
					{
						pq($tag)->eq($i)->attr($attribute, $CI->config->item('base_url').$attr_value);
					}
				}
			}
		}

		return $doc->htmlOuter();
	}

	// ------------------------------------------------------------------------
 
	/**
	* thumbs info
	*
	* create an array with thumbs data
	*
	* @access	public
	* @param	int	
	* @return	array
	*/	
	function thumbs_info($image_id)
	{
		if( ! ($image_id > 0 ))
		{
			return FALSE;
		}

		$CI =& get_instance();
		$CI->load->database();

		$image_info = $CI->db->get_where('jempe_images', array('image_id' => $image_id));
		$image_info = $image_info->row_array();

		if($image_info["image_type"] == "file")
		{
			return array();
		}

		$thumb_types = array_keys($this->images_thumbs);
		$thumb_types[] = 'original';

		$thumb_select = "*, CONCAT(thumb_url, thumb_path) AS url, thumb_width AS width, thumb_height AS height";

		$CI->db->select($thumb_select, FALSE);

		$image_extension = $image_info['image_extension'];

		if($image_extension == "jpg" OR $image_extension == "gif" OR $image_extension == "png")
		{
			$is_image = TRUE;
		}
		else
		{
			$is_image = FALSE;
		}

		if($is_image)
		{
			$CI->db->where_in('thumb_type', $thumb_types);
		}
		else
		{
			$CI->db->where('thumb_type', 'original');
		}

		$thumbs = $CI->db->get_where('jempe_thumbs', array('thumb_image' => $image_id));

		if(($is_image && $thumbs->num_rows() == count($thumb_types)) OR ( ! $is_image && $thumbs->num_rows() == 1))
		{
			$thumbs = $thumbs->result_array();
		}
		else
		{
			$sizes = $this->images_thumbs;
			$config = $this->upload_images_config;
		
			foreach($sizes as $name => $info)
			{
				if( ! file_exists(upload_path().$config["upload_path"]."thumbs/".$name."/".$image_info["image_file"]))
				{
					$CI->load->library('jempe_form');
					$CI->jempe_form->process_thumb($image_id, $name);
				}
			}

			if($is_image)
			{
				$CI->db->where_in('thumb_type', $thumb_types);
			}
			else
			{
				$CI->db->where('thumb_type', 'original');
			}

			$CI->db->select($thumb_select, FALSE);
			$thumbs = $CI->db->get_where('jempe_thumbs', array('thumb_image' => $image_id));

			$thumbs = $thumbs->result_array();
		}

		$output = array();

		foreach($thumbs as $thumb)
		{
			$output[$thumb['thumb_type']] = $thumb;
		}
	
		return $output;
	}

/**
 * editable fields
 *
 * convert fields to editable fields
 *
 * @access	public
 * @param	array	
 * @return	array
 */	

function editable_fields($results){
	$CI =& get_instance();
	$CI->load->library('jempe_form');
		

		foreach($results as $campo=>$valor){

			foreach($this->cms_fields as $cms_field){
				//check field type
				if($campo == $cms_field["name"]){

					$dato_campo["content_" .$campo] = $valor;

					if($cms_field["type"] == "text")
						$campo_form = $CI->jempe_form->form_input("content_" .$campo , $valor  ); 

					if($cms_field["type"] == "htmlarea")
						$campo_form = $CI->jempe_form->form_htmlarea("content_" .$campo , $valor  ); 

					if($cms_field["type"] == "area")
						$campo_form = $CI->jempe_form->form_textarea("content_" .$campo , $valor  );

					if(isset($campo_form)){
					$fields[$campo]='<form method="post" action="' .base_url() .'admin/page_edit" >'
					.$campo_form
					.'<input type="hidden" name="structure_id" value="' .$CI->uri->segment(3) .'">
					<input type="submit" value="' .$CI->lang->line('jempe_button_edit_field') .'">	
					</form>';
					}
				}
				
			}

			if(!isset($fields[$campo]))
				$fields[$campo] = $valor;

		}

		return $fields;

}

/**
 * template menu
 *
 * template menu
 *
 * @access	public
 * @param	string	
 * @return	array
 */	

function template_menu($template){
	$CI =& get_instance();
	$CI->load->database();

	$CI->db->from('jempe_menus');
	$CI->db->where('menu_template',$template ); 
	$menus = $CI->db->get();	

	foreach($menus->result_array() as $menu){

		$CI->db->from('jempe_menu_bind , jempe_content , jempe_structure');
		$CI->db->where('mb_page = content_structure');
		$CI->db->where('structure_id = content_structure');
		$CI->db->where('mb_menu',$menu["menu_id"] ); 
		$CI->db->order_by('mb_order','desc');
		$botones = $CI->db->get();

		foreach($botones->result_array() as $boton){
			$boton["link"] = $this->page_link( $boton["structure_id"] );
			$datos_botones[] = $this->field_names($boton);

		}
		$result["menu_" .$menu["menu_name"] ] = $datos_botones;
		unset($datos_botones);

	}
	if(isset($result))
		return $result;

}

/**
 * field preset
 *
 * is there a preset value for that field
 *
 * @access	public
 * @param	string	
 * @return	string
 */	

function field_preset($field){
	$CI =& get_instance();
	$CI->load->database();

	if($this->selected_preset){
		if(isset($this->presets[$this->selected_preset][$field]))
			return $this->presets[$this->selected_preset][$field];
		else
			return false;
	}else
		return false;

}

/**
 * pages tree 
 *
 * complete site tree
 *
 * @access	public
 * @param	string	
 * @return	string
 */

function pages_tree( $parent ){
	$CI =& get_instance();
	$CI->load->database();

	$tree = array();

	$CI->db->select('structure_id');
	$CI->db->where('sb_parent',$parent);
	$CI->db->where('structure_id = content_id');
	$CI->db->where('structure_id = sb_structure');
	$CI->db->where('content_is_category = 1');
	$CI->db->from('jempe_structure_bind');
	$CI->db->from('jempe_structure');
	$children = $CI->db->get('jempe_content');

	if( $children->num_rows() > 0 ) {

		foreach( $children->result_array() as $child ){
			if( $this->has_children($child["structure_id"]) ){
				if($parent > 0 )
					$tree[$child["structure_id"]] = $this->pages_tree( $child["structure_id"] );
				else
					$tree[$parent][$child["structure_id"]] = $this->pages_tree( $child["structure_id"] );
			}else{	
				if($parent > 0 )
					$tree[$child["structure_id"]] = $child["structure_id"];
				else
					$tree[$parent][$child["structure_id"]] = $child["structure_id"];
			}
		}

	}else{
		$tree = $parent;
	}

	if( $tree === 0 )
		return array(0); 

	return $tree;
}

	/**
	* has children 
	*
	* check if a page has children
	*
	* @access	public
	* @param	string	
	* @return	string
	*/
	function has_children($page)
	{
		$CI =& get_instance();
		$write_db = $CI->load->database('write', TRUE);

		if($this->cache_enabled)
		{
			$cache_key = $this->cache_prefix.'jempe_has_children_'.$page;

			if(($has_children = $CI->cache->get($cache_key)) !== FALSE)
			{
				return (bool) $has_children;
			}
		}
	
		$write_db->limit(1);
		$write_db->select('structure_id');
		$write_db->where('sb_parent', $page);
		$write_db->where('structure_id = content_id');
		$write_db->where('structure_id = sb_structure');
		$write_db->from('jempe_structure_bind');
		$write_db->from('jempe_structure');
		$children = $write_db->get('jempe_content');
	
		if($children->num_rows() > 0)
		{
			if($this->cache_enabled)
			{
				$CI->cache->save($cache_key, 1, $this->cache_time);
			}

			return TRUE;
		}
		else
		{
			if($this->cache_enabled)
			{
				$CI->cache->save($cache_key, 0, $this->cache_time);
			}

			return FALSE;
		}
	}

	/**
	* is parent 
	*
	* Check if a page is parent of another, It helps to prevent infinite loops when creating a new page
	*
	* @access	public
	* @param	int
	* @param	int
	* @param	bool	
	* @return	string
	*/
	function is_parent($child_page, $parent_page, $is_parent = FALSE)
	{
		if($is_parent)
		{
			return $is_parent;
		}
	
		$CI =& get_instance();
		$write_db = $CI->load->database('write', TRUE);
	
		if($this->cache_enabled)
		{
			$cache_key = $this->cache_prefix.'jempe_is_parent_'.$child_page.'_'.$parent_page;

			if(($has_parent = $CI->cache->get($cache_key)) !== FALSE)
			{
				return (bool) $has_parent;
			}
		}
	
		$write_db->select('sb_parent');
		$write_db->where('structure_id', $child_page);
		$write_db->where('structure_id = content_id');
		$write_db->where('structure_id = sb_structure');
		$write_db->from('jempe_structure_bind');
		$write_db->from('jempe_structure');
		$parents = $write_db->get('jempe_content');
	
		if($parents->num_rows() > 0)
		{
			foreach($parents->result_array() as $parent)
			{
				if($parent["sb_parent"] == $parent_page)
				{
					$is_parent = TRUE;
				}
	
				if($this->has_parent($parent["sb_parent"]))
				{
					$is_parent = $this->is_parent($parent["sb_parent"], $parent_page, $is_parent);
				}
			}
		}

		if($this->cache_enabled)
		{
			if($is_parent)
			{
				$cache_parent = 1;
			}
			else
			{
				$cache_parent = 0;
			}

			$CI->cache->save($cache_key, $cache_parent, $this->cache_time);
			$this->create_cache_key($child_page, 'jempe_page', $cache_key, $this->cache_time);
			$this->create_cache_key($parent_page, 'jempe_page', $cache_key, $this->cache_time);
		}
	
		return $is_parent;
	}

	/**
	* has children 
	*
	* check is a page has parent pages
	*
	* @access	public
	* @param	string	
	* @return	string
	*/
	
	function has_parent($page)
	{
		$CI =& get_instance();
		$write_db = $CI->load->database('write', TRUE);

		if($this->cache_enabled)
		{
			$cache_key = $this->cache_prefix.'jempe_has_parent_'.$page;

			if(($has_parent = $CI->cache->get($cache_key)) !== FALSE)
			{
				return (bool) $has_parent;
			}
		}
	
		$write_db->select('structure_id');
		$write_db->where('sb_parent >', 0);
		$write_db->where('structure_id', $page);
		$write_db->where('structure_id = content_id');
		$write_db->where('structure_id = sb_structure');
		$write_db->from('jempe_structure_bind');
		$write_db->from('jempe_structure');
		$children = $write_db->get('jempe_content');

		if($children->num_rows() > 0)
		{
			if($this->cache_enabled)
			{
				$CI->cache->save($cache_key, 1, $this->cache_time);
			}

			return TRUE;
		}
		else
		{
			if($this->cache_enabled)
			{
				$CI->cache->save($cache_key, 0, $this->cache_time);
			}

			return FALSE;
		}
	}

	// ------------------------------------------------------------------------

	/**
	* unique_url
	*
	* create a unique url
	*
	* @access	public
	* @param	string	
	* @return	string
	*/
	function unique_url($name, $field_name)
	{
		$CI =& get_instance();
		$CI->load->helper('url');
		$CI->load->database();
	
		$url = url_title($name);
	
		$CI->db->where($field_name, $url);	
	
		if($CI->db->count_all_results('jempe_content') == 0)
		{
			return $url;
		}
	
		for($i = 1; $i < 100; $i++)
		{		
			$new_url = $url.$i;
			$CI->db->where($field_name, $new_url);	
			if($CI->db->count_all_results('jempe_content') > 0)
			{
				break;
			}
		}
	
		return $new_url;
	}

	// ------------------------------------------------------------------------
 
	/**
	* Show admin menu when a user is logged in
	*
	* @access	public
	* @param	int	
	* @return	string
	*/
	function show_menu($structure_id)
	{
		$CI =& get_instance();
		if($CI->session->userdata("user_id"))
		{ 
			$CI->load->view('admin/admin_menu', array("id" => $structure_id));
			echo $this->menu_spacer;
		}
	}

	// ------------------------------------------------------------------------
 
	/**
	* Remove tags and edit button code of a field
	*
	* when a jempe field is an HTML tag attribute, it must be cleaned to avoid HTML errors
	*
	* @access	public
	* @param	string
	* @return	string
	*/
	function clean_tags($string)
	{
		$CI =& get_instance();
	
		$string = str_replace(str_replace("{button_name}", $CI->lang->line('jempe_button_edit_field'), $this->edit_element_button), "", $string);
	
		return strip_tags($string);
	}

	// ------------------------------------------------------------------------
 
	/**
	* Insert a cache key in database
	*
	* insert a key to delete proper cache keys after updating a page or another cached item
	*
	* @access	public
	* @param	int	
	* @param	string
	* @param	string
	* @param	int
	* @return	void
	*/
	function create_cache_key($item_id, $type, $name)
	{
		if( ! $this->cache_enabled)
		{
			return false;
		}

		$CI =& get_instance();

		if(is_array($name))
		{
			$key_values = $name;
		}
		else
		{
			if($cache_key_value = $CI->cache->get($type .'_' .$item_id))
			{
				$key_values = unserialize($cache_key_value);

				if( ! in_array($name, $key_values))
				{
					$key_values[] = $name;
				}
			}
			else
			{
				$key_values = array($name);
			}
		}
	
		$CI->cache->save($type .'_' .$item_id, serialize($key_values), $this->cache_time * 100);
	}

	// ------------------------------------------------------------------------
 
	/**
	* Delete cache keys for a specific page
	*
	* @access	public
	* @param	int	
	* @param	string
	* @return	void
	*/
	function delete_cache_keys($item_id, $type)
	{
		if( ! $this->cache_enabled)
		{
			return false;
		}

		$CI =& get_instance();

		$lang_cache_keys = array(
			$this->cache_prefix.'st_from_id_{id}_lvl',
			$this->cache_prefix.'st_from_id_{id}_{lang}',
			$this->cache_prefix.'jempe_page_titles_{id}_{lang}'.
			$this->cache_prefix.'jempe_page_link_{id}_{lang}',
			$this->cache_prefix.'jempe_has_children_{id}',
			$this->cache_prefix.'jempe_has_parent_{id}'
		);

		if($cache_keys = $CI->cache->get($type .'_' .$item_id))
		{
			$cache_keys = unserialize($cache_keys);

			foreach($cache_keys as $cache_key)
			{
				$CI->cache->delete($cache_key);
			}

			$this->create_cache_key($item_id, $type, $cache_key);
		}
	}

	// ------------------------------------------------------------------------
 
	/**
	* Send Email
	*
	* @access	public
	* @param	mixed	
	* @param	string
	* @param	string
	* @return	bool
	*/
	function send_email($to, $subject, $message, $email_data = array(), $email_config = array())
	{
		$CI =& get_instance();
		$write_db = $CI->load->database('write', TRUE);
		$CI->load->library('jempe_db');

		$CI->load->library('email');

		if(count($email_config))
		{
			$CI->email->initialize($email_config);
		}
		else if($CI->config->item('jempe_email_config') !== FALSE)
		{
			$CI->email->initialize($CI->config->item('jempe_email_config'));
		}

		$email_data['to'] = $to;
		$email_data['subject'] = $subject;
		$email_data['message'] = $message;

		if( ! isset($email_data['from']))
		{
			$email_data['from'] = $CI->config->item('site_from_email');
		}

		if( ! isset($email_data['from_name']))
		{
			$email_data['from_name'] = $CI->config->item('site_from_email_name');
		}

		$email_log = array();

		foreach($email_data as $function_name => $value)
		{
			if($function_name == "from")
			{
				$CI->email->from($value, $email_data['from_name']);
			}
			else if(method_exists($CI->email, $function_name))
			{
				if(is_array($value))
				{
					$log_value = implode(',', $value);
				}
				else
				{
					$log_value = $value;
				}

				$email_log['email_'.$function_name] = $log_value;

				$CI->email->$function_name($value);
			}
		}
		
		$return = $CI->email->send();

		$email_log['email_debug'] = $CI->email->print_debugger();

		$CI->jempe_db->insert_except('jempe_email_log', $email_log);

		return $return;
	}

	// ------------------------------------------------------------------------
 
	/**
	* Get images with a tag
	*
	* @access	public
	* @param	string	
	*/
	function images_with_tag($tag_name, $from = 0, $limit = 10)
	{
		$CI =& get_instance();

		$CI->db->limit($limit, $from);
		$CI->db->select('image_id');
		$CI->db->order_by('image_name', 'ASC');
		$CI->db->where('tag_name', $tag_name);
		$CI->db->where('it_tag = tag_id');
		$CI->db->from('jempe_tags');
		$CI->db->where('it_image = image_id');
		$CI->db->from('jempe_image_tags');
		$images = $CI->db->get_where('jempe_images', array('image_active' => 1));

		$result_images = array();

		if($images->num_rows() > 0)
		{
			foreach($images->result_array() AS $image)
			{
				$result_images[] = $this->thumbs_info($image['image_id']);
			}
		}

		return $result_images;
	}
}

// END Jempe_cms Library

/* End of file jempe_cms.php */
/* Location: ./application/libraries/jempe_cms.php */