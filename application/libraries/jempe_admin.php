<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Jempe Admin Library
 *
 * Administration functions
 *
 * @package             Jempe
 * @subpackage  Libraries
 * @category    Admin
 * @author              Sucio Kastro
 * @link                http://code.google.com/p/jempe/wiki/JempeAdminClass
 */

class Jempe_admin {

	// These are the templates for the Jempe Admin Menu
	public $admin_menu_link = '<a href="%link%" style="float:left; padding:0; margin:0; font-size:11px; color:white; text-decoration:none; font-family:Arial,helvetica,sans-serif; line-height:20px; %selected% " %popup% ><br />%name%</a>';
	public $admin_menu_selected_link = '<a href="%link%" style="float:left; background:url(%url%images/selected_menu.png) center 13px no-repeat; padding:0; margin:0; font-size:11px; color:white; text-decoration:none; font-family:Arial,helvetica,sans-serif; line-height:20px; %selected% "  %popup%  ><br />%name%</a>';
	public $admin_menu_spacer = '<div style="background:black; float:left; height:24px; width:12px; ">&nbsp;</div>';

	// These are the different values for permission types
	public $permission_types = array('no' => 0, 'read' => 1, 'write' => 2);

	// Folder name of jempe admin
	public $admin_url = 'admin';

	// User fields to store in session
	public $user_session_fields = 'user_username, user_id, user_type';

	public $theme = 'admin';

	public $allow_multiple_logins = TRUE;

	/**
	* Authenticate
	*
	* Authenticate User
	*
	* @access       public
	* @return       bool
	*/
	function authenticate($username, $password, $user_id = FALSE)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('session');

		if( ! $this->allow_multiple_logins)
		{
			$CI->load->library('jempe_db');
			$CI->db->where('session_id', NULL);
			$CI->db->join('jempe_sessions', 'session_id = user_session_id AND last_activity < '.($CI->session->_get_time() + $CI->session->sess_time_to_update), 'left');
		}

		$CI->db->select($this->user_session_fields);

		if($user_id > 0)
		{
			$CI->db->where('user_id', $user_id);
		}
		else
		{
			$CI->db->where('user_password', md5(trim($password)));
			$CI->db->where('user_username', trim($username));
		}

		$user = $CI->db->get_where('jempe_users', array('user_active' => 1));

		if($user->num_rows() > 0)
		{
			$user_data = $user->row_array();

			$CI->session->set_userdata($user_data);
			
			if( ! $this->allow_multiple_logins)
			{
				$CI->jempe_db->update_except('jempe_users', 'user_id', array('user_id' => $user_data['user_id'], 'user_session_id' => $CI->session->userdata('session_id')));
			}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
	}

	// --------------------------------------------------------------------
	
	/**
	* Logout
	*
	* Remove session data to logout
	*
	* @access       public
	* @return       void
	*/
	function logout()
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('session');
		
		$session_fields = explode(',', $this->user_session_fields);

