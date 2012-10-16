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
 * Jempe Admin Controller
 *
 * This Controller class handle admin backend pages
 *
 * @package		Jempe
 * @subpackage	Controllers
 * @category	Admin Backend
 * @author		Sucio Kastro
 * @link		http://jempe.org/documentation/controllers/admin.html
 */

Class Admin extends CI_Controller{

	/**
	* Admin constructor
	*
	* Loads main libraries and set the headers of admin backend pages
	* 
	*
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->library('jempe_cms');
		$this->load->library('jempe_admin');
		$this->load->library('jempe_form');
		$this->load->library('jempe_db');
		$this->load->database();
		$this->load->helper('url');
		$this->lang->load('jempe');
		$this->load->library('session');
		$this->jempe_admin->admin_menu_functions();

		if(strpos($_SERVER["REQUEST_URI"], '.js'))
		{
			$this->output->set_header('Content-Type: application/x-javascript; charset='.$this->config->item('charset'));
		}
		else
		{
			$this->output->set_header('HTTP/1.0 200 OK');
			$this->output->set_header('HTTP/1.1 200 OK');
			$this->output->set_header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
			$this->output->set_header('Pragma: no-cache'); 
			$this->output->set_header('Expires: Fri, 01 Jan 1990 00:00:00 GMT');
			$this->output->set_header('Date: '.gmdate('D, d M Y H:i:s', time()).' GMT');

			if(strpos($_SERVER['REQUEST_URI'], '.xml'))
			{
				$this->output->set_header('Content-Type: text/xml; charset='.$this->config->item('charset'));
			}
			else if (strpos($_SERVER['REQUEST_URI'], '.css'))
			{
				$this->output->set_header('Content-Type: text/css');
			}
			else if (strpos($_SERVER['REQUEST_URI'], '.htc'))
			{
				$this->output->set_header('Content-type: text/x-component');
			}
			else
			{
				$this->output->set_header('Content-Type: text/html; charset='.$this->config->item('charset'));
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Render website pages in admin backend
	 *
	 * Render pages changing the links to admin pages links and add Jempe main menu
	 *
	 * @access	public
	 * @return	void
	 */	
	function index()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}
		
		// is there a default page?
		$index = $this->db->get_where('jempe_structure', array('structure_id' => 1));

		if($index->num_rows() > 0)
		{
			if($this->uri->segment(3) > 0)
			{
				$page_id = $this->uri->segment(3);
			}
			else
			{
				redirect($this->jempe_cms->page_link($this->jempe_admin->admin_home_page()));
			}
	
			// set links type for admin site
			$this->jempe_cms->user_type = 'admin';

			//create the pages tree
			$this->jempe_cms->structure = $this->jempe_cms->structure_from_id($page_id);
			array_unshift($this->jempe_cms->structure, array('structure_id' => 0));
			$this->jempe_cms->page_data();

			// search current page data
			$data = $this->jempe_cms->page_data;
	
			// check if there is a model with the same name of the main template and loads it
			if(isset($data['model']))
			{
				$this->load->model($data['model'], '', TRUE);
				$data = $this->$data['model']->action($data);
			}
	
			// check if there is a model with the same name of the second template and loads it
			if(isset($data['model_template']))
			{
				$this->load->model($data['model_template'], '', TRUE);
				$data = $this->$data['model_template']->action($data);
			}
	
			$data['jempe_admin'] = TRUE;
			$this->load->view($this->jempe_cms->template, $data);
		}
		else
		{
			redirect('admin/page_edit');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin backend login page
	 *
	 * Render Admin backend login page
	 *
	 * @access	public
	 * @return	void
	 */
	function login()
	{
		if(count($_POST))
		{
			if($this->input->post('user_username') && $this->input->post('user_password')){
	
				if($this->jempe_admin->authenticate($this->input->post('user_username'), $this->input->post('user_password')))
				{
					redirect('');
				}
				else
				{
					$content['error'] = $this->lang->line('jempe_error_login');
				}
			}
			else
			{
				$content['error'] = $this->lang->line('jempe_error_login');
			}

			$this->load->view('admin/login', $content);
		}
		else
		{
			$this->load->view('admin/login');
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin page to upload files
	 *
	 * @access	public
	 * @return	void
	 */
	function jempe_uploader()
	{
		$this->load->library('jempe_form');

		if($this->input->post('upload_config') && isset($this->jempe_cms->upload_files_config[$this->input->post('upload_config')]))
		{
			$config = $this->jempe_cms->upload_files_config[$this->input->post('upload_config')];
			$config['upload_path'] = upload_path().$config['upload_path'];
			$config['remove_spaces'] = TRUE;

			$this->load->library('upload');
			$this->upload->initialize($config);

			if ( ! $this->upload->do_upload('jempe_uploader_'.$this->input->post('upload_config')))
			{
				$response['error'] = $this->upload->display_errors('','');
			}	
			else
			{
				if(isset($config['execute_after_upload']))
				{
					$post_upload_model = $config['execute_after_upload']['Model'];
					$post_upload_function = $config['execute_after_upload']['action'];

					$this->load->Model($post_upload_model);

					$response = $this->$post_upload_model->$post_upload_function($this->upload->data());
				}
				else
				{
					$response = $this->upload->data();
					if(isset($config['onComplete']))
					{
						$response['onComplete'] = $config['onComplete'];
					}
					$response['error'] = '';
				}
			}
		}
		else
		{
			$response['error'] = $this->lang->line('jempe_uploader_config_error');
		}

		$this->output->set_output($this->jempe_form->xml_results($response));
	}

	
	// --------------------------------------------------------------------
	
	/**
	 * Check if a record is unique
	 *
	 * @access	public
	 * @return	void
	 */
	function jempe_is_unique()
	{
		$config = $this->input->post('jempe_is_unique');

		if($this->config->item('is_unique_config'))
		{
			$unique_config = $this->config->item('is_unique_config');

			$results['success'] = 0;
			$results['error'] = '';

			if(isset($unique_config[$config]))
			{
				$is_unique = $unique_config[$config];

				if($is_unique['not_equal_source'] == 'post')
				{
					$not_equal_value = $this->input->post($is_unique['not_equal_key']);
				}
				else if($is_unique['not_equal_source'] == 'session')
				{
					$not_equal_value = $this->session->userdata($is_unique['not_equal_key']);
				}

				if($this->jempe_db->is_unique($this->input->post($is_unique['db_field']), $is_unique['db_table'], $is_unique['db_field'], $is_unique['filter'], $is_unique['not_equal_field'], $not_equal_value))
				{
					$results['success'] = 1;
				}
				else
				{
					$this->load->helper('string');

					$last_suggestion = increment_string($this->input->post($is_unique['db_field']), '');
					$suggestion_number = 0;
					while ($suggestion_number < 10)
					{
						if($this->jempe_db->is_unique($last_suggestion, $is_unique['db_table'], $is_unique['db_field'], $is_unique['filter'], $is_unique['not_equal_field'], $not_equal_value))
						{
							$suggestion_number = 11;
							$results['suggestion'] = $last_suggestion;
						}
						$suggestion_number++;
					}
				}
			}
		}

		$this->load->library('jempe_form');
		$this->output->set_output($this->jempe_form->xml_results($results));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin backend new page form
	 *
	 * Render Admin backend new page form inside a fancybox popup
	 *
	 * @access	public
	 * @return	void
	 */
	function new_page(){
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		$this->load->library('form_validation');

		// changes the multiple checkbox field template
		$this->jempe_form->checkboxmultiple_template = $this->jempe_form->radiomultiple_template = array(
			'checkbox_open' => '<div style="float:left;">',
			'checkbox_row' => '<div style="float:left; width:100%;"><div style="float:left;"class="jempe_category">{value}</div><div style="float:left;" class="jempe_category">{name}</div></div>',
			'checkbox_close' => '</div>'
		);

		$content['template'] = "admin/new_page" ;

		list($content_fields, $content['admin_content']['cms_fields']) = $this->jempe_admin->create_cms_fields(array(), 'new_page');

		$this->jempe_form->javascript_functions[] = "
			function url_title( text ){
				text = text.toLowerCase();
				text = text.replace(/ /g,'_');
				text = text.replace(/[^a-zA-Z0-9_]+/g,'');

				return text;
			}

			function str_replace (search, replace, subject, count) {
				var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,
					f = [].concat(search),
					r = [].concat(replace),
					s = subject,
					ra = r instanceof Array, sa = s instanceof Array;
				s = [].concat(s);
				if (count) {
					this.window[count] = 0;
				}
				
				for (i=0, sl=s.length; i < sl; i++) {
					if (s[i] === '') {
					continue;
					}
					for (j=0, fl=f.length; j < fl; j++) {
					temp = s[i]+'';
					repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
					s[i] = (temp).split(f[j]).join(repl);
					if (count && s[i] !== temp) {
						this.window[count] += (temp.length-s[i].length)/f[j].length;}
					}
				}
				return sa ? s : s[0];
			}
		";

		for($i = 0; $i < count($content_fields); $i++)
		{
			if(isset($content_fields[$i]['extra']))
			{
				$content_fields[$i]['extra'] .= ' autocomplete="off" id="'.$content_fields[$i]['name'].'" ';
			}
			else
			{
				$content_fields[$i]["extra"] = ' autocomplete="off" id="'.$content_fields[$i]['name'].'" ';
			}

			if( $content_fields[$i]['field_name'] == "title" )
			{
				$content_fields[$i]['label'] .= $this->jempe_form->form_checkbox('ut'.$content_fields[$i]['name'], 1, $this->input->post('ut'.$content_fields[$i]['name']), 'id="ut'.$content_fields[$i]['name'].'"');

				$url_field_id = str_replace('title', 'url', $content_fields[$i]['name']);
				$link_name_field_id = str_replace('title', 'link_name', $content_fields[$i]['name']);

				$this->jempe_form->jquery_functions[] = '
					$("#ut'.$content_fields[$i]['name'].'").click(function()
					{
						if($(this).is(":checked"))
						{
							$("#'.$url_field_id.'").removeAttr("readonly");
							$("#'.$link_name_field_id.'").removeAttr("readonly");
						}
						else
						{
							$("#'.$url_field_id.'").attr("readonly", TRUE);
							$("#'.$link_name_field_id.'").attr("readonly", TRUE);
						}
					});

					$("#'.$content_fields[$i]['name'].'").keyup(function()
					{
						if($("#ut'.$content_fields[$i]['name'].'").not(":checked"))
						{
							$("#'.$url_field_id.'").val(url_title($(this).val()));
							$("#'.$link_name_field_id.'").val($(this).val());
						}
					});';
			}
			if($content_fields[$i]['field_name'] == "link_name" OR $content_fields[$i]['field_name'] == "url" )
			{
				$content_fields[$i]['extra'] .= ' readonly="TRUE" ';
			}
		}

		$content_fields[] = array(
			"name"=>"is_category",
			"var_name"=>"content_is_category",
			"type"=>"bool",
			"label"=>$this->lang->line("jempe_field_content_is_category"),
			"extra"=>'class="jempe_checkbox"'
		);

		$content_fields[] = array(
			"name"=>"parent",
			"var_name"=>"sb_parent",
			"type"=>"multiplecheckbox",
			"options"=>$this->jempe_admin->page_list( false , $this->jempe_cms->max_levels ),
			"label"=>$this->lang->line("jempe_field_sb_parent"),
			"extra"=>'class="jempe_checkbox"'
		);

		$templates_list = $content["admin_content"]["templates_list"] = $this->jempe_admin->template_list();

		$content_fields[] = array(
			"name"=>"template",
			"var_name"=>"content_template",
			"type"=>"list",
			"options"=>$templates_list,
			"label"=>$this->lang->line("jempe_field_content_template")
		);

		$this->jempe_form->jquery_functions[] = '
			$("#new_page_categories").click( function(){
				$(".page_data_fields").hide();
				$(".template_fields").hide();
				$(".categories_fields").show();
				$("#jempe_submenu a").removeClass("selected");
				$(this).addClass("selected");
			});

			$("#new_page_data").click( function(){
				$(".page_data_fields").show();
				$(".template_fields").hide();
				$(".categories_fields").hide();
				$("#jempe_submenu a").removeClass("selected");
				$(this).addClass("selected");
			});

			$("#new_page_template").click( function(){
				$(".page_data_fields").hide();
				$(".template_fields").show();
				$(".categories_fields").hide();
				$("#jempe_submenu a").removeClass("selected");
				$(this).addClass("selected");
			});
			$(".page_data_fields input[type=submit]").click( function(c){
				c.preventDefault();
				$("#new_page_template").trigger("click");
			});
			$(".template_fields input[type=submit]").click( function(c){
				c.preventDefault();
				$("#new_page_categories").trigger("click");
			});
			$("select[name=content_template]").change(function(){
				$("#template_previews img").hide();
				template_name = str_replace(".php" , "" , $(this).val());
				$(".preview_" + template_name ).fadeIn("slow");
			});
		';

		if ($this->form_validation->run() == FALSE)
		{
			$content["error"] = $this->form_validation->error_string('<div class="jempe_error">', '</div>') ;
		}
		else
		{
			$default_page_values = $this->jempe_db->blank_fields('jempe_content');

			foreach($_POST as $var => $value)
			{
				$default_page_values[$var] = $value;
			}

			$default_page_values['content_template_general'] = "base.php";

			unset($default_page_values['content_timestamp']);

			$content_id = $this->jempe_admin->edit_page($default_page_values);

			// clear codeigniter cache
			$this->jempe_admin->delete_cache();

			$this->jempe_form->jquery_functions[] = '
				parent.window.location = "' .$this->jempe_cms->page_link( $content_id ) .'";
			';
			
		}

		if( count( $_POST ) ){
			$default_values = $_POST;
		}else{
			$default_values = $this->jempe_db->blank_fields( 'jempe_content' );
		}

		$content["submenu"] = "new_page_menu";

		$content["admin_content"]["fields"] = $this->jempe_form->form($content_fields , $default_values ) ;

		$content["title"] = $this->lang->line('jempe_page_new_page');
		$this->load->view('admin/admin_popup',$content);

	}

	// --------------------------------------------------------------------
	
	/**
	 * Admin backend show CMS editable fields
	 *
	 * @access	public
	 * @return	void
	 */
	function inline_field()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			$this->output->set_output('Error:'.str_replace('{url}', site_url('admin/login'), $this->lang->line('jempe_error_ajax_must_login')));
		}
		else if(strpos($this->input->post('field_name'), 'jempe_lang') === 0)
		{
			$lang_line_field = 'lang_line_'.$this->config->item('language');

			$this->db->select('lang_id, lang_type, '.$lang_line_field);
			$lang_line = $this->db->get_where('jempe_lang', array('lang_key' => $this->input->post('structure_id')));
			$lang_line = $lang_line->row_array();

			if( ! isset($lang_line[$lang_line_field]))
			{
				$lang_line[$lang_line_field] = $this->input->post('structure_id');
			}

			$output = '<form id="form_inline_editor" onsubmit=" jempe_save_field(); return false;">'.$this->jempe_form->form_hidden('lang_id', $lang_line);

			if($lang_line['lang_type'] == 'htmlarea')
			{
				$lang_form_field = "form_htmlarea";
			}
			else if($lang_line['lang_type'] == 'textarea')
			{
				$lang_form_field = "form_textarea";
			}
			else
			{
				$lang_form_field = "form_input";
			}

			$output .= ''.$this->jempe_form->$lang_form_field($lang_line_field, $lang_line);

			$output .= '</form>';

			$this->output->set_output($output);
		}
		else if($this->jempe_admin->page_permission($this->input->post('structure_id'), $this->jempe_admin->user_id(), 'insert'))
		{
			$this->db->select('structure_id, content_fields_preset');
			$this->db->select($this->input->post('field_name'));
			$this->db->group_by('structure_id');
			$this->db->where('structure_id = content_id');
			$this->db->where('sb_structure = structure_id');
			$this->db->from('jempe_structure_bind');
			$this->db->from('jempe_structure');
			$page_data = $this->db->get_where('jempe_content', array('structure_id' => $this->input->post('structure_id')));

			if($page_data->num_rows() > 0)
			{
				$page_data = $page_data->row_array();

				$output = '<form id="form_inline_editor" onsubmit=" jempe_save_field(); return false;">'.$this->jempe_form->form_hidden('structure_id', $page_data).$this->jempe_form->form_hidden('content_fields_preset', $page_data);

				foreach($this->jempe_cms->cms_fields as $cms_field)
				{
					$field_name = "content_" .$cms_field["name"];

					if(isset($cms_field['options']))
					{
						$options = $cms_field['options'];
					}
					else
					{
						$options = FALSE;
					}

					if(isset($cms_field['extra']))
					{
						$extra = $cms_field['extra'];
					}
					else
					{
						$extra = FALSE;
					}

					if(isset($cms_field['thumb']))
					{
						$thumb = $cms_field['thumb'];
					}
					else
					{
						$thumb = FALSE;
					}
	
					if(isset($cms_field["languages"]))
					{
						foreach($cms_field["languages"] AS $language_field_name)
						{
							if($language_field_name == $this->input->post('field_name'))
							{
								$output .= $this->jempe_form->create_form_field($cms_field['type'], $this->input->post('field_name'), $page_data, $options, $extra, $thumb);
							}	
						}
					}
					else
					{
						if($field_name == $this->input->post('field_name'))
						{
							$output .= $this->jempe_form->create_form_field($cms_field['type'], $this->input->post('field_name'), $page_data, $options, $extra, $thumb);
						}
					}
				}
				$output .= '</form>';

				$this->output->set_output($output);
			}
			else
			{
				$this->output->set_output('Error: '.$this->lang->line('jempe_error_page_not_exist'));
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save CMS field
	 *
	 * Validate and save CMS field
	 *
	 * @access	public
	 * @return	void
	 */
	function save_field()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			$response['error'] = 'login_error'; 
		}
		else if($this->input->post('lang_id') > 0 && $this->jempe_admin->user_permission('lang', 'write'))
		{
			$lang_var = 'lang_line_'.$this->config->item('language');

			$this->jempe_db->update_except('jempe_lang', 'lang_id', array('lang_id' => $this->input->post('lang_id'),  $lang_var => $this->input->post($lang_var)));

			$this->jempe_cms->delete_cache_keys($this->input->post('lang_id'), 'jempe_lang');

			$response['success'] = 1;
			$response['error'] = '';
		}
		else if($this->jempe_admin->page_permission($this->input->post('structure_id'), $this->jempe_admin->user_id(), 'insert'))
		{
			$this->load->library('form_validation');

			foreach($this->jempe_cms->cms_fields as $cms_field)
			{
				$field_name = "content_" .$cms_field["name"];

				if(isset($cms_field['validation']))
				{
					$field_validation = $cms_field['validation'];
				}
				else
				{
					$field_validation = 'trim';
				}

				if(isset($cms_field["languages"]))
				{
					foreach($cms_field["languages"] AS $language_field_name)
					{
						if($this->input->post($language_field_name) !== FALSE)
						{
							$this->form_validation->set_rules($language_field_name, $this->lang->line('jempe_field_'.$field_name), $field_validation);
						}	
					}
				}
				else
				{
					if($this->input->post($field_name) !== FALSE)
					{
						$this->form_validation->set_rules($field_name, $this->lang->line('jempe_field_'.$field_name), $field_validation);
					}
				}
			}

			if($this->form_validation->run())
			{
				// TODO make sure that only the editable field data is submitted 
				if($this->input->post('structure_id') > 0)
				{
					// check the fields that were sent by the inline form
					$sent_fields = array();

					foreach($_POST as $field_name => $field_value)
					{
						$sent_fields[] = $field_name;
					}

					$this->load->model('My_presets');

					if(in_array('jempe_process_fields', $this->My_presets->presets))
					{
						$this->My_presets->jempe_process_fields();
					}

					if(in_array($this->input->post('content_fields_preset'), $this->My_presets->presets))
					{
						$preset_name = $this->input->post('content_fields_preset');

						$this->My_presets->$preset_name();
					}

					foreach($_POST as $field_name => $field_value)
					{
						if( ! in_array($field_name, $sent_fields))
						{
							unset($_POST[$field_name]);
						}
					}

					$field_data = $_POST;
					$field_data["structure_id"] = $this->input->post('structure_id');
	
					$this->jempe_admin->edit_page($field_data);
					$response['success'] = 1;
					$response['error'] = '';
					$this->jempe_admin->delete_cache();
				}
				else
				{
					$response['error'] = $this->lang->line('jempe_error_specify_page');
				}
			}
			else
			{
				$response['error'] = trim($this->form_validation->error_string());
			}
		}
		else
		{
			$response['error'] = $this->lang->line('jempe_error_page_permission');
		}

		$this->output->set_output($this->jempe_form->xml_results($response));
	}

	function page_edit()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		$this->load->library('form_validation');

		// changes the multiple checkbox field template
		$this->jempe_form->checkboxmultiple_template = $this->jempe_form->radiomultiple_template = array(
			"checkbox_open" => '<div style="float:left;">' ,
			"checkbox_row" => '<div style="float:left; width:100%;"><div style="float:left;"class="jempe_category">{value}</div><div style="float:left;" class="jempe_category"><label for="{option_id}">{name}</label></div></div>' ,
			"checkbox_close" => '</div>'
		);

		// check if url has field values preset
		if($this->uri->segment(3) == "preset" && strlen($this->uri->segment(4)) && isset($this->jempe_cms->presets[$this->uri->segment(4)]))
		{
			$this->jempe_cms->selected_preset = $this->uri->segment(4);
		}

		//check post values for field values preset
		if($this->input->post('content_fields_preset'))
		{
			$this->jempe_cms->selected_preset = $this->input->post('content_fields_preset');
		}

		//check if the page used field values preset
		if($this->uri->segment(3) > 0)
		{
			if( ! $this->jempe_admin->page_permission($this->uri->segment(3), $this->jempe_admin->user_id()))
			{
				redirect('admin');
			}

			$page_preset = $this->db->get_where('jempe_content',array('content_id'=>$this->uri->segment(3)) );
			$page_preset = $page_preset->row_array();
		
			if(strlen($page_preset["content_fields_preset"]) && isset($this->jempe_cms->presets[$page_preset["content_fields_preset"]]))
			{
				$this->jempe_cms->selected_preset = $page_preset["content_fields_preset"];
			}
		}

		$content["template"] = "admin/page_edit" ;

		// create CMS form fields
		list($content_fields , $content["admin_content"]["cms_fields"]) = $this->jempe_admin->create_cms_fields();

		// only if doesnt have child pages
		if($this->input->post("structure_id") > 0)
		{
			if( ! $this->jempe_cms->has_children($this->input->post("structure_id")))
			{
				// is category field unchecked? 
				if(isset($_POST["jempe_fields"]) && ! $this->input->post('content_is_category') && in_array("content_is_category", $_POST["jempe_fields"]))
				{
						$_POST["content_is_category"] = 0;
				}
			}
			else
			{
				$_POST["content_is_category"] = 1;
			}
		}

		if($this->jempe_cms->field_preset("content_is_category") !== FALSE)
		{
			$is_category_field_type = "hidden";
		}
		else
		{
			$is_category_field_type = "bool";
		}

		$is_category_field_settings = array(
			"name"		=>	"is_category",
			"var_name"	=>	"content_is_category",
			"type"		=>	$is_category_field_type,
			"label"		=>	$this->lang->line("jempe_field_content_is_category"),
			"extra"		=>	'class="jempe_checkbox"'
		);

		if($this->uri->segment(3) > 0 OR ($this->input->post("structure_id") > 0))
		{
			// if going to edit a page create a hidden field with the page id value
			$content_fields[] = array(
				'var_name'	=>	'structure_id', 
				'name'		=>	'id', 
				'label'		=>	"", 
				"type"		=>	"hidden"
			);

			// dont add the current page to the parent pages list
			if($this->input->post('structure_id') > 0)
			{
				$jempe_page_list = $this->jempe_admin->page_list($this->input->post('structure_id'), $this->jempe_cms->max_levels);

				$page_id = $this->input->post('structure_id');

			}
			else
			{
				$jempe_page_list = $this->jempe_admin->page_list($this->uri->segment(3), $this->jempe_cms->max_levels);

				$page_id = $this->uri->segment(3);
			}

			// if page has children pages , content_is_category checkbox is read only
			if($this->jempe_cms->has_children($page_id))
			{
				$is_category_field_settings["extra"] = ' readonly="true" ';
			}
		}
		else
		{
			$jempe_page_list = $this->jempe_admin->page_list(FALSE, $this->jempe_cms->max_levels);
		}

		// if page preset has a sb_parent value convert field to a hidden input 
		if($this->jempe_cms->field_preset('sb_parent') !== FALSE OR count($jempe_page_list) == 0)
		{
			$content_fields[] = array(
				'name'		=>	'parent',
				'var_name'	=>	'sb_parent',
				'type'		=>	'hidden',
				'options'	=>	'',
				'label'		=>	''
			);
		}
		else
		{
			$parent_page_field_type = "multiplecheckbox";

			$content_fields[] = array(
				'name'		=>	'parent',
				'var_name'	=>	'sb_parent',
				'type'		=>	$parent_page_field_type,
				'options'	=>	$jempe_page_list,
				'label'		=>	$this->lang->line('jempe_field_sb_parent'),
				'extra'		=>	'class="jempe_checkbox"'
			);
		}

		// create template dropdown field
		$content_fields[] = $is_category_field_settings;

		$content_fields[] = array(
			"name"		=>	"template",
			"var_name"	=>	"content_template",
			"type"		=>	"list",
			"options"	=>	$this->jempe_admin->template_list(),
			"label"		=>	$this->lang->line("jempe_field_content_template")
		);

		// create main template dropdown field
		$container_templates = $this->jempe_admin->template_list('template_general');

		// if there is only one template file hide the field
		if(count($container_templates) == 1 OR $this->jempe_cms->field_preset('content_template_general') !== FALSE)
		{
			$template_name = array_keys($container_templates);

			if($this->jempe_cms->field_preset('content_template_general') !== FALSE)
			{
				$default_value = $this->jempe_cms->field_preset('content_template_general');
			}
			else
			{
				$default_value = $template_name[0];
			}

			$content_fields[] = array(
				"name"		=>	"template_general",
				"var_name"	=>	"content_template_general",
				"type"		=>	"hidden",
				"label"		=>	$this->lang->line("jempe_field_content_template_general"),
				"value"		=>	$default_value
			);
		}
		else
		{
			$content_fields[] = array(
				"name"		=>	"template_general",
				"var_name"	=>	"content_template_general",
				"type"		=>	"list",
				"options"	=>	$this->jempe_admin->template_list('template_general'),
				"label"		=>	$this->lang->line("jempe_field_content_template_general")
			);
		}

		$this->load->model('My_presets');
		if(in_array('jempe_process_fields', $this->My_presets->presets))
		{
			$this->My_presets->jempe_process_fields();
		}

		if($this->jempe_cms->selected_preset)
		{
			if(in_array($this->jempe_cms->selected_preset, $this->My_presets->presets))
			{
				$preset_name = $this->jempe_cms->selected_preset;

				$this->My_presets->$preset_name();
			}
		}

		// form is submitted
		if(isset($_POST) && count($_POST))
		{
			// 
			if($this->input->post("structure_id") && ! $this->input->post("content_url") && ! $this->input->post("content_title"))
			{	
				$this->form_validation->set_rules('structure_id', 'Structure id', "trim|required|integer|is_natural_no_zero");
			}

			if( ! isset($_POST['sb_parent']) OR ! is_array($_POST['sb_parent']))
			{
				$_POST['sb_parent'] = array();
			}

			// look for parent pages
			if($this->input->post('structure_id') > 0)
			{
				$previous_parents = $this->db->get_where('jempe_structure_bind', array('sb_structure' => $this->input->post("structure_id")));

				if($previous_parents->num_rows() > 0)
				{
					$previous_parents = $previous_parents->result_array();

					foreach($previous_parents as $previous_parent)
					{
						if( ! $this->jempe_admin->page_permission($previous_parent["sb_parent"], $this->jempe_admin->user_id(), 'insert'))
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

			if($this->form_validation->run() == FALSE)
			{
				$content["error"] = $this->form_validation->error_string('<div class="jempe_error">','</div>') ;
			}
			else
			{
				$content_id = $this->jempe_admin->edit_page( $_POST );

				// clear codeigniter cache
				$this->jempe_admin->delete_cache();

				redirect($this->jempe_cms->page_link($content_id));
			}

			$default_values = $_POST;
		}
		else
		{
		if($this->uri->segment(3) > 0)
		{
				$this->db->from('jempe_content , jempe_structure , jempe_structure_bind ');
				$this->db->where('content_structure = structure_id' ); 
				$this->db->where('content_structure = sb_structure' ); 
				$this->db->where('content_structure',$this->uri->segment(3)); 
				$pagina = $this->db->get();
				$default_values = $pagina->row_array();

				// check all parent pages
				$parent_pages = $this->db->get_where('jempe_structure_bind',array('sb_structure'=>$default_values["structure_id"] ));

				foreach($parent_pages->result_array() as $parent_page)
				{
					$parent[] = $parent_page["sb_parent"]; 
				}

				$default_values["sb_parent"] = $parent;
			}
			else
			{
				// set db default values
				$campos_content = $this->jempe_db->blank_fields('jempe_content');
				$campos_structure_bind = $this->jempe_db->blank_fields('jempe_structure_bind');
				$campos_structure_bind["sb_parent"] = array();

				$default_values = array_merge($campos_content ,$campos_structure_bind );

				// assign preset values
				if($this->jempe_cms->selected_preset)
				{
					foreach($this->jempe_cms->presets[$this->jempe_cms->selected_preset] as $preset_field=>$preset_value)
					{
						$default_values[$preset_field] = $preset_value;
					}
				}
			}
		}

		//send preset name to form
		if($this->jempe_cms->selected_preset )
			$_POST["content_fields_preset"] = $this->jempe_cms->selected_preset;

		$content["admin_content"]["fields"] = $this->jempe_form->form($content_fields , $default_values ) ;

		$content["title"] = $this->lang->line('jempe_page_edit');
		$this->load->view('admin/admin',$content);

	}

	function edit_order(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		// process page order values
		if( $this->input->post('sb_parent') !== false && is_numeric( $this->input->post('sb_parent') ) ){
		
			$sorted_data = $this->jempe_form->process_sortable_list( 'jempe_page_list' , $_POST );


			if( count($sorted_data) ){
				foreach( $sorted_data as $page_id=>$page_order ){
					
					$this->db->limit(1);
					$this->db->where('sb_structure', $page_id );
					$this->db->where('sb_parent', $this->input->post('sb_parent'));
					$this->db->update('jempe_structure_bind', array('sb_order'=>$page_order) ); 


				}
			}
			
			$content["admin_content"]["success"] = true;
		}

		$content["template"] = "admin/edit_order" ;

		if( ($this->uri->segment(3) > 0 || $this->uri->segment(3) === "0" ) ){
			$this->db->from('jempe_content , jempe_structure , jempe_structure_bind ');
			$this->db->where('content_structure = structure_id' ); 
			$this->db->where('content_structure = sb_structure' ); 
			$this->db->where('sb_parent',$this->uri->segment(3) ); 
			$this->db->order_by('sb_order' , 'asc');
			$pages = $this->db->get();

			$_POST["sb_parent"] = $this->uri->segment(3) ;
		}

		$page_list = array();

		foreach($pages->result_array() as $page){
			$page_list[$page["structure_id"]] = $this->jempe_cms->page_link($page["structure_id"] , true);

		}

		$content["admin_content"]["sortable_list"] = $this->jempe_form->form_sortable_list( 'jempe_page_list' ,$page_list , 'jempe_page_list_placeholder' );

		$content["title"] = $this->lang->line('jempe_page_edit_order');
		$this->load->view('admin/admin',$content);
	}

	function delete(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		if( ! $this->jempe_admin->page_permission($this->input->post('structure_id'), $this->jempe_admin->user_id()))
		{
			redirect('admin');
		}

		if($this->input->post('delete'))
		{
			// delete only jempe_structure_bind row, to be able to create the page again
			if($this->input->post('delete') == $this->lang->line('jempe_button_delete') ){

				//check if the page is blocked
				$blocked = $this->db->get_where('jempe_structure',array('structure_blocked'=>1,'structure_id'=>$this->input->post('structure_id')));
		
				if($blocked->num_rows() == 0 && $this->jempe_admin->page_permission($this->input->post('structure_id'), $this->jempe_admin->user_id()))
				{
					$this->db->delete('jempe_structure_bind', array('sb_structure' => $this->input->post('structure_id')));
				}

				$content["admin_content"]["success"] = TRUE;
			}	

		}
		$content["template"] = "admin/delete" ;

		// check page data
		$this->db->from('jempe_content , jempe_structure , jempe_structure_bind ');
		$this->db->where('content_structure = structure_id' ); 
		$this->db->where('content_structure = sb_structure' ); 
		$this->db->where('structure_id',$this->uri->segment(3) ); 
		$page = $this->db->get();


		$content["title"] = $this->lang->line('jempe_page_delete');
		$content["admin_content"]["page"] = $page->row_array();
		$this->load->view('admin/admin_popup',$content);		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Page that delete site cache
	 *
	 * @access	public
	 * @return	void
	 */
	function delete_cache()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		$this->jempe_admin->delete_cache();			redirect();
	}
	
	function admin_password()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		if( ! $this->jempe_admin->user_permission('change_password', 'write'))
		{
			redirect('admin');
		}

		$this->load->library('form_validation');

		$content["admin_content"] = "";

		if($this->input->post("user_password") !== FALSE)
		{
			$this->form_validation->set_rules('old_password', $this->lang->line('jempe_button_old_password'), 'trim|required|callback_check_password');
			$this->form_validation->set_rules('user_password', $this->lang->line('jempe_button_new_password'), 'trim|required|alpha_dash|matches[confirm_password]|min_length[5]|max_length[30]|md5');
			$this->form_validation->set_rules('confirm_password', $this->lang->line('jempe_button_confirm_password'), 'trim|required');

			if($this->form_validation->run())
			{
				$password_data = array(
					'user_id' => $this->jempe_admin->user_id(),
					'user_password' => $this->input->post("user_password")
				);

				$this->jempe_db->update_except('jempe_users', 'user_id', $_POST, array('user_username','user_type','user_update'));
				$content["success"] = TRUE;
			}
			else
			{
				$content["error"] = $this->form_validation->error_string();
			}
		}
		else
		{
			$admin = $this->db->get_where('jempe_users', array('user_id' => $this->jempe_admin->user_id()));
			$_POST = $admin->row_array();
		}

		$content["template"] = "admin/admin_password" ;

		$content["title"] = $this->lang->line('jempe_page_admin_password');
		$this->load->view('admin/admin_popup', $content);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin page to add/edit/delete users
	 *
	 * @access	public
	 * @return	void
	 */
	function users()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('admin/login');
		}

		if( ! $this->jempe_admin->user_permission('users', 'read'))
		{
			redirect('admin');
		}

		$content["submenu"] = "users_menu";

		$content["admin_content"]["user_types"] = $this->jempe_admin->user_types_list();

		$this->db->select("user_id, user_first_name, user_last_name, user_username, user_email, '" .$this->lang->line('jempe_permission_hide_password')."' AS user_password, user_type", FALSE);
		$this->db->where('user_id >', 1);
		$users = $this->db->get_where('jempe_users', array('user_active'=>1));

		$user_fields = array(
			array(
				'field' 	=> 'user_id',
				'type' 		=> 'id'
			),
			array(
				'field' 	=> 'user_first_name',
				'type' 		=> 'text',
				'title' 	=> $this->lang->line('jempe_column_user_first_name'),
				'default' 	=> ''
			),
			array(
				'field' 	=> 'user_last_name',
				'type' 		=> 'text',
				'title'		=> $this->lang->line('jempe_column_user_last_name'),
				'default'	=> ''
			),
			array(
				'field'		=> 'user_username',
				'type'		=> 'text',
				'title'		=> $this->lang->line('jempe_column_user_username'),
				'default'	=> '',
				'style'		=> 'rel="jempe_name_field"'
			),
			array(
				'field'		=> 'user_email',
				'type'		=> 'text',
				'title'		=> $this->lang->line('jempe_column_user_email'),
				'default'	=> ''
			),
			array(
				'field'		=> 'user_password',
				'type'		=> 'text',
				'title'		=> $this->lang->line('jempe_column_user_password'),
				'default'	=> ''
			),
			array(
				'field'		=> 'user_type',
				'type'		=> 'list',
				'title'		=> $this->lang->line('jempe_column_user_type'),
				'options'	=> $this->jempe_admin->user_types_list(),
				'default'	=> 2
			)
		);

		if($this->jempe_admin->user_permission('users', 'write'))
		{
			$user_fields[] = array(
				'title'		=> '',
				'style'		=> 'style="width:110px;"',
				'field'		=> 'edit_row',
				'type'		=> 'edit',
				'label'		=> $this->lang->line('jempe_button_edit_user')
			);
	
			$user_fields[] = array(
				'title'		=> '',
				'style'		=> 'style="width:80px;"',
				'field'		=> 'delete_row',
				'type'		=> 'delete',
				'label'		=> $this->lang->line('jempe_button_delete_user')
			);
	
			$content["admin_content"]["add_user_button"] = $this->jempe_form->form_jquery_table_add_button($this->lang->line('jempe_button_add_user'), 'users_table');
			$content["admin_content"]["users"] = $this->jempe_form->form_jquery_table($users->result_array(), $user_fields, $this->jempe_admin->admin_url('edit_user.xml'), $this->jempe_admin->admin_url('delete_user.xml'), $this->lang->line('jempe_message_delete_user'), $this->lang->line('jempe_message_no_users'), 'users_table');
		}
		else
		{
			unset($user_fields[0]);

			if($users->num_rows() > 0)
			{
				$users = $users->result_array();

				for($i = 0; $i < count($users); $i++)
				{
					$users[$i]["user_type"] = $user_fields[3]["options"][$users[$i]["user_type"]] ;
				}

			}

			$content["admin_content"]["add_user_button"] = "";
			$content["admin_content"]["users"] = $this->jempe_form->results_table($users, $user_fields, $this->lang->line('jempe_message_no_users'));
		}

		$content["template"] = "admin/users";

		$content["title"] = $this->lang->line('jempe_page_users');
		$this->load->view('admin/admin', $content);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin page to edit users
	 *
	 * @access	public
	 * @return	void
	 */
	function edit_user()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('admin/login');
		}

		if( ! $this->jempe_admin->user_permission('users', 'write'))
		{
			redirect('admin');
		}

		$this->load->library('form_validation');

		if($this->input->post("user_password") == $this->lang->line('jempe_permission_hide_password'))
		{
			$_POST["user_password"] = "";
		}

		if($this->input->post('user_id') > 0)
		{
			$password_required = '';
		}
		else
		{
			$password_required = 'required|';
		}

		$this->form_validation->set_rules('user_first_name', $this->lang->line('jempe_column_user_first_name'), 'trim|required');
		$this->form_validation->set_rules('user_last_name', $this->lang->line('jempe_column_user_last_name'), 'trim|required');
		$this->form_validation->set_rules('user_username', $this->lang->line('jempe_column_user_username'), 'trim|required|alpha_dash|min_length[4]|max_length[15]|is_unique[user_username,jempe_users,user_id,'.$this->input->post('user_id').',user_active = 1]');
		$this->form_validation->set_rules('user_email', $this->lang->line('jempe_column_user_email'), 'trim|required|valid_email');
		$this->form_validation->set_rules('user_password', $this->lang->line('jempe_column_user_password'), 'trim|'.$password_required.'alpha_dash|min_length[6]|max_length[15]|md5');
		$this->form_validation->set_rules('user_type', $this->lang->line('jempe_column_user_type'), 'trim|required|integer|is_natural_no_zero');

		if($this->form_validation->run())
		{
			$user_types = $this->jempe_admin->user_types_list();

			$_POST["user_edited_by"] = $this->jempe_admin->user_id();

			if($this->input->post('user_id') > 0)
			{

				if( ! strlen($this->input->post('user_password')))
				{
					unset($_POST['user_password']);
				}

				$this->jempe_admin->edit_user($_POST);
			}
			else
			{
				$_POST["user_id"] = $this->jempe_admin->edit_user($_POST);
			}

			$_POST['user_password'] = $this->lang->line('jempe_permission_hide_password');
		}

		$results = $_POST;
		$results["error"] = $this->form_validation->error_string(' ', ' ');

		$this->output->set_output($this->jempe_form->xml_results($results));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin page to delete users
	 *
	 * @access	public
	 * @return	void
	 */
	function delete_user()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('admin/login');
		}

		if( ! $this->jempe_admin->user_permission('users','write'))
		{
			redirect('admin');
		}

		if($this->input->post('user_id') > 1)
		{
			$this->jempe_admin->edit_user(array('user_id' => $this->input->post('user_id'), 'user_active' => 0));
		}

		$this->output->set_output('<user><success>1</success></user>');
	}

	function user_types(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		if( !$this->jempe_admin->user_permission('users','read') )
			redirect('admin');

		$content["submenu"] = "users_menu";

		// delete user types only when any user belongs to that type
		if( $this->uri->segment(3) == "delete" && $this->uri->segment(4) > 1 && $this->jempe_admin->user_permission('users','write') ){
			$active_users = $this->db->get_where('jempe_users' , array( 'user_type'=>$this->uri->segment(4) , 'user_active'=>1 ) );

			if($active_users->num_rows() == 0 ){
				$this->db->limit(1);
				$this->db->where('user_type_id',$this->uri->segment(4));
				$this->db->update('jempe_user_types' , array('user_type_active'=>0));
			}
				
		}

		$this->db->where('user_type_id > 1');
		$user_types = $this->db->get_where('jempe_user_types', array('user_type_active'=>1)) ;

		$user_type_fields = array(
			array(
				'field'=>'user_type_name',
				'type'=>'text',
				'title'=>$this->lang->line('jempe_column_user_type_name'),
				'default'=>''
			)
		);

		if( $this->jempe_admin->user_permission('users','write') ){

			$content["admin_content"]["add_user_type"] = '<p>&nbsp;<button onclick="location=\'' .$this->jempe_admin->admin_url('edit_user_type' ) .'\'" class="jempe_button">' .$this->lang->line('jempe_button_add_user_type') .'</button></p>';
			$user_type_fields[] = array(
				'field'=>'user_type_id',
				'title'=>'',
				'label'=> '<a href="' .$this->jempe_admin->admin_url('edit_user_type/{field}' ) .'">' .$this->lang->line('jempe_button_edit_user_type') .'</a>'
			);
			$user_type_fields[] = array(
				'field'=>'delete',
				'title'=>''
			);
		}else{
			$content["admin_content"]["add_user_type"] = "";

			$user_type_fields[] = array(
				'field'=>'user_type_id',
				'title'=>'',
				'label'=> '<a href="' .$this->jempe_admin->admin_url('edit_user_type/{field}' ) .'">' .$this->lang->line('jempe_button_view_user_type_permissions') .'</a>'
			);
		}


		$user_types_list = array();

		if( $user_types->num_rows() > 0 ){
			foreach( $user_types->result_array() as $user_type ){
				if( $this->lang->line('jempe_user_type_' . $user_type["user_type_name"] ) )
					$user_type["user_type_name"] = $this->lang->line('jempe_user_type_' . $user_type["user_type_name"] );

				$active_users = $this->db->get_where('jempe_users' , array( 'user_type'=>$user_type["user_type_id"] , 'user_active'=>1 ) );

				if($active_users->num_rows() == 0 ){
					$user_type["delete"] = '<a href="' .$this->jempe_admin->admin_url('user_types/delete/' .$user_type["user_type_id"] ) .'">' .$this->lang->line('jempe_button_delete_user_type') .'</a>';
				}else{
					$user_type["delete"] = "";
				}

				$user_types_list[] = $user_type;

			}
		}

		$content["admin_content"]["user_types"] = $this->jempe_form->results_table( $user_types_list , $user_type_fields , $this->lang->line('jempe_message_no_users') );

		$content["template"] = "admin/user_types" ;

		$content["title"] = $this->lang->line('jempe_page_users');
		$this->load->view('admin/admin' , $content);

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Admin page to Edit user types
	 *
	 * @access	public
	 * @return	void
	 */
	function edit_user_type()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		if( ! $this->jempe_admin->user_permission('users','read'))
		{
			redirect('admin');
		}


		if( ! $this->jempe_admin->user_permission('users','write'))
		{
			$this->jempe_form->verify_form = true;
			$this->jempe_form->radiomultiple_template = array(
				"radio_open" => '' ,
				"radio_row" => '<td style="text-align:center;">{name}</td>' ,
				"radio_close" => ''
			);
		}
		else
		{
			$this->jempe_form->radiomultiple_template = array(
				"radio_open" => '' ,
				"radio_row" => '<td style="text-align:center;">{value}</td>' ,
				"radio_close" => ''
			);
		}

		if($this->uri->segment(3) == 1)
			redirect('admin/user_types');

		$this->load->library('form_validation');

		$content["submenu"] = "users_menu";
		$content["breadcrumb"] = array(
			array('url'=>'user_types' , 'name'=>$this->lang->line('jempe_menu_user_types') ),
			array('url'=>'edit_user_type/' .$this->uri->segment(3) , 'name'=>$this->lang->line('jempe_menu_edit_user_type') )
		);

		$content["admin_content"]["permissions"] = array();
		$permissions = $this->db->get('jempe_permissions' );

		$permissions = $permissions->result_array();

		$this->form_validation->set_rules('user_type_name' , $this->lang->line('jempe_column_user_type_name') , 'trim|required|is_unique[user_type_name,jempe_user_types,user_type_id,' .$this->input->post('user_type_id') .',user_type_active = 1]');

		if($this->form_validation->run() && $this->jempe_admin->user_permission('users','write'))
		{
			$content["admin_content"]["success"] = TRUE;

			$this->jempe_form->verify_form = TRUE;

			$this->jempe_form->radiomultiple_template = array(
				"radio_open" => '',
				"radio_row" => '<td style="text-align:center;">{name}</td>',
				"radio_close" => ''
			);

			$_POST['user_type_edited_by'] = $this->jempe_admin->user_id();

			if($this->input->post('user_type_id') > 0)
			{
				$user_type_id = $this->input->post('user_type_id');
				$this->jempe_db->update_except('jempe_user_types', 'user_type_id', $_POST, array());
			}
			else
			{
				$user_type_id = $this->jempe_db->insert_except('jempe_user_types', $_POST, array());
			}

			if($user_type_id > 1)
			{
				$this->db->limit(count($permissions));
				$this->db->delete('jempe_user_type_permissions' , array('utp_user'=>$user_type_id));

				$data["utp_user"] = $user_type_id;

				foreach($permissions as $permission)
				{
					$data["utp_permission"] = $permission["permission_id"];
					$data["utp_permission_value"] = $this->input->post("permission_".$permission["permission_id"]);

					$this->db->insert('jempe_user_type_permissions', $data);
				}

				$this->db->select('structure_id');
				$this->db->where('sb_structure = structure_id');
				$this->db->where('structure_id = content_id');
				$this->db->from('jempe_structure_bind');
				$this->db->from('jempe_structure');
				$category_pages = $this->db->get_where('jempe_content', array('content_is_category' => 1));

				if($category_pages->num_rows() > 0)
				{
					$category_pages = $category_pages->result_array();

					$category_pages[] = array('structure_id' => 0);

					$this->db->delete('jempe_user_type_page_permissions', array('utpp_user_type'=> $user_type_id));

					foreach($category_pages as $category_page)
					{
						if($this->input->post('new_'.$category_page["structure_id"]) !== FALSE)
						{
							$permission_data = array(
								'utpp_structure' => $category_page["structure_id"],
								'utpp_new' => (int)$this->input->post('new_'.$category_page["structure_id"]),
								'utpp_edit' => (int)$this->input->post('edit_' .$category_page["structure_id"]),
								'utpp_recur' => (int)$this->input->post('recur_' .$category_page["structure_id"]),
								'utpp_user_type' => $user_type_id
							);

							$this->db->insert('jempe_user_type_page_permissions', $permission_data);
						}
					}
				}

				$this->db->select('structure_id');
				$this->db->where('sb_structure = structure_id');
				$this->db->where('structure_id = content_id');
				$this->db->from('jempe_structure_bind');
				$this->db->from('jempe_structure');
				$non_category_pages = $this->db->get_where('jempe_content', array('content_is_category' => 0));

				if($non_category_pages->num_rows() > 0)
				{
					foreach($non_category_pages->result_array() as $non_category_page)
					{
						$this->jempe_admin->update_page_permissions($non_category_page["structure_id"]);
					}
				}
			}	
		}

		if($this->input->post('user_type_name') === FALSE)
		{
			if($this->uri->segment(3) > 0)
			{
				$user_type = $this->db->get_where('jempe_user_types', array('user_type_id' => $this->uri->segment(3)));
	
				$_POST = $user_type->row_array();

				$user_type_page_permissions = $this->db->get_where( 'jempe_user_type_page_permissions' , array('utpp_user_type' => $this->uri->segment(3)));

				if($user_type_page_permissions->num_rows() > 0)
				{
					foreach($user_type_page_permissions->result_array() as $page_permission)
					{
						$_POST["new_".$page_permission["utpp_structure"]] = $page_permission["utpp_new"];
						$_POST["edit_".$page_permission["utpp_structure"]] = $page_permission["utpp_edit"];
						$_POST["recur_".$page_permission["utpp_structure"]] = $page_permission["utpp_recur"];
					}
				}
			}
			else
			{
				$_POST = $this->jempe_db->blank_fields('jempe_user_types');
			}
		}

		foreach($permissions as $permission){

			if( $this->input->post('permission_' .$permission["permission_id"] ) === false ){

				if( $this->uri->segment(3) > 0 ){

					$_POST['permission_' .$permission["permission_id"]] = $this->jempe_admin->user_permission( $permission["permission_name"] , false , $this->uri->segment(3) ) ;
				}else
					$_POST['permission_' .$permission["permission_id"]] = 0;

			}

			if($this->lang->line('jempe_permission_' . $permission["permission_name"] ) )
				$permission["permission_name"] = $this->lang->line('jempe_permission_' . $permission["permission_name"] );

			$content["admin_content"]["permissions"][] = $permission;
		}


		$content["admin_content"]["permission_types"] = array( '0'=>$this->lang->line('jempe_column_no_access') , '1'=>$this->lang->line('jempe_column_read') , '2'=>$this->lang->line('jempe_column_write'));

		$content["template"] = "admin/edit_user_type" ;

		$pages_tree = $this->jempe_cms->pages_tree(0);

		$this->page_permissions = array();

		$this->load->helper('text');
		$permissions_tree = $this->_permissions_tree( $pages_tree );

		if( !$this->jempe_form->verify_form ){
			$this->jempe_form->jquery_functions[] = " 
			$('.new_page').click( function(){
	
				var page_id = $(this).attr('rel');
	
				if( $('#new_' + page_id ).val() == 1){
					$('#new_' + page_id ).val(0);
					$('#permissions_tree .new_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_new_disabled.png');
				}else{
					$('#new_' + page_id ).val(1);
					$('#permissions_tree .new_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_new_enabled.png');
				} 
	
			});
			$('.edit_page').click( function(){
	
				var page_id = $(this).attr('rel');
	
				if( $('#edit_' + page_id ).val() == 1){
					$('#edit_' + page_id ).val(0);
					$('#permissions_tree .edit_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_edit_disabled.png');
				}else{
					$('#edit_' + page_id ).val(1);
					$('#permissions_tree .edit_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_edit_enabled.png');
				} 
	
			}); 
			$('.recur_page').click( function(){
	
				var page_id = $(this).attr('rel');
	
				if( $('#recur_' + page_id ).val() == 1){
					$('#recur_' + page_id ).val(0);
					$('#permissions_tree .recur_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_recur_disabled.png');
	
					$('#permissions_tree .recur_page_' + page_id).parent().find('.recur_page').each(function (i){
						var recur_id = this.className;
						recur_id = recur_id.replace('recur_page recur_page_','');
	
						$('.recur_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_recur_disabled.png');
	
						$('#recur_' + recur_id).val(0);
					});
	
				}else{
					$('#recur_' + page_id ).val(1);
					$('#permissions_tree .recur_page_' + page_id).attr( 'src' , '" .static_url() ."images/icon_recur_enabled.png');
	
					var new_value = $('#new_' + page_id).val();
					var edit_value = $('#edit_' + page_id).val();
	
					$('#permissions_tree .recur_page_' + page_id).parent().find('.recur_page').each(function (i){
						var recur_id = this.className;
						recur_id = recur_id.replace('recur_page recur_page_','');
	
						$('#recur_' + recur_id ).val(1);
						$('#new_' + recur_id).val(new_value);
						$('#edit_' + recur_id).val(edit_value);
	
						if( new_value == 1 ){
							$('.new_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_new_enabled.png');
						}else{
							$('.new_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_new_disabled.png');
						}
	
						if( edit_value == 1 ){
							$('.edit_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_edit_enabled.png');
						}else{
							$('.edit_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_edit_disabled.png');
						}
	
						$('.recur_page_'+ recur_id).attr( 'src' , '" .static_url() ."images/icon_recur_enabled.png');
	
					});
	
				} 
	
			}); ";

		}

		$content["admin_content"]["permissions_tree"] = $permissions_tree ;

		$content["title"] = $this->lang->line('jempe_page_users');
		$this->load->view('admin/admin' , $content);

	}

	function _permissions_tree( $tree ){

		$permissions_tree = '<ul>';

		foreach( $tree as $page_id=>$value){

			if( is_array( $value ) ){
				$children = $this->_permissions_tree($value);
			}else{
				$children = '';
			}

			if( $page_id > 0 ){
				$page_info = $this->db->get_where('jempe_content' , array('content_id'=>$page_id) );
				$page_info = $this->jempe_cms->field_names($page_info->row_array());
			}else{
				$page_info['link_name'] = $this->lang->line('jempe_category_general');
			}

			if( !isset($this->page_permissions[$page_id] )){
				$new_page_input = $this->jempe_form->form_hidden('new_' .$page_id , $this->input->post('new_' .$page_id) , ' class="new_input" id="new_' .$page_id .'" rel="' .$page_id .'" ' );	
				$edit_page_input = $this->jempe_form->form_hidden('edit_' .$page_id , $this->input->post('edit_' .$page_id) , ' class="edit_input" id="edit_' .$page_id .'" rel="' .$page_id .'" ' );
				$recur_page_input = $this->jempe_form->form_hidden('recur_' .$page_id , $this->input->post('recur_' .$page_id) , ' class="recur_input" id="recur_' .$page_id .'" rel="' .$page_id .'" ' );
			}else{
				$new_page_input = "";
				$edit_page_input = "";
				$recur_page_input = "";
			}

			if( $this->input->post('new_' .$page_id) == 1 ){
				$new_icon = "enabled";
			}else{
				$new_icon = "disabled";
			}

			if( $this->input->post('edit_' .$page_id) == 1 ){
				$edit_icon = "enabled";
			}else{
				$edit_icon = "disabled";
			}

			if( $this->input->post('recur_' .$page_id) == 1 ){
				$recur_icon = "enabled";
			}else{
				$recur_icon = "disabled";
			}

			if( isset($page_info["title"]) &&  strlen(trim($page_info["title"]) ) ){
				$page_title = " (" .character_limiter($page_info["title"], 50) .")";
			}else{
				$page_title = "";
			}

			$permissions_tree .= '<li> &raquo; <img src="' .static_url() .'images/icon_new_' .$new_icon .'.png" class="new_page new_page_' .$page_id .'" rel="' .$page_id .'" />' . $new_page_input  .'<img src="' .static_url() .'images/icon_edit_' .$edit_icon .'.png"  rel="' .$page_id .'" class="edit_page edit_page_' .$page_id .'" />' .$edit_page_input .'<img src="' .static_url() .'images/icon_recur_' .$recur_icon .'.png"  rel="' .$page_id .'" class="recur_page recur_page_' .$page_id .'"  />&nbsp;&nbsp;' .$recur_page_input .$page_info["link_name"] .$page_title   .$children .'</li>';

			$this->page_permissions[$page_id] = 1;

		}


		$permissions_tree .= '</ul>';

		return $permissions_tree;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Render Image Manager
	 *
	 * @access	public
	 * @return	void
	 */	
	function image_manager()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}


		if($this->uri->segment(2) == "file_manager")
		{
			$labels_prefix = $content["manager_content"]["labels_prefix"] = "file_manager";
			$files_path = str_replace(base_url(), "jempe_base_url", upload_url()).$this->jempe_cms->upload_docs_config["upload_path"]."files/";
		}
		else
		{
			$labels_prefix = $content["manager_content"]["labels_prefix"] = "jempe_manager";
		}


		$this->jempe_form->jquery_fancybox = true;

		if( ! ($this->jempe_admin->user_permission('images_other_users', 'read') OR $this->jempe_admin->user_permission('images_same_user_type', 'read') OR $this->jempe_admin->user_permission('own_images', 'read')))
		{
			redirect('admin');
		}

		if($this->uri->segment(3) == "field" && strlen($this->uri->segment(4)))
		{
			$this->session->set_userdata("jempe_image_field", $this->uri->segment(4));
		}

		$this->jempe_form->javascript_functions[] = '
			var insert_image_ids = new Array();
		';

		$field_info = $this->jempe_cms->field_info($this->session->userdata('jempe_image_field'));

		$content['thumbs'] = $field_info["thumb"];

		$this->load->library('form_validation');

		if($this->uri->segment(2) == "file_manager")
		{
			$upload_config = $this->jempe_cms->upload_docs_config;
		}
		else
		{
			$upload_config = $this->jempe_cms->upload_images_config;
		}


		if($this->input->post('quantity') > 0 && $this->jempe_admin->user_permission('own_images','write'))
		{
			$this->form_validation->set_rules('jempe_tags', $this->lang->line('jempe_manager_tags'), 'trim|required');
			$this->form_validation->set_rules('quantity', 'quantity', 'trim|required|integer|is_natural_no_zero');

			if($this->form_validation->run())
			{
				$_POST["jempe_tags"] = $this->_validate_tags($this->input->post('jempe_tags'));

				if(strpos($this->input->post('jempe_tags'), ',') !== FALSE)
				{
					$jempe_tags = explode(",", $this->input->post('jempe_tags'));
				}
				else
				{
					$jempe_tags[0] = $this->input->post('jempe_tags');
				}

				$upload_photos_config = $upload_config;

				if($this->uri->segment(2) == "file_manager")
				{
					$upload_photos_config["upload_path"] = upload_path().$upload_photos_config["upload_path"]."files/";
				}
				else
				{
					$upload_photos_config["upload_path"] = upload_path().$upload_photos_config["upload_path"]."original/";
				}

				$this->load->library('upload');
	
				$upload_errors = array();
				$uploaded_images = array();
				$all_images = array();
	
				for($i = 1; $i <= $this->input->post('quantity'); $i++)
				{
					$this->upload->initialize($upload_photos_config);

					if($this->upload->do_upload('image_'.$i))
					{
						$data = $this->upload->data();

						unset($_POST["image_id"]);

						$this->_process_uploaded_image($data);

						$uploaded_images[] = $data['file_name'];

						if( ! isset($first_image_id))
						{
							$first_image_id = $this->input->post('image_id');
						}

						$this->_process_tags($jempe_tags, $this->input->post('image_id'));

						if($field_info["type"] == "htmlarea")
						{
							$all_images[] = $this->input->post('image_id');
						}
					}
					else
					{
						$this->jempe_form->jquery_functions[] = '
							alert("'.$this->upload->display_errors('', '').'");
						';
						$upload_errors[] = $this->upload->display_errors();
					}
				}

				for($i = 0; $i < count($all_images); $i++)
				{
					$this->jempe_form->javascript_functions[] = '
						insert_image_ids['.$i.'] = '.$all_images[$i].';
					';
				}

				if(count($uploaded_images) > 0 && count($upload_errors) == 0)
				{
					if($this->uri->segment(2) == "file_manager")
					{
						$this->jempe_form->jquery_functions[] = '
							insert_jempe_link("'.$files_path.$uploaded_images[0].'");
						';
					}
					else
					{
						if($field_info["type"] == "htmlarea")
						{
							if(is_array($field_info["thumb"]))
							{
								$this->jempe_form->jquery_functions[] = '
									show_thumb_popup()
								';
							}
							else
							{
								$this->jempe_form->jquery_functions[] = '
									insert_tinymce_images(insert_image_ids, "'.$field_info["thumb"].'");
								';
							}
						}
						else
						{
							$image_thumbs = $this->jempe_cms->thumbs_info($first_image_id);

							$this->jempe_form->jquery_functions[] = '
								insert_image('.$first_image_id.', "'.$image_thumbs[$field_info['thumb']]['url'].'");
							';
						}
					}
				}
				else if(count($upload_errors))
				{
					$content['manager_content']['upload_errors'] = $upload_errors;
				}
			}
		}

		$image_fields = array(
			array(
				'field'=>'delete_images',
				'title'=>'<input type="checkbox" name="all_images" id="all_images" >',
				'style'=>'style="width:30px;text-align:center;"'
			),
		);

		if($this->uri->segment(2) != "file_manager")
		{
			$image_fields[] = array(
				'field'=>'image_file',
				'title'=>$this->lang->line($labels_prefix.'_column_image'),
				'style'=>'style="width:70px;text-align:center;"'
			);

			$image_fields[] = array(
				'field'=>'image_name',
				'title'=>$this->lang->line($labels_prefix.'_column_image_name'),
				'style'=>'style="width:305px;"'
			);
		}
		else
		{
			$image_fields[] = array(
				'field'=>'image_file',
				'title'=>$this->lang->line($labels_prefix.'_column_image_name'),
				'style'=>'style="width:305px;"',
				'label' => '<a href="javascript:void(0);" onclick="insert_jempe_link(\''.$files_path.'{field}\')">{field}</a>'
			);
		}

		$image_fields[] = array(
			'field'=>'image_timestamp',
			'title'=>$this->lang->line($labels_prefix.'_column_image_updated'),
			'style'=>'style="width:150px;"'
		);

		if($this->uri->segment(2) != "file_manager")
		{
			$image_fields[] = array(
				'field'=>'image_id',
				'title'=>' ',
				'label'=>'<a href="' .$this->jempe_admin->admin_url('image_upload/{field}' ) .'"><img border="0" src="' .static_url() .'images/icons/pencil_24.png" alt="' .$this->lang->line('jempe_manager_edit_image') .'" title="' .$this->lang->line('jempe_manager_edit_image') .'" ></a>',
				'style'=>'style="width:35px;text-align:center;"'
			);
		}

		$this->jempe_form->jquery_functions[] = '
			$("#all_images").click(function(){

				var all_images_value = $(this).attr("checked");
				$("input[name=\'image_id\']").attr("checked" , all_images_value );
			});
		';

		$this->jempe_form->jquery_functions[] = '
			$("#delete_images").click(function(){

				var selected_images = new Object;
				var i = 1;

				$("input:checked").each(function () {
                			selected_images[\'image\' + i] = $(this).val() ;
					i++;
				});

				$.post("'.$this->jempe_admin->admin_url('delete_images.xml').'", selected_images , function(xml) {
	
					$("success", xml).each(function () {

						var image_id = $(this).text();

						$("input[class=\'image" + image_id +"\']").parent().parent().remove();
					});
				});
			});
		';

		$this->jempe_form->jquery_functions[] = '
			$("a.jempe_tag").click(function(){

				$("input[name=\'jempe_search\']").val("tag:" + $(this).attr("rel") );
				$("#jempe_search_form").submit();
			});
		';

		if( ! $this->jempe_admin->user_permission('images_other_users', 'read') && $this->jempe_admin->user_permission('images_same_user_type', 'read'))
		{
			$same_user_type_permission = TRUE;
		}

		if( ! $this->jempe_admin->user_permission('images_other_users', 'read') && ! $this->jempe_admin->user_permission('images_same_user_type', 'read') && $this->jempe_admin->user_permission('own_images','read'))
		{
			$own_images_permission = TRUE;
		}

		if(isset($uploaded_images) && count($uploaded_images))
		{
			$uploaded_images_query = "(";

			foreach($uploaded_images as $uploaded_image)
			{
				if($uploaded_images_query != "(")
				{
					$uploaded_images_query .= " OR ";
				}

				$uploaded_images_query .="image_file = '" .$uploaded_image ."'";
			}

			$uploaded_images_query .= ")";

			$this->db->where($uploaded_images_query);
		}

		$_POST["jempe_search"] = trim($this->input->post('jempe_search'));

		if($this->input->post('jempe_search'))
		{
			if(strpos($this->input->post('jempe_search'), 'tag:') === FALSE)
			{
				$this->db->like('image_name', $this->input->post('jempe_search'));
			}
			else
			{
				$this->db->where('tag_name', str_replace("tag:", "", $this->input->post('jempe_search')));
				$this->db->from('jempe_image_tags');
				$this->db->from('jempe_tags');
				$this->db->where('it_image = image_id');
				$this->db->where('it_tag = tag_id');
			}
		}

		if($this->uri->segment(5) > 0)
		{
			$this->db->where('image_id', $this->uri->segment(5));
		}

		if(isset($same_user_type_permission))
		{
			$this->db->from('jempe_users');
			$this->db->from('jempe_user_types');
			$this->db->where('user_type_id = user_type');
			$this->db->where('user_id = image_user');
			$this->db->where('user_type_id', $this->session->userdata('user_type'));
		}

		if(isset($own_images_permission))
		{
			$this->db->where('image_user', $this->jempe_admin->user_id());
		}

		if($this->uri->segment(2) == "file_manager")
		{
			$image_type = "file";
		}
		else
		{
			$image_type = "image";
		}

		$this->db->order_by('image_timestamp', 'desc');
		$images_list = $this->db->get_where('jempe_images', array('image_active' => 1, 'image_type' => $image_type));

		$result_images = array();

		if($images_list->num_rows() > 0)
		{
			foreach($images_list->result_array() as $image_of_list)
			{
				$image_thumbs = $this->jempe_cms->thumbs_info($image_of_list['image_id']);

				foreach($image_thumbs as $thumb_type => $thumb_data)
				{
					$thumb_js_data = '';

					foreach($thumb_data as $thumb_var => $thumb_var_value)
					{
						if($thumb_js_data != '')
						{
							$thumb_js_data .= ', ';
						}

						$thumb_js_data .= '
							"'.$thumb_var.'" : "'.$thumb_var_value.'"';
					}

					$this->jempe_form->javascript_functions[] = '
						var image_'.$image_of_list['image_id'].'_'.$thumb_type.' = {
							'.$thumb_js_data.'
						}
					';
				}

				if($this->uri->segment(2) != "file_manager")
				{
					$image_of_list['image_file'] = '<a class="jempe_select_image" href="javascript:void(0);" rel="'.$image_thumbs[$field_info["thumb"]]['url'].'" ><img title="' .$this->lang->line('jempe_manager_select_image') .'" src="'.$image_thumbs['jempe']['url'].'?time=' .time() .'"></a>';
				}

				if($this->jempe_admin->can_delete_image($image_of_list["image_id"]))
				{
					$image_of_list["delete_images"] = '<input type="checkbox" name="image_id" value="'.$image_of_list["image_id"].'"  class="image'.$image_of_list["image_id"].'" >';
				}
				else
				{
					$image_of_list["delete_images"] = '<img src="'.static_url().'images/icons/lock_24.png" />'.'<input type="hidden" name="image_id" value="'.$image_of_list["image_id"].'"  class="image'.$image_of_list["image_id"].'" >';
				}

				$result_images[] = $image_of_list;
			}
		}

		$content["manager_content"]["manager_files"] = $this->jempe_form->results_table($result_images, $image_fields, $this->lang->line($labels_prefix.'_no_images'));

		if(isset($own_images_permission))
		{
			$this->db->where('image_user', $this->jempe_admin->user_id());
		}

		$this->db->select('tag_name , count(*) AS cuantos', FALSE);
		$this->db->order_by('tag_name', 'asc');
		$this->db->group_by('tag_id');
		$this->db->where('it_image = image_id');
		$this->db->where('it_tag = tag_id');
		$this->db->where('image_active', 1);
		$this->db->where('image_type', $image_type);
		$this->db->from('jempe_images');
		$this->db->from('jempe_image_tags');
		$tags = $this->db->get('jempe_tags');
		$content["manager_content"]["tags"] = $tags->result_array();

		$available_tags = array();

		foreach($content["manager_content"]["tags"] as $available_tag)
		{
			$available_tags[] = '"'.$available_tag['tag_name'].'"';
		}

		$this->jempe_form->form_jquery_ui('ui');
		$this->jempe_form->jquery_css['ui'] = static_url()."jquery/css/ui-darkness/jquery-ui-1.8.2.custom.css";

		$this->jempe_form->jquery_functions[] = '
		var availableTags = [
			'.implode(', ', $available_tags).'
		];
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		$("#jempe_tags")
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).data( "autocomplete" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				source: function( request, response ) {
					response( $.ui.autocomplete.filter(
						availableTags, extractLast( request.term ) ) );
				},
				focus: function() {
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );

					terms.pop();

					terms.push( ui.item.value );

					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			});
		';

		if($this->jempe_admin->user_permission('own_images', 'write'))
		{
			$this->jempe_form->jquery_functions[] = '
				$("#new_upload a").click( function(){
					var upload_fields = $("#upload_quantity").val();
	
					var $lastupload = $("input[name=\'image_" + upload_fields + "\']");
					upload_fields++;
	
					$("#upload_quantity").val( upload_fields );
	
					new_upload = $lastupload.parent().clone().appendTo("#upload_fields");
	
					new_upload.find("input").attr("name" , "image_" + upload_fields );
					new_upload.find("input").val(\'\');
	
				});
			';
			$this->jempe_form->jquery_functions[] = '
				$("#upload_images_form").submit(function(){
					if( $("#jempe_tags").val() == "" ){
						alert("' .$this->lang->line('jempe_manager_error_select_tag') .'");
						return false;
					}
				});
			';
			$content["manager_content"]["upload_fields"] = TRUE;
		}

		if($field_info["type"] == "htmlarea")
		{
			if(is_array($field_info["thumb"]))
			{
				$this->jempe_form->jquery_functions[] = '
					$(".jempe_select_image").click(function (){
						insert_image_ids[0] = $(this).parent().parent().find("input").val();

						show_thumb_popup();
					});
				';
			}
			else
			{
				$this->jempe_form->jquery_functions[] = '
					$(".jempe_select_image").click(function (){

						insert_image_ids[0] = $(this).parent().parent().find("input").val();

						insert_tinymce_images(insert_image_ids, "'.$field_info["thumb"].'");
					});
				';
			}
		}
		else
		{
			$this->jempe_form->jquery_functions[] = '
				$(".jempe_select_image").click(
					function()
					{
						var selected_image_id = $(this).parent().parent().find("input").val();

						insert_image(selected_image_id, $(this).attr(\'rel\'));
					}
				);
			';
		}

		$content["template"] = "admin/image_manager/images_list";
		$content["title"] = $this->lang->line('jempe_page_image_manager');
		$this->load->view('admin/imagemanager' , $content);
	}

	function delete_images(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		$images = array();

		if(count($_POST))
		{
			foreach($_POST as $image_id)
			{
				if( $image_id > 0 && $this->jempe_admin->can_delete_image( $image_id))
				{
					$images[] = $image_id;

					$this->db->limit(1);
					$this->db->where('image_id', $image_id);
					$this->db->update('jempe_images' , array('image_active' => 0));
					foreach($this->jempe_cms->images_thumbs as $thumb_name => $thumb_config)
					{
						$this->jempe_form->delete_thumb($image_id, $thumb_name);
					}
				}
			}
		}
		$output = '<results>';

		if(count($images))
		{
			foreach($images as $image)
			{
				$output .= '<success>'.$image.'</success>';
			}
		}

		$output .= '</results>';

		$this->output->set_output($output);
	}

	function image_upload(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		$this->load->library('form_validation');
		$this->load->helper('directory');

		$upload_config = $this->jempe_cms->upload_images_config;
		
		$thumb_folder = $upload_config["upload_path"] ."thumbs/";

		$upload_config["upload_path"] = upload_path().$upload_config["upload_path"] ."original/";

		$this->load->library('upload', $upload_config );
	
		$content["manager_content"] = "";

		if( $this->input->post('upload') !== false ){
			if ( ! $this->upload->do_upload('jempe_image')){
				$content["manager_content"]["error"] = $this->upload->display_errors();
			}else{
				$data = $this->upload->data();
				
				$this->_process_uploaded_image($data);

				$content["manager_content"]["image"] = $data['file_name'];
				$content["manager_content"]["success"] = true;
			}
		}else{

			if( $this->input->post('jempe_tags') )
				$_POST["jempe_tags"] = $this->_validate_tags( $this->input->post('jempe_tags') );

			if( $this->input->post('image_name') !== false ){
				$this->form_validation->set_rules( 'image_name'  , $this->lang->line('jempe_manager_column_image_name') , 'trim|required' );
				$this->form_validation->set_rules( 'jempe_tags'  , $this->lang->line('jempe_manager_tags') , 'trim|required' );

				$image_info = $this->db->get_where('jempe_images' , array('image_id'=>$this->uri->segment(3)) );
				$image_info = $image_info->row_array();

				if( $this->form_validation->run() && $this->input->post('image_id') > 0 ){

					if( strpos( $this->input->post('jempe_tags') , ',' ) === false ){
						$tags = array( $this->input->post('jempe_tags') );
					}else{
						$tags = explode(',' , $this->input->post('jempe_tags') );
					}

					$this->jempe_db->update_except( 'jempe_images' , 'image_id' , $_POST , array( 'image_user' , 'image_file' ) );

					$image_id = $this->input->post('image_id');


					$this->db->delete( 'jempe_image_tags' , array( 'it_image' => $image_id ) );

					$this->_process_tags( $tags , $image_id );

					redirect('admin/image_manager');

				}

			}else{
				if($this->input->post('image_id') ){
					$image_info = $this->db->get_where('jempe_images' , array('image_id'=>$this->input->post('image_id')) );	
				}else{
					if( $this->uri->segment(3) > 0 ){
						$image_info = $this->db->get_where('jempe_images' , array('image_id'=>$this->uri->segment(3)) );
					}
				}

				if( isset( $image_info ) ){
					$image_info = $image_info->row_array();
					$_POST = $image_info;

					$this->db->where('it_image' , $this->input->post('image_id'));
					$this->db->where('it_tag = tag_id');
					$this->db->from('jempe_image_tags');
					$jempe_tags = $this->db->get( 'jempe_tags' );

					$jempe_tags = $this->jempe_db->results_to_list( $jempe_tags->result_array() , 'tag_name' , 'tag_name' ); 

					$_POST["jempe_tags"] = implode(',' , $jempe_tags );

				}
				
			}

			if( isset( $image_info["image_file"] ) )
				$content["manager_content"]["image"] = $image_info["image_file"];

		}

		if( $this->input->post('image_id') ){
			$this->jempe_form->jquery_functions[] = '
				$("#replace_image").click(function(){
					$(".upload_image").show();
					$(".edit_image").hide();
				});
			';

			$this->jempe_form->form_jquery_ui( 'autocomplete' );
			
			$this->jempe_form->jquery_functions[] = '
				$("#jempe_tags").autocomplete("' .$this->jempe_admin->admin_url('image_tags') .'", {
					minChars: 0, 
					highlight: false,
					scroll: true, 
					scrollHeight: 300, 
					formatItem: function(data, i, n, value) { 
						return data;
					},
					formatResult: function(data, value) { 
						return value;
					}
				});
			';

		}

		$this->jempe_form->jquery_functions[] = '
			$("#cancel_edit").click(function(){
				window.location = "' .$this->jempe_admin->admin_url('image_manager/') .'";
			});
		';


		$content["template"] = "admin/image_manager/image_upload";
		$content["title"] = $this->lang->line('jempe_page_image_manager');
		$this->load->view('admin/imagemanager' , $content);
	}

	function image_tags(){

		if( strpos( $this->input->post('q') , ',' ) === false ){
			$tag = $this->input->post('q');
			$result = '';
		}else{
			$tags = explode(',',$this->input->post('q') );
			$tag = array_pop($tags);
			$result = implode(',' , $tags ) .', ';
		}
			
		$tag = trim($tag);

		$this->db->like( 'tag_name' , $tag , 'after' );
		$jempe_tags = $this->db->get('jempe_tags');
	
		if( $jempe_tags->num_rows() > 0 ){
			foreach($jempe_tags->result_array() as $jempe_tag){
				echo $result .$jempe_tag["tag_name"] ."\n";
			}
		}
	}

	function _validate_tags( $tags ){

		$tags = trim($tags);

		if( strpos($tags , ',' ) ){
			$tags_array = explode("," , $tags );
		}else
			return $tags;

		$tags_array = array_unique($tags_array);

		$result = array();

		foreach( $tags_array as $tag ){
			$tag = trim($tag);

			if( strlen($tag) )
				$result[] = $tag;
		}

		return implode( "," , $result );

	}

	function _process_tags( $tags , $image_id ){
		foreach( $tags as $tag){
			$tag = trim( $tag );
			$jempe_tag = $this->db->get_where( 'jempe_tags' , array( 'tag_name' => $tag ) );

			if( $jempe_tag->num_rows() > 0 ){
				$jempe_tag = $jempe_tag->row_array();
				$tag_id = $jempe_tag["tag_id"];
			}else{
				$tag_id = $this->jempe_db->insert_except( 'jempe_tags' , array('tag_name'=>  $tag ) , array() );
			}

			$this->db->insert('jempe_image_tags' , array( 'it_tag'=>$tag_id , 'it_image' => $image_id ) );

		}
	}

	function _process_uploaded_image($data)
	{
		$image_extension = strtolower(str_replace('.', '', $data['file_ext']));

		$image_data = array(
			'image_name' => $data['raw_name'],
			'image_file' => $data['file_name'],
			'image_user' => $this->jempe_admin->user_id(),
			'image_extension' => $image_extension
		);

		if($this->uri->segment(2) != "file_manager")
		{
			$image_data["image_type"] = "image";
		}
		else
		{
			$image_data["image_type"] = "file";
		}

		if($this->input->post('image_id') > 0)
		{
			unset($image_data["image_user"]);

			$image_data["image_id"] = $this->input->post('image_id');
			$this->jempe_db->update_except('jempe_images', 'image_id', $image_data, array());
		}
		else
		{
			$_POST["image_id"] = $this->jempe_db->insert_except('jempe_images', $image_data, array());
		}

		$original_thumb_data = array(
			'thumb_type' => 'original',
			'thumb_image' => $this->input->post('image_id'),
			'thumb_name' => $data['raw_name'],
			'thumb_url' => upload_url(),
			'thumb_path' => str_replace(upload_path(), '', $data['full_path']),
			'thumb_filesize' => filesize($data['full_path'])
		);

		$thumb_size = getimagesize($data['full_path']);

		if($thumb_size !== FALSE)
		{
			$original_thumb_data['thumb_width'] = $thumb_size[0];
			$original_thumb_data['thumb_height'] = $thumb_size[1];
		}

		$write_db = $this->load->database('write', TRUE);

		$write_db->select('thumb_id');
		$thumb_exists = $write_db->get_where('jempe_thumbs', array('thumb_image' => $this->input->post('image_id'), 'thumb_type' => 'original'));

		if($thumb_exists->num_rows() > 0)
		{
			$thumb_exists = $thumb_exists->row_array();

			$original_thumb_data['thumb_id'] = $thumb_exists['thumb_id'];

			$this->jempe_db->update_except('jempe_thumbs', 'thumb_id', $original_thumb_data);
		}
		else
		{
			$original_thumb_data['thumb_id'] = $this->jempe_db->insert_except('jempe_thumbs', $original_thumb_data);
		}

		if(is_array($this->jempe_cms->execute_after_upload))
		{
			$post_upload_model = $this->jempe_cms->execute_after_upload['Model'];
			$post_upload_function = $this->jempe_cms->execute_after_upload['action'];

			$this->load->Model($post_upload_model);

			$this->$post_upload_model->$post_upload_function($original_thumb_data['thumb_id']);
		}

		if($thumb_size !== FALSE)
		{
			foreach($this->jempe_cms->images_thumbs as $thumb_name => $thumb_config)
			{
				$this->jempe_form->process_thumb($this->input->post('image_id'), $thumb_name);
			}
		}

		$_POST["image_name"] = $image_data['image_name'];
	}

	function image_thumb(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		if( !isset( $this->jempe_cms->images_thumbs[$this->uri->segment(4)] ) )
			redirect('admin/image_manager');
		

		$image = $this->db->get_where('jempe_images' , array('image_id' => $this->uri->segment(3) ) );
		$image = $image->row_array();	

		$upload_config = $this->jempe_cms->upload_images_config;
		
		$thumb_folder = upload_url() .$this->jempe_cms->upload_images_config["upload_path"] ."thumbs/" .$this->uri->segment(4) ."/";

		$content["image"] = upload_url() .$this->jempe_cms->upload_images_config["upload_path"] . 'original/' .$image["image_file"];

		$this->jempe_form->form_jquery_ui( 'crop' );

		$content["crop_setup"] = array();

		
		$image_size = getimagesize( upload_path().$this->jempe_cms->upload_images_config["upload_path"] . 'original/' .$image["image_file"] );

		$crop_window_size = 550;

		if( $image_size[0] > $image_size[1] ){
			if( $image_size[0] > $crop_window_size ){
				$content["resize"] = "width:" .$crop_window_size ."px;";
				$resize_ratio = $image_size[0] / $crop_window_size;
			}else{
				$resize_ratio = 1;
			}
		}else{
			if( $image_size[1] > $crop_window_size ){
				$content["resize"] = "height:" .$crop_window_size ."px;";
				$resize_ratio = $image_size[1] / $crop_window_size;
			}else{
				$resize_ratio = 1;
			}
		}

		if( $this->input->post('crop') !== false ){

			$parameters = array(
				'x_axis' => round($this->input->post('crop_x') * $resize_ratio ),
				'y_axis' => round($this->input->post('crop_y') * $resize_ratio ),
				'width' => round($this->input->post('crop_width') * $resize_ratio ),
				'height' => round($this->input->post('crop_height') * $resize_ratio )
			);

			$this->jempe_form->process_thumb( $this->uri->segment(3) , $this->uri->segment(4) , $parameters  );
			$content["success"] = true;
			$content["image"] = base_url() .$this->jempe_cms->upload_images_config["upload_path"] . 'thumbs/' .$this->uri->segment(4) .'/' .$image["image_file"];

		}
	
		$thumb_setup = $this->jempe_cms->images_thumbs[$this->uri->segment(4)];

		if( isset( $thumb_setup["width"] ) && isset( $thumb_setup["height"] ) ){
			$content["crop_setup"]["aspectRatio"] = $thumb_setup["width"] / $thumb_setup["height"] ;
			$content["crop_setup"]["minSize"] = array( ($thumb_setup["width"] / $resize_ratio ) , ( $thumb_setup["height"] / $resize_ratio ) );
		}else if( isset( $thumb_setup["width"] ) ){
			$content["crop_setup"]["minSize"] = array( ($thumb_setup["width"] / $resize_ratio ) , 1 );
		}else if( isset( $thumb_setup["heigth"] ) ){
			$content["crop_setup"]["minSize"] = array( 1 , ( $thumb_setup["height"] / $resize_ratio ) );
		}


		$content["title"] = $this->lang->line('jempe_page_image_manager');
		$this->load->view("admin/image_manager/images_thumb" , $content);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Process all image thumbs
	 *
	 * When a thumb size is created, it helps to create missing thumbs
	 *
	 * @access	public
	 * @return	void
	 */
	function process_all_thumbs()
	{
		if($this->jempe_admin->is_authenticated())
		{
			$this->db->select('image_id');
			$images = $this->db->get('jempe_images');
	
			if($images->num_rows() > 0)
			{
				foreach($images->result_array() AS $image)
				{
					foreach( $this->jempe_cms->images_thumbs AS $thumb_name=>$thumb_config)
					{
						$this->jempe_form->process_thumb($image['image_id'], $thumb_name);
					}
				}
			}
		}
	}

	function link_manager(){
		if(!$this->jempe_admin->is_authenticated())
			redirect('/admin/login');

		$this->load->view('admin/imagemanager');
	}

	function logout(){
		$this->jempe_admin->logout();
		redirect('/admin/login');
	}

	function sitemap(){

		$this->db->select('structure_id , DATE(content_timestamp) as modified');
		$this->db->from('jempe_content , jempe_structure , jempe_structure_bind ');
		$this->db->where('content_structure = structure_id' ); 
		$this->db->where('content_structure = sb_structure' ); 
		$paginas = $this->db->get();
		

		foreach($paginas->result_array() as $pagina){
			$pagina["modified"] = $pagina["modified"];

			if($pagina["structure_id"] == 1)
				$pagina["link"] = base_url();
			else
				$pagina["link"] = $this->jempe_cms->page_link($pagina["structure_id"]);
			$content['pages'][]= $pagina;
		}


		$this->load->view('admin/sitemap',$content);
	}

	function check_url($url,$db_field)
	{
		if(isset($_POST['sb_parent']) && count($_POST['sb_parent'])){
			// check if a page has the same parent and the same url
			foreach($_POST["sb_parent"] as $parent){
				$this->db->from('jempe_content , jempe_structure , jempe_structure_bind ');
				$this->db->where('content_structure = structure_id' ); 
				$this->db->where('content_structure = sb_structure' ); 
				$this->db->where($db_field,$url ); 
				if($this->input->post('structure_id') > 0)
					$this->db->where('structure_id !=' , $this->input->post('structure_id') ); 
				$this->db->where('sb_parent', $parent ); 
				$url_exists = $this->db->get();
	
				if($url_exists->num_rows() > 0){
		
					$this->form_validation->set_message('check_url', str_replace("[%url%]",$url,$this->lang->line("jempe_error_url") ) );
					return false;
				}
	
			}

		}else{
			$this->form_validation->set_message('check_url', $this->lang->line("jempe_error_parent")  );
			return false;
		}


			return TRUE;
	}

	function check_password(){
		$admin = $this->db->get_where('jempe_users',array('user_id'=>$this->input->post('user_id') , 'user_password'=>md5($this->input->post('old_password')) ));

		if($admin->num_rows() == 0){
			$this->form_validation->set_message('check_password', $this->lang->line('jempe_error_old_password')  );
			return false;
		}else{
			return true;
		}

	}

	function backup()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		$this->load->dbutil();
		$backup =& $this->dbutil->backup(); 
		$this->load->helper('file');

		$upload_config = $this->jempe_cms->upload_images_config;

		write_file(upload_path().$upload_config["upload_path"].'thumbs/temp/jempe_backup.gz', $backup); 
		$this->load->helper('download');
		force_download('jempe_backup.gz', $backup);
	}

	function cache_info()
	{
		if($this->jempe_admin->is_authenticated())
		{
			var_dump($this->cache->cache_info());
		}
	}

	function tinymce_links()
	{
		$output = 'var tinyMCELinkList = new Array(
		';

		$link_name_field = $this->jempe_cms->db_field_names(array("link_name"));

		$pages = $this->jempe_cms->child_pages('all', array("url", "title", "link_name"), 0, $this->db->count_all("jempe_content"), array($link_name_field => "asc"));

		if(count($pages))
		{
			for($i = 0; $i < count($pages); $i++)
			{
				if($i > 0)
				{
					$output .= ",\n";
				}

				$page = $pages[$i];

				$output .= '["'.$this->jempe_cms->clean_tags($page['link_name']).'", "jempe_url'.$page['structure_id'].'"]';
			}
		}

		$output .= ');';

		$this->output->set_output($output);
	}
}

// END Admin Controller

/* End of file admin.php */
/* Location: ./jempe/application/controllers/admin.php */