		foreach($session_fields as $session_field)
		{
			$CI->session->unset_userdata(trim($session_field));
		}
	}

	// --------------------------------------------------------------------

	/**
	* Is Authenticated
	*
	* 
	*
	* @access       public
	* @return       bool
	*/      
	function is_authenticated()
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('session');

		if(strlen($CI->session->userdata('user_id')))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Get User id of user
	*
	* @access       public
	* @param        int
	* @return       bool
	*/      
	function user_id()
	{
		$CI =& get_instance();
		$CI->load->library('session');

		return $CI->session->userdata('user_id');
	}

	// --------------------------------------------------------------------

	/**
	* User Data
	*
	* Return user data
	*
	* @access       public
	* @param        int
	* @return       bool
	*/      
	function user_data($user_id = FALSE)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('session');

		if($user_id === FALSE)
		{
			$user_id = $this->user_id();
		}

		$user_data = $CI->db->get_where('jempe_users', array('user_id' => $user_id));
	
		if($user_data->num_rows() > 0)
		{
			return $user_data->row_array();
		}
		else
		{
			return FALSE;
		}
	}

        // --------------------------------------------------------------------

        /**
        * Show a list of users
        *
        * @access       public
        * @param        int show from row #
        * @param        mixed limit of rows
        * @param        array key/pair of table columns and values
        * @param        array key/pair of table columns direction (asc/desc)
        * @return       array
        */      
        function users($from = 0, $limit = 'all', $filter = array(), $sort = array(), $select = '*')
        {
                $CI =& get_instance();
                $CI->load->database();

		$CI->db->select($select, FALSE);

		if($limit != 'all')
		{
			$CI->db->limit($limit, $from);
		}

		if( ! array_key_exists('user_active', $filter))
		{
			$filter['user_active'] = 1;
		}

		foreach($filter AS $column => $value)
		{
			$CI->db->where($column, $value);
		}

		foreach($sort AS $column => $direction)
		{
			$CI->db->order_by($column, $direction);
		}

		$users = $CI->db->get('jempe_users');

		return $users->result_array();
        }

	// --------------------------------------------------------------------

	/**
	* Edit User
	*
	* Edit user or insert a user if user_id is not provided
	*
	* @access       public
	* @param        array
	* @return       bool
	*/      
	function edit_user($user_data, $exclude_fields = array())
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_db');
        
		if(isset($user_data['user_id']) && $user_data['user_id'] > 0)
		{
			if(isset($user_data['user_created']))
			{
				unset($user_data['user_created']);
			}

			return $CI->jempe_db->update_except('jempe_users', 'user_id', $user_data, $exclude_fields);
		}
		else
		{
			$user_data['user_created'] = date('Y-m-d H:i:s');
			return $CI->jempe_db->insert_except('jempe_users', $user_data, $exclude_fields);
		}
	}

	// --------------------------------------------------------------------

	/**
	* Reset Password
	*
	* Send Email to reset user password
	*
	* @access       public
	* @param        string
	* @param        string
	* @return       bool
	*/      
	function reset_password($username, $subject, $message, $url)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_db');
		$CI->load->library('form_validation');
        
		$CI->db->select('user_id, user_email, user_username');
		$username_exists = $CI->db->get_where('jempe_users', array('user_username' => $username));

		if($username_exists->num_rows() > 0)
		{
			$username_exists = $username_exists->row_array();

			if($CI->form_validation->valid_email($username_exists['user_username']))
			{
				$user_email = $username_exists['user_username'];
			}
			else
			{
				$user_email = $username_exists['user_email'];
			}

			$user_data = $this->user_data($username_exists['user_id']);

			$search = $replace = array();

			foreach($user_data AS $user_field => $value)
			{
				$search[] = '{'.$user_field.'}';
				$replace[] = $value;
			}

			$reset_key = $username_exists['user_id'].'.'.uniqid();

			$search[] = '{url}';
			$replace[] = $url.'/'.$reset_key;

			$CI->jempe_cms->send_email($user_email, str_replace($search, $replace, $subject), str_replace($search, $replace, $message));

			$this->edit_user(array(
				'user_id' => $user_data['user_id'],
				'user_reset_key' => $reset_key,
				'user_reset_expiration' => date('Y-m-d H:i:s', time() + (60 * 60 * 24))
			));

			return $user_email;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Can reset password
	*
	* Check if password can be reseted with provided key
	*
	* @access       public
	* @param        string
	* @return       bool
	*/      
	function can_reset_password($reset_key)
	{
		if(strlen(trim($reset_key)) == 0)
		{
			return FALSE;
		}

		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_db');

		$CI->db->select('user_id');
		$CI->db->where('user_reset_expiration > NOW()', NULL, FALSE);
		$username_exists = $CI->db->get_where('jempe_users', array('user_reset_key' => $reset_key));

		if($username_exists->num_rows() == 1)
		{
			$username_exists = $username_exists->row_array();
			return $username_exists['user_id'];
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Admin url
	*
	* create a url for the admin site
	*
	* @access       public
	* @param        string
	* @return       bool
	*/      
	function admin_url($url)
	{
			return site_url($this->admin_url.'/'.$url);
	}

	// --------------------------------------------------------------------
	
	/**
	* Page List
	*
	* return the list of categories of the site
	*
	* @access       public
	* @param        string
	* @return       bool
	*/
	function page_list($self=false , $max_levels=0)
	{
                $CI =& get_instance();
                $CI->load->database();
                $CI->load->library('jempe_cms');
                $CI->load->library('jempe_db');
                $CI->load->helper('url');

                if($CI->jempe_cms->page_list_condition !== FALSE){
                        $CI->db->where($CI->jempe_cms->page_list_condition);
                }

                $CI->db->select('structure_id,'.$CI->jempe_cms->db_field_names(array('title'), TRUE), FALSE);
                $CI->db->from('jempe_content, jempe_structure, jempe_structure_bind');
                $CI->db->where('content_is_category', 1); 
                $CI->db->where('content_structure = structure_id'); 
                $CI->db->where('content_structure = sb_structure'); 
                $pages = $CI->db->get();
                
                if($this->page_permission(0, $CI->session->userdata('user_id'), 'insert'))
                {
                        $lista[0]['sb_parent'] = 0;
                        $lista[0]['link'] = $CI->lang->line('jempe_category_general');
                }

                foreach($pages->result_array() as $page)
                {
                        if($self != $page['structure_id'] && ($max_levels == 0 OR $CI->jempe_cms->structure_from_id($page['structure_id'], 'structure_id', TRUE) < $max_levels) && $this->page_permission($page['structure_id'], $CI->session->userdata('user_id'), 'insert'))
                        {
                                $page['sb_parent'] = $page['structure_id'];
                                $page['link'] = $CI->jempe_cms->page_link($page['structure_id'], TRUE);

                                if(($self > 0 && ! $CI->jempe_cms->is_parent($page['structure_id'], $self)) OR $self === FALSE)
                                {
                                        $lista[] = $page;
                                }
                        }
                }
        
                if(isset($lista) && count($lista) > 0)
                {
                        $page_list = $CI->jempe_db->results_to_list($lista, 'sb_parent', 'link');
                        asort($page_list);

                        return  $page_list;
                }
                else
                {
                        return array();
                }
        
        
        }
        // TODO Fix syntax of the rest of the library

        // --------------------------------------------------------------------
        
        /**
        * User permission
        *
        * Check if user can edit or view a page
        *
        * @access       public
        * @return       bool
        */      
        function user_permission($permission, $permission_type = FALSE, $user_type = FALSE)
        {
                $CI =& get_instance();
        
                if($user_type === FALSE)
                {
                        $user_type = $CI->session->userdata('user_type');
                }
        
                if($user_type == 1){
                        if($permission_type === FALSE)
                        {
                                return 2;
                        }
                        else
                        {
                                return TRUE;
                        }
                }
                        
                $CI->db->where('utp_user', $user_type);
                $CI->db->where("permission_name", $permission);
                $CI->db->where('permission_id = utp_permission');
                $CI->db->from('jempe_permissions');
                $user_permission = $CI->db->get('jempe_user_type_permissions');
        
                if($permission_type === FALSE)
                {
                        if( $user_permission->num_rows() > 0 )
                        {
                                $user_permission = $user_permission->row_array();
                                return $user_permission['utp_permission_value'];
                        }
                        else
                        {
                                return 0;
                        }
                }else{
                        $permission_type_number = $this->permission_types[$permission_type];
        
                        if( $user_permission->num_rows() > 0 )
                        {
                                $user_permission = $user_permission->row_array();
        
                                if($user_permission["utp_permission_value"] >= $permission_type_number )
                                        return true;
                                else
                                        return false;
                        }else
                                return false;
                }
                
        }
        
	/**
	* Page permissions
	*
	* check page user permissions
	*
	* @access       public
	* @return       bool
	*/      
	function page_permission($page_id, $user_id, $permission_type = FALSE)
	{
		$CI =& get_instance();
	
		if($CI->session->userdata('user_type') == 1)
		{
			return TRUE;
		}

		if($page_id > 0 OR $page_id === 0 OR $page_id === "0")
		{
			if($permission_type == FALSE)
			{
				$is_user_page = $CI->db->get_where("jempe_structure", array("structure_id" => $page_id, "structure_user" => $user_id));

				if($is_user_page->num_rows() > 0)
				{
					$CI->db->where("utp_permission", 5);
					$CI->db->where("utp_user = user_type");
					$CI->db->from("jempe_user_type_permissions");
					$can_edit_his_pages = $CI->db->get_where("jempe_users", array("user_id" => $user_id));

					if($can_edit_his_pages->num_rows() > 0)
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}
				}
			}
		}
	
		if($page_id > 0 OR $page_id === 0 OR $page_id === "0")
		{
			$CI->db->select('user_type');
			$CI->db->where('user_id = structure_user');
			$CI->db->from('jempe_users');
			$page_user_type = $CI->db->get_where('jempe_structure', array('structure_id' => $page_id));

			if( $page_user_type->num_rows() > 0)
			{
				$page_user_type = $page_user_type->row_array();
				$same_user_type_query = "OR (utp_permission = 4 AND utp_permission_value = 2 AND user_type = " .$page_user_type["user_type"] .")";
			}
			else
			{
				$same_user_type_query = "";
			}
		}
		else
		{
			$same_user_type_query = "OR (utp_permission = 4 AND utp_permission_value = 2 AND user_type = " .$CI->session->userdata('user_type') .")";
		}


		if($page_id > 0 OR $page_id === 0 OR $page_id === "0")
		{
			$CI->db->where('structure_id', $page_id);
			$CI->db->where('utpp_structure', $page_id);
		}
		else
		{
			$CI->db->from('jempe_content');
			$CI->db->from('jempe_structure_bind');
			$CI->db->where('content_id = structure_id');
			$CI->db->where('sb_structure = structure_id');
			$CI->db->where('content_is_category', 1);
		}

		if($permission_type == "insert")
		{
			$CI->db->where('utpp_new', 1);
		}
		else
		{
			$CI->db->where('utpp_edit',1);
		}

		$CI->db->where('utpp_user_type = user_type');
		$CI->db->from('jempe_user_type_page_permissions');

		$CI->db->select('structure_id');
		$CI->db->where('((utp_permission = 5 AND utp_permission_value = 2 AND ( structure_user = ' .$user_id .' OR structure_user = 0 )) OR (utp_permission = 3 AND utp_permission_value = 2)' .$same_user_type_query .')' , null , false);
		$CI->db->where('user_id' , $user_id );
		$CI->db->where('utp_user = user_type' );
		$CI->db->where('permission_id = utp_permission');
		$CI->db->from('jempe_permissions');
		$CI->db->from('jempe_users');
		$CI->db->from('jempe_structure');
		$user_permission = $CI->db->get('jempe_user_type_permissions');

		if($user_permission->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
        
        /**
        * Update Page permissions
        *
        * change page permissions
        *
        * @access       public
        * @return       bool
        */      
        function update_page_permissions( $page_id  ){
                $CI =& get_instance();
        
                $CI->db->select('user_type_id');
                $CI->db->where('user_type_id > 1');
                $user_types = $CI->db->get_where('jempe_user_types', array('user_type_active'=>1));
        
                if( $user_types->num_rows() > 0 ){
                        foreach( $user_types->result_array() as $user_type ){
        
                                $CI->db->select('utpp_structure , utpp_new , utpp_edit , utpp_recur , content_is_category');
                                $CI->db->where('content_is_category = 1');
                                $CI->db->where('content_id = utpp_structure');
                                $CI->db->from('jempe_content');
                                $page_permissions = $CI->db->get_where('jempe_user_type_page_permissions' , array('utpp_user_type' => $user_type["user_type_id"] , 'utpp_structure' => $page_id ) );
        
                                if( $page_permissions->num_rows() == 0 ){
                                
                                        $CI->db->select_sum('utpp_new');
                                        $CI->db->select_sum('utpp_edit');       
                                        $CI->db->select_sum('utpp_recur');
                                        $CI->db->group_by('utpp_user_type');
                                        $CI->db->where('utpp_structure = sb_parent');
                                        $CI->db->where('utpp_user_type' , $user_type["user_type_id"] );
                                        $CI->db->from('jempe_user_type_page_permissions');
                                        $parent_permissions = $CI->db->get_where('jempe_structure_bind' , array('sb_structure'=>$page_id) );
        
                                        if( $parent_permissions->num_rows() > 0 ){
                                                $parent_permissions = $parent_permissions->row_array();
        
                                                $CI->db->delete('jempe_user_type_page_permissions' , array('utpp_user_type'=>$user_type["user_type_id"] , 'utpp_structure' => $page_id ) );
        
        
                                                if( $parent_permissions["utpp_recur"] > 0 ){
        
                                                        if( $parent_permissions["utpp_new"] > 0 ){
                                                                $new_permission = 1;
                                                        }else{
                                                                $new_permission = 0;
                                                        }
        
                                                        if( $parent_permissions["utpp_edit"] > 0 ){
                                                                $edit_permission = 1;
                                                        }else{
                                                                $edit_permission = 0;
                                                        }
        
                                                        $data_permissions  = array( 'utpp_user_type' => $user_type["user_type_id"] , 'utpp_structure' => $page_id , 'utpp_recur'=>1 , 'utpp_edit'=> $edit_permission , 'utpp_new'=> $new_permission );
        
                                                        $CI->db->insert('jempe_user_type_page_permissions' , $data_permissions );
        
                                                }
        
                                        }
        
                                }
                        }
                }
                
        }
        
        
        /**
        * user types
        *
        * User types list
        *
        * @access       public
        * @return       bool
        */      
        function user_types_list(){
                $CI =& get_instance();
        
                $user_types_list = array();
        
                $user_types = $CI->db->get_where('jempe_user_types' , array('user_type_active'=>1) );
        
                foreach($user_types->result_array() as $user_type ){
        
                        if( $CI->lang->line('jempe_user_type_' .$user_type["user_type_name"] ) )
                                $user_type["user_type_name"] = $CI->lang->line('jempe_user_type_' .$user_type["user_type_name"] );
                                
                        $user_types_list[$user_type["user_type_id"]] = $user_type["user_type_name"];
                }
        
                return $user_types_list;
        
        }

        
        /**
        * Admin menu link
        *
        * Create admin menu link 
        *
        * @access       public
        * @return       bool
        */      
        function admin_menu_link( $name , $link , $popup = false ){
                $CI =& get_instance();
        
                $url_segments = explode('/' , $link );
        
                $is_selected = true;
        
                // check if it is current page
                for( $i=0;$i<count($url_segments);$i++){
                        if($url_segments[$i] != $CI->uri->segment($i + 1) )
                                $is_selected = false;
        
                }
        
                if($is_selected){
                        $admin_menu_link = $this->admin_menu_selected_link;
                }else{
                        $admin_menu_link = $this->admin_menu_link;
                }
        
                if(strpos($link, "http") !== 0)
		{
                        $link = site_url($link);
		}
        
                $admin_link = str_replace('%url%',$CI->config->item('static_url') , str_replace( '%name%' , $name , str_replace( '%link%' , $link , $admin_menu_link ) )  );
        
                if($popup === true)
                        $admin_link = str_replace( '%popup%' , 'rel="popup"' , $admin_link ) ;
                else if($popup === false)
                        $admin_link = str_replace( '%popup%' , '' , $admin_link ) ;
                else if( strlen( $popup ) )
                        $admin_link = str_replace( '%popup%' , ' id="' .$popup .'"' , $admin_link ) ;
        
        
                return $admin_link;
                
                
        }

        
	/**
	* Page list
	*
	* Templates files list
	*
	* @access       public
	* @return       array
	*/
	function template_list($field = 'template')
	{
		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->helper('directory');

		foreach(directory_map(APPPATH.'views/'.$CI->jempe_cms->theme) as $template)
		{
			if( ! is_array($template))
			{
				if($field == "template")
				{
						$is_template = ! preg_match('/^base/', $template); 
				}

				if($field == "template_general")
				{
					$is_template = preg_match('/^base/', $template);
				}

				if(preg_match("/[.]php$/", $template) && $is_template)
				{
					if($CI->lang->line('jempe_template_'.preg_replace("/[.]php$/", "", $template)))
					{
						$template_name = $CI->lang->line('jempe_template_'.preg_replace("/[.]php$/" ,"", $template));
					}
					else
					{
						$template_name = $template;
					}

					$templates[$template] = $template_name;
				}
			}
		}

		asort($templates);
	
		return $templates;
	}
        
        
	/**
	* delete cache
	*
	* Delete server cache
	*
	* @access       public
	* @return       array
	*/
	function delete_cache()
	{
		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->helper('file');
	
		if($CI->jempe_cms->cache)
		{
			delete_files(BASEPATH .'cache/');
		}

		if($CI->jempe_cms->cache_enabled)
		{
			$CI->cache->clean();
		}
	}
        
	/**
	* can delete image
	*
	* check if the image belongs to a page
	*
	* @access       public
	* @return       array
	*/
	function can_delete_image($image_id)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_cms');

		$image_info = $CI->db->get_where('jempe_images', array('image_id' => $image_id));
		$image_info = $image_info->row_array();

		foreach($CI->jempe_cms->cms_fields as $cms_field)
		{
			if($cms_field["type"] == "htmlarea")
			{
				$CI->db->select('content_id');
				$CI->db->limit(1);

				if( ! isset($cms_field['languages']))
				{
					$CI->db->like("content_".$cms_field["name"], $image_info["image_file"]);
				}
				else
				{
					foreach($cms_field['languages'] as $lang_field)
					{
						$CI->db->like($lang_field, $image_info["image_file"]);
					}
				}

				$CI->db->where("sb_structure = content_id");
				$CI->db->from('jempe_structure_bind');
				$find_photo = $CI->db->get('jempe_content');
	
				if($find_photo->num_rows() > 0)
				{
					return FALSE;
				}
			}
	
			if($cms_field["type"] == "image")
			{
				$CI->db->select('content_id');
				$CI->db->limit(1);
				$CI->db->where("content_" .$cms_field["name"] , $image_id );
				$CI->db->where("sb_structure = content_id");
				$CI->db->from('jempe_structure_bind');
				$find_photo = $CI->db->get('jempe_content');
	
				if($find_photo->num_rows() > 0)
				{
					return FALSE;
				}
			}
		}
	
		return true;
	}
        
        /**
        * admin home page
        *
        * What is the admin default page of this user?
        *
        * @access       public
        * @return       array
        */
        function admin_home_page(){
                $CI =& get_instance();
        
                if( $CI->session->userdata("user_type") == 1 )
                        return 1;
                else{
        
                        //TODO home page para usuarios con el mismo tipo
        
                        if( $this->user_permission( 'edit_own_pages' , 'write' ) ){
                                $CI->db->select("structure_id");
                                $CI->db->order_by("structure_id","asc");
                                $CI->db->limit(1);
                                $CI->db->where("sb_structure = structure_id");
                                $CI->db->from("jempe_structure_bind");
                                $first_own_page = $CI->db->get_where('jempe_structure' , array('structure_user'=>$CI->session->userdata("user_id")) );
        
                                if( $first_own_page->num_rows() > 0 ){
                                        $first_own_page = $first_own_page->row_array();
                                        return $first_own_page["structure_id"];
                                }
        
                        }
        
                        
        
                }
        
                return 1;
        
        
        }

	// ------------------------------------------------------------------------

	/**
	* edit_page
	*
	* edit or create a new page
	*
	* @access       public
	* @return       bool
	*/
	function edit_page($page_data)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library('jempe_db');
		$CI->load->library('session');
		$CI->load->library('jempe_cms');

		$cms_fields = $CI->jempe_cms->cms_fields;

		foreach($cms_fields as $cms_field)
		{
			if($cms_field["type"] == "date")
			{
				if(isset($page_data["content_".$cms_field["name"]]))
				{
					if(strlen($page_data["content_".$cms_field["name"]]))
					{
						$page_data["content_".$cms_field["name"]] = date('Y-m-d', strtotime($page_data["content_".$cms_field["name"]]));
					}
					else
					{
						$page_data["content_".$cms_field["name"]] = NULL;
					}
				}
			}

			if($cms_field["type"] == "image")
			{
				$image_key_name = "content_".$cms_field["name"];

				if(array_key_exists($image_key_name, $page_data))
				{
					if( ! ($page_data[$image_key_name] > 0))
					{
						$page_data[$image_key_name] = NULL;
					}
				}
			}

			if($cms_field["name"] == "url")
			{
				if(isset($cms_field['languages']))
				{
					$structure_name_field = current($cms_field['languages']);
					$CI->jempe_cms->cms_languages = array_keys($cms_field['languages']);
				}
				else
				{
					$structure_name_field = 'content_url';
				}
			}
		}

		// look for parent pages
		if(isset($page_data['structure_id']) && $page_data['structure_id'] > 0)
		{
			$previous_parents = $CI->db->get_where('jempe_structure_bind' , array('sb_structure'=>$page_data['structure_id']));
        
			if($previous_parents->num_rows() > 0)
			{
				$previous_parents = $previous_parents->result_array();
        
				foreach($previous_parents as $previous_parent)
				{
					if( ! $this->page_permission($previous_parent["sb_parent"], $CI->session->userdata('user_id'), 'insert'))
					{ 
						$_POST["sb_parent"][] = $previous_parent["sb_parent"];
					}
				}
			}
			else
			{
				$previous_parents = array();
			}
		}
		else
		{
			$previous_parents = array();
		}

		$page_data["content_user"] = $CI->session->userdata('user_id');

		if(isset($page_data['structure_id']) && $page_data['structure_id'] > 0 && $this->page_permission($page_data['structure_id'], $CI->session->userdata('user_id')))
		{
			$page_data["content_id"] = $page_data['structure_id'];

			//saves old data in jempe_history table
			$history = $CI->db->get_where('jempe_content', array('content_id' => $page_data["content_id"]));
			$history = $history->row_array();

			$CI->jempe_db->insert_except('jempe_history', $history, array("content_id","content_timestamp"));

			// check pages order
			$page_previous_order = $CI->db->get_where('jempe_structure_bind', array('sb_structure' => $page_data["content_id"]));
			$page_previous_order = $CI->jempe_db->results_to_list($page_previous_order->result_array(), 'sb_parent', 'sb_order');

			//update page data
			$CI->jempe_db->update_except('jempe_content', "content_id", $page_data, array());

			$CI->jempe_cms->delete_cache_keys($page_data["content_id"], 'jempe_page');
		}
		else
		{
			// check for a page with the id 1
			$index = $CI->db->get_where('jempe_structure', array("structure_id"=>"1"));

			if($index->num_rows() == 0)
			{
				$page_data["structure_id"] = 1;
			}
			else
			{
				//check the last page id set a greater value
				$CI->db->order_by('structure_id desc'); 
				$CI->db->limit(1);
				$last_id = $CI->db->get('jempe_structure');
				$last_id = $last_id->row_array();
				$page_data["structure_id"] = $last_id["structure_id"] + 1;
			}

			// assign url value to structure_name 
			$page_data["structure_name"] = $page_data[$structure_name_field];
			$page_data["structure_user"] = $CI->session->userdata("user_id");
			$page_data["content_id"] = $CI->jempe_db->insert_except('jempe_structure', $page_data, array());
			$page_data["content_structure"] = $page_data["content_id"];
			$new_page_id = $CI->jempe_db->insert_except('jempe_content', $page_data, array());
		}

		//searches greatest sb_order value
		$CI->db->order_by('sb_order', 'desc');
		$CI->db->limit(1);
		$highest_order = $CI->db->get('jempe_structure_bind') ;
		if($highest_order->num_rows() > 0)
		{
			$highest_order = $highest_order->row_array();
			$highest_order = $highest_order['sb_order'] + 1;
		}
		else
		{
			$highest_order = 1;
		}

		if(isset($page_data["sb_parent"]))
		{
			//delete all parent pages bind to create them again

			if(count($previous_parents) > 0)
			{
				foreach($previous_parents as $previous_parent)
				{
					if($this->page_permission($previous_parent["sb_parent"], $CI->session->userdata('user_id'), 'insert'))
					{
						$CI->jempe_cms->delete_cache_keys($previous_parent["sb_parent"], 'jempe_page');

						$CI->db->delete('jempe_structure_bind', array('sb_structure' => $page_data["content_id"], 'sb_parent' => $previous_parent["sb_parent"])); 
					}
				}
			}

			foreach($page_data["sb_parent"] as $parent)
			{
				if($parent != $page_data["structure_id"] && !$CI->jempe_cms->is_parent($page_data["structure_id"], $parent) && $this->page_permission($parent, $CI->session->userdata('user_id'), 'insert'))
				{
					$data_parent = array(
						"sb_structure"	=>	$page_data["structure_id"],
						"sb_parent"	=>	$parent
					);

					// if it is a new page will give the highest order value, maintain old sb_order value if updating the page
					if(isset($new_page_id) && $new_page_id > 0)
					{
						$data_parent['sb_order'] = $highest_order;
					}
					else
					{
						if(isset($page_previous_order[$parent]))
						{
							$data_parent['sb_order'] = $page_previous_order[$parent];
						}
						else
						{
							$data_parent['sb_order'] = $highest_order;
						}
					}

					$CI->db->insert('jempe_structure_bind', $data_parent);

					$CI->jempe_cms->delete_cache_keys($data_parent['sb_order'], 'jempe_page');
				}
			}

			$this->update_page_permissions($page_data["structure_id"]);
		}

		return $page_data["content_id"];
	}

        // setup jquery functions form admin menu

        function admin_menu_functions(){
                $CI =& get_instance();
        
                $CI->jempe_form->jquery_fancybox = true;
                $CI->jempe_form->jquery_functions[] = '
                        $("#jempe_new_page").fancybox({ "type":"iframe" , "width":600 , "height":430 , "autoScale" :false });
                        $("a[rel^=popup]").fancybox({ "type":"iframe" , "showNavArrows":false });
                ';
        
        }
        
        // create form fields for CMS
        
        function create_cms_fields($content_fields = array(), $page = 'edit_page')
	{
                $CI =& get_instance();
        
                //check form fields
                foreach($CI->jempe_cms->cms_fields as $field)
		{
                        if($page == 'edit_page' OR ($page == 'new_page' && ($field["name"] == "url" OR $field["name"] == "title" OR $field["name"] == "link_name")))

                        //check if it is a multiple language field
                        if(isset($field["languages"]) && is_array($field["languages"]) && count($field["languages"]) > 1)
			{
                                $lang_fields = 0;
                                foreach($field["languages"] as $language => $db_field)
				{
                                        $form_field = $field;
					$form_field["field_name"] = $form_field["name"];

                                        //assign the preset value if there is one
                                        if($CI->jempe_cms->field_preset($db_field) !== FALSE)
					{
                                                $form_field["type"] = "hidden";
					}
 
                                        $form_field["label"] = $CI->lang->line('jempe_field_content_'.$field["name"])." ( ".$language." )";
                                        $form_field["var_name"] = $db_field;
                                        if($lang_fields)
					{
                                                $form_field["name"] .= $language;
					}
        
                                        // if it is a url field, add proper validation rules 
                                        if($field["name"] == "url")
					{
                                                $CI->form_validation->set_rules($db_field, $CI->lang->line('jempe_field_content_'.$field["name"]), "trim|required|url|callback_check_url[$db_field]");
                                        }

                                        // if it is a link name field, add proper validation rules 
                                        if($field["name"] == "title" OR $field["name"] == "link_name")
					{
                                                $CI->form_validation->set_rules($db_field, $CI->lang->line('jempe_field_content_'.$field["name"]), "trim|required");
                                        }

                                        $content_fields[] = $form_field;
                                        $lang_fields++;
                                        $content["admin_content"]["cms_fields"][] = $form_field["name"];
                                }
                        }
			else
			{
				$field["field_name"] = $field["name"];

                                $field["label"] = $CI->lang->line('jempe_field_content_'.$field["name"]);
                                $field["var_name"] = "content_".$field["name"];

                                if($CI->jempe_cms->field_preset($field["var_name"]) !== FALSE)
				{
                                        $field["type"] = "hidden";
				}

                                // if it is a url field, add proper validation rules 
                                if($field["name"] == "url")
				{
                                        $CI->form_validation->set_rules("content_".$field["name"], $CI->lang->line('jempe_field_content_'.$field["name"]), 'trim|required|url|callback_check_url[content_url]');
                                }
        
                                // if it is a link name field, add proper validation rules 
                                if($field["name"] == "title" OR $field["name"] == "link_name")
				{
                                        $CI->form_validation->set_rules( "content_".$field["name"], $CI->lang->line('jempe_field_content_'.$field["name"]), 'trim|required');
                                }
        
                                $content_fields[] = $field;
                                $content["admin_content"]["cms_fields"][] = $field["name"];
        
                        }
        
                        // set non selected checkbox values to 0
        
                        if($field['type'] == 'bool' && isset($_POST["jempe_fields"]) && !$CI->input->post('content_' .$field['name'] ) && in_array( "content_" .$field['name'] , $_POST["jempe_fields"] ) )
                                $_POST["content_" .$field['name'] ] = 0;
        
                }
        
                return array($content_fields , $content["admin_content"]["cms_fields"] );
        
        }
}

// END Jempe_Admin class

/* End of file jempe_admin.php */
/* Location: ./application/libraries/jempe_admin.php */
