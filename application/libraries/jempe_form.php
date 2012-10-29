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
 * Jempe Form Library
 *
 * Class to create forms
 *
 * @package		Jempe
 * @subpackage	Libraries
 * @category	Forms
 * @author		Sucio Kastro
 * @link		http://jempe.org/documentation/libraries/form.html
 */

class Jempe_form {

	// This variable convert the form fields in text fields, useful to review form data
	public $verify_form = FALSE;

	public $jquery_image_manager = false;

	public $checkboxmultiple_template = array(
		"checkbox_open" => '<table>' ,
		"checkbox_row" => '<tr><td><label for="{option_id}">{name}</label></td><td>{value}</td></tr>' ,
		"checkbox_close" => '</table>'
	);

	public $radiomultiple_template = array(
		"radio_open" => '<table>' ,
		"radio_row" => '<tr><td>{name}</td><td>{value}</td></tr>' ,
		"radio_close" => '</table>'
	);

	public $results_table_template = array(
		"jquery_table_open" => '<table id="{id}" cellspacing="0" >' ,
		"table_open" => '<table>' ,
		"table_head_open" => "\n<thead>\n" ,
		"table_head_close" => "\n</thead>\n" ,
		"table_body_open" => "\n<tbody>\n" ,
		"table_body_close" => "\n</tbody>\n" ,
		"table_title_row_open" => "<tr>\n" ,
		"table_title_cell_open" => "<th {style}>\n" ,
		"table_title_cell_close" => "</th>\n" ,
		"table_title_row_close" => "</tr>\n" ,
		"table_row_open" => "<tr {style}>\n" ,
		"jquery_table_row_open" => "<tr id=\"{id}\" >\n" ,
		"table_cell_open" => "<td {style}>\n" ,
		"table_cell_close" => "</td>\n" ,
		"table_row_close" => "</tr>\n" ,
		"table_close" => "\n</table>\n",
		"table_additional_row" => "<tr><td colspan=\"{columns}\">{content}</td></tr>\n" ,
		"table_no_results_row" => "<tr><td colspan=\"{columns}\" align=\"center\">{message}</td></tr>\n" ,
		"table_pagination_row" => "<tr><td colspan=\"{columns}\">{content}</td></tr>\n"
	);

	public $jquery_table_template = array(
		"table_id" => 'jempe_jquery_table',
		"add_button_id" => 'jempe_add_button',
		"blank_row_id" => 'jempe_blankRow',
		"edit_link_rel" => 'edit_row_link',
		"delete_link_rel" => 'delete_row_link',
		"cancel_class" => 'jempe_cancel',
		"save_changes_class" => 'jempe_saveChanges',
		"editable_row_id" => 'jempe_editableRow',
		"view_class" => 'jempe_view',
		"edit_class" => 'jempe_edit',
		"button_class" => 'jempe_button',
		"view_prefix" => 'jempe_info_',
		"save_changes" => 'Save Changes',
		"cancel" =>'Cancel'
	);

	public $jquery_script = false;
	public $jquery_window_ready = false;
	public $jquery_popup = false;
	public $jquery_functions = array();
	public $jquery_css = array();
	public $javascript_functions = array();
	public $jquery_files = array();
	public $jquery_fancybox = false;
	public $jquery_table = false;

	public $jquery_ui_source = array(
		'ui'=>'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js',
		'crop'=>'jcrop.js',
		'grid'=>'jquery.jqgrid.min.js',
		'file'=>'ajaxupload.js'
	 );

	public $sortable_list_template = array(
		"list_open" => '<ul id="{id}">' ,
		"list_row" => '<li>{name}<input name="{value}" value="1" type="hidden"></li>' ,
		"list_close" => '</ul>'
	);

	public $tinymce = false;
	public $tinymce_buttons = 'theme_advanced_buttons1 : "save,|,undo,redo,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,|,bullist,numlist,outdent,indent,|,link,unlink,{image_button},|,removeformat,code,fullscreen",theme_advanced_buttons2 : "",theme_advanced_buttons3 : "",';
	public $tinymce_plugins = 'safari,style,save,{image},advlink,inlinepopups,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,template';
	public $use_tinymce = true;

	public $jquery_ajaxupload = FALSE;

	public $recaptcha_public_key = '6Ld75sASAAAAAMHIhpskfRhvKNlOHuihJzcu217E';
	public $recaptcha_private_key = '6Ld75sASAAAAALue32308l7xH4FlTjzghudrH0KC';
	public $recaptcha_error = FALSE;

	public $textcaptcha_key = 'd2we0h2zd7kg4kw4co0gwgk8s9hwyp3s';

	public $drag_and_drop_lists_template = array(
		'container_template' => '<div onmouseover="{init}">{available_items}{selected_items}</div>',
		'available_list_open' => '<ul id="{id}" class="jempe_drag_and_drop_{name}">',
		'available_list_item' => '<li rel="{item_id}">{item_name}</li>',
		'available_list_close' => '</ul>',
		'selected_list_open' => '<ul id="{id}" class="jempe_drag_and_drop_{name}">',
		'selected_list_item' => '<li rel="{item_id}">{item_name}</li>',
		'selected_list_close' => '</ul>'
	);

	/**
	* Constructor - Sets messages
	*/

	function __construct()
	{
		$CI =& get_instance();
		$this->jquery_table_template['save_changes'] = $CI->lang->line('jempe_button_save_changes');
		$this->jquery_table_template['cancel'] = $CI->lang->line('jempe_button_cancel');

		if(isset($CI->jempe_cms->tinymce_buttons))
		{
			$this->tinymce_buttons = $CI->jempe_cms->tinymce_buttons;
		}
		if(isset($CI->jempe_cms->tinymce_plugins))
		{
			$this->tinymce_plugins = $CI->jempe_cms->tinymce_plugins;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	* Form Declaration
	*
	* Creates the opening portion of the form.
	*
	* @access	public
	* @param	string	the URI segments of the form destination
	* @param	array	a key/value pair of attributes
	* @param	array	a key/value pair hidden data
	* @return	string
	*/	
	function form_open($action = '', $attributes = '', $hidden = array())
	{
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
			return form_open($action, $attributes, $hidden);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	* Form Declaration - Multipart type
	*
	* Creates the opening portion of the form, but with "multipart/form-data".
	*
	* @access	public
	* @param	string	the URI segments of the form destination
	* @param	array	a key/value pair of attributes
	* @param	array	a key/value pair hidden data
	* @return	string
	*/
	function form_open_multipart($action, $attributes = array(), $hidden = array())
	{
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
			return form_open_multipart($action, $attributes, $hidden);
		}
	}
	
	// --------------------------------------------------------------------
		
	/**
	* Hidden Input Field
	*
	* Generates hidden field.
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_hidden($name, $value = '', $extra = '')
	{
		$CI =& get_instance();
		$CI->load->helper('form');
	
		if(is_array($value))
		{
			$form_value = $value[$name];
		}
		else
		{
			$form_value = $value;
		}
	
		return '<input type="hidden" name="'.$name.'" value="'.$form_value.'" '.$extra.' />';
	}
	
	// --------------------------------------------------------------------
	
	/**
	* Image field
	*
	* Generates image fields..
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string the thumb type
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_image($name, $value = '', $thumb, $extra = '')
	{
		$CI =& get_instance();
	
		if(is_array($value))
		{
			$image_id = $value[$name];
		}
		else
		{
			$image_id = $value;
		}
	
		$add_image_icon = "images/icons/image_add_48.png";
	
		if($thumb == FALSE)
		{
			$thumb = "jempe";
		}
	
		if($image_id > 0)
		{
			$CI->load->database();
			$CI->load->library('jempe_cms');
	
			$config = $CI->jempe_cms->upload_images_config;
		
			$image_info = $CI->db->get_where("jempe_images" , array('image_id' => $image_id));
			$image_info = $image_info->row_array();
			$image = upload_url().$config['upload_path'] . "thumbs/".$thumb."/".$image_info["image_file"]."?time=".time();
			$display_cancel_button = "block";
		}
		else
		{
			$image = static_url().$add_image_icon;
			$display_cancel_button = "none";
		}
	
		if( ! $this->verify_form)
		{
			$CI->load->helper('url');
			$CI->load->library('jempe_admin');
			$CI->load->library('jempe_form');
	
			if($this->jquery_image_manager == FALSE)
			{
				$this->javascript_functions[] = '
					function remove_image_button(element)
					{
						element.parent().find("input").val(0);
						element.parent().find(".jempe_image_manager_photo").attr("src" , "'.static_url().$add_image_icon.'" );
						element.hide();
					}
					function open_image_manager(element)
					{
						selected_jempe_image_manager = element.parent();
						window.open( element.attr("rel") , "_blank", "width=950,height=600,scrollbars=yes,status=no,resizable=no,screenx=0,screeny=0");
					}
				';
	
				$this->jquery_image_manager = TRUE;
			}
	
			return '
			<div>
				<img rel="'.$image_id.'" style="margin-bottom:5px;display:' .$display_cancel_button.';" src="'.static_url().'images/icons/cancel_24.png" onclick="remove_image_button($(this))" class="remove_image_button" />
				<img class="jempe_image_manager_photo"  onclick="open_image_manager($(this))" src="'.$image.'" rel="'.$CI->jempe_admin->admin_url("image_manager/field/".$name."/".$image_id).'" />
				<input type="hidden" name="'.$name.'" value="'.$image_id.'" '.$extra.' />
			</div>';
		}
		else
		{
			return '<img  src="'.upload_url().$image.'" />';
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Hidden Id
	*
	* Generate hidden field only if the key exists in value array useful for id fields
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @return	string
	*/	
	function form_hidden_id($name, $value = '')
	{
		$CI =& get_instance();
	
		if(is_array($value) && isset($value[$name]) && $value[$name] > 0)
		{
			return $this->form_hidden($name,$value);
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Text Input Field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_input($data = '', $value = '', $extra = '')
	{
		if(is_array($value))
		{
			$field_value = $value[$data];
		}
		else
		{
			$field_value = $value;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');

			return form_input($data, $field_value, $extra);
		}
		else
		{
			return $field_value;
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Password Field
	*
	* Identical to the input function but adds the "password" type
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_password($name = '', $value = '', $extra = '')
	{
		if(is_array($value))
		{
			$default_value = $value[$name];
		}
		else
		{
			$default_value = $value;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
			return form_password($name, $default_value, $extra);
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Javacript upload file Field
	*
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string the name of the key that contains the configuration values in $this->jempe_cms->upload_files_config
	* @return	string
	*/	
	function form_file($name, $value, $config_array)
	{
		if(is_array($value))
		{
			$default_value = $value[$name];
		}
		else
		{
			$default_value = $value;
		}

		if( ! $this->verify_form)
		{
			$CI =& get_instance();

			$this->form_jquery_ui('file');

			$config = $CI->jempe_cms->upload_files_config[$config_array];

			$uniq_id = uniqid();

			if($this->jquery_ajaxupload === FALSE)
			{
				$this->jquery_functions[] = '
					$("*[onmouseover^=jempe_ajaxupload]").each(function()
					{
						$(this).trigger("mouseover");
					});
				';

				$this->javascript_functions[] = '
					function jempe_ajaxupload(button, config)
					{
						if(button.hasClass("jempe_ajaxupload") == false)
						{
							new AjaxUpload(button, {
								action: "'.site_url('jempe_uploader.xml').'", 
								name: "jempe_uploader_"+config,
								data: 
								{
									"upload_config": config
								},
								onSubmit : function(file, ext)
								{
									button.css("background", "url('.static_url().'images/loading.gif) no-repeat");
									button.attr("disabled", "disabled");
								},
								onComplete: function(file, response)
								{
									button.removeAttr("style");
									// enable upload button
									button.removeAttr("disabled");
									
									if($("error", response).text() != "")
									{
										alert($("error", response).text());
									}
									else
									{
										var file_name = $("file_name", response).text();

										if($("file_url", response).length > 0)
										{
											var file_link = "<a target=_blank href="+ $("file_url", response).text() +">" + $("file_name", response).text() + "</a>";
										}
										else
										{
											var file_link = file_name;
										}

										var file_link_id = file_name.replace(".", "_");

										// add file to the list
										button.parent().find(".jempe_uploaded").append("<li id=file_" + file_link_id +">" + file_link + " <a class=\'jempe_uploader_remove\' onclick=\\"$(this).parent().parent().parent().find(\'.jempe_uploader_field\').val(\'\'); $(this).parent().parent().parent().find(\'.jempe_uploader_button\').show(); $(this).parent().remove();\\" href=\'javascript:void(0);\'>X</a></li>");
										button.parent().find(".jempe_uploader_field").val(file_name);
										button.hide();

										if($("onComplete", response).length > 0)
										{
											eval($("onComplete", response).text() +"(response)");
										}
									}
								}
							});
						}
					}
				';
				$this->jquery_ajaxupload = TRUE;
			}

			$file_path = upload_path().$config['upload_path'].$default_value;

			if(strlen($default_value) && file_exists($file_path) && ! is_dir($file_path))
			{
				$uploaded_file = '<li>'.$default_value.' <a href="javascript:void(0);" onclick="$(this).parent().parent().parent().find(\'.jempe_uploader_field\').val(\'\'); $(this).parent().parent().parent().find(\'.jempe_uploader_button\').show(); $(this).parent().remove();" class="jempe_uploader_remove">X</a></li>';
				$button_style = 'style="display:none;"';
			}
			else
			{
				$uploaded_file = '';
				$button_style = '';
			}

			return '<ul class="jempe_uploaded" >'.$uploaded_file.'</ul><div '.$button_style.' onmouseover="jempe_ajaxupload($(this), \''.$config_array.'\')" class="jempe_uploader_button" ><noscript>'.$CI->lang->line('jempe_uploader_no_script').'</noscript><span>'.$CI->lang->line('jempe_uploader_upload_file').'</span></div><input class="jempe_uploader_field" type="hidden" value="'.$default_value.'" name="'.$name.'">';
		}
		else
		{
			return $default_value;
		}
	}

		
	// ------------------------------------------------------------------------
	
	/**
	* Javacript upload file Field
	*
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed array containing the list of files
	* @param	string the name of the key that contains the configuration values in $this->jempe_cms->upload_files_config
	* @return	string
	*/	
	function form_files($name, $values, $config_array)
	{
		if( ! $this->verify_form)
		{
			$CI =& get_instance();

			$this->form_jquery_ui('file');

			$config = $CI->jempe_cms->upload_files_config[$config_array];

			$uniq_id = uniqid();

			if($this->jquery_ajaxupload === FALSE)
			{
				$this->jquery_functions[] = '
					$("*[onmouseover^=jempe_ajaxupload]").each(function()
					{
						$(this).trigger("mouseover");
					});
				';

				$this->javascript_functions[] = '

					var jempe_uploader_'.$name.'_files = 0;

					function jempe_ajaxupload(button, config)
					{
						if(button.hasClass("jempe_ajaxupload") == false)
						{
							new AjaxUpload(button, {
								action: "'.site_url('jempe_uploader.xml').'", 
								name: "jempe_uploader_"+config,
								data: 
								{
									"upload_config": config
								},
								onSubmit : function(file, ext)
								{
									button.css("background", "url('.static_url().'images/loading.gif) no-repeat");
									button.attr("disabled", "disabled");
								},
								onComplete: function(file, response)
								{
									button.removeAttr("style");
									// enable upload button
									button.removeAttr("disabled");
									
									if($("error", response).text() != "")
									{
										alert($("error", response).text());
									}
									else
									{
										var file_name = $("file_name", response).text();

										if($("file_url", response).length > 0)
										{
											var file_link = "<a target=_blank href="+ $("file_url", response).text() +">" + $("file_name", response).text() + "</a>";
										}
										else
										{
											var file_link = file_name;
										}

										var file_new_id = "new_'.$name.'" + jempe_uploader_'.$name.'_files;

										var input_field = "<input class=\'jempe_uploader_field\' rel=\'" + file_new_id + "\' type=\'hidden\' value=\'" + file_name + "\' name=\''.$name.'[" + file_new_id + "]\'>";
										jempe_uploader_'.$name.'_files ++;

										// add file to the list
										button.parent().find(".jempe_uploaded").append("<li>" + file_link + " <a class=\'jempe_uploader_remove\' onclick=\\"$(this).parent().parent().parent().find(\'.jempe_uploader_field\').val(\'\'); $(this).parent().parent().parent().find(\'.jempe_uploader_button\').show(); $(this).parent().remove();\\" href=\'javascript:void(0);\'>X</a>" + input_field + "</li>");

										if($("onComplete", response).length > 0)
										{
											eval($("onComplete", response).text() +"(response)");
										}
									}
								}
							});
						}
					}
				';
				$this->jquery_ajaxupload = TRUE;
			}

			$uploaded_file = $upload_form_fields = '';

			if(isset($values) && count($values))
			{
				foreach($values as $file_id => $file_name)
				{
					$uploaded_file .= '<li>'.$file_name.' <a href="javascript:void(0);" onclick="$(this).parent().parent().parent().find(\'.jempe_uploader_field[rel='.$file_id.']\').val(\'\'); $(this).parent().parent().parent().find(\'.jempe_uploader_button\').show(); $(this).parent().remove();" class="jempe_uploader_remove">X</a></li>';

					$upload_form_fields .= '<input class="jempe_uploader_field" rel="'.$file_id.'" type="hidden" value="'.$file_name.'" name="'.$name.'['.$file_id.']">';
				}
			}

			return '<ul class="jempe_uploaded" >'.$uploaded_file.'</ul><div onmouseover="jempe_ajaxupload($(this), \''.$config_array.'\')" class="jempe_uploader_button" ><noscript>'.$CI->lang->line('jempe_uploader_no_script').'</noscript><span>'.$CI->lang->line('jempe_uploader_upload_file').'</span></div>'.$upload_form_fields;
		}
		else
		{
			return $default_value;
		}
	}

	// ------------------------------------------------------------------------
	
	/**
	* Upload Field
	*
	* Identical to the input function but adds the "file" type
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_upload($name = '', $value = '', $extra = '')
	{
		if(is_array($value))
		{
			$selected_value = $value[$name];
		}
		else
		{
			$selected_value = $value;
		}	

		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
	
			return form_upload($name, '', $extra);
		}
		else
		{
			return $selected_value;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Textarea field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_textarea($name = '', $value = '', $extra = '')
	{
		if(is_array($value))
		{
			$selected_value = $value[$name];
		}
		else
		{
			$selected_value = $value;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
	
			return form_textarea($name, $selected_value, $extra);
		}
		else
		{
			return $selected_value;
		}
	}
	
	// ------------------------------------------------------------------------

	/**
	* Html area
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @param	integer field width
	* @param	integer field height
	* @param	string list of tinymce plugins
	* @param	string list of tinymce buttons
	* @param	string tinymce plugin
	* @return	string
	*/	
	function form_htmlarea($name = '', $value = '', $extra = '', $width = 600, $height = 300, $plugins = FALSE, $buttons = FALSE, $theme = "advanced")
	{
		$CI =& get_instance();
	
		if(is_array($value))
		{
			$selected_value = $value[$name];
		}
		else
		{
			$selected_value = $value;
		}
	
		if( ! $this->verify_form)
		{
			if($this->use_tinymce && ! isset($this->jquery_functions['html_area_'.$name.'_config']))
			{
				$this->tinymce = true;
		
				$CI->load->library('jempe_admin');
				$CI->load->library('jempe_cms');
	
				$image_url = "jempe_image_url : '" .$CI->jempe_admin->admin_url('image_manager/field/'.$name."'" );
	
				if($CI->jempe_admin->user_permission('images_other_users', 'read') OR $CI->jempe_admin->user_permission('images_same_user_type', 'read') OR $CI->jempe_admin->user_permission('own_images', 'read'))
				{
					$image_plugin = "jempeimage";
					$image_button = "jempeimage";
				}
				else
				{
					$image_plugin = "advimage";
					$image_button = "image";
				}
	
				if( ! $plugins)
				{
					$plugins = str_replace("{image}", $image_plugin , $this->tinymce_plugins);
				}
				else
				{
					$plugins = str_replace("{image}", $image_plugin , $plugins);
				}

				if( ! $buttons)
				{
					$buttons = str_replace('{image_button}', $image_button, $this->tinymce_buttons);
				}
				else
				{
					$buttons = str_replace("{image_button}", $image_button, $buttons);
				}

				$this->javascript_functions[] = '
					function jempe_fileBrowser(field_name, url, type, win) {
						var cmsURL = "'.site_url('admin/file_manager').'";     // your URL could look like "/scripts/my_file_browser.php"
						var searchString = window.location.search; // possible parameters
						if (searchString.length < 1) {
							// add "?" to the URL to include parameters (in other words: create a search string because there wasnt one before)
							searchString = "?";
						}

						window.open (cmsURL + searchString, "jempe_file_manager", "width=950,height=600,scrollbars=yes,status=no,resizable=no,screenx=0,screeny=0");

						return false;
					}
				';

				$this->javascript_functions['html_area_'.$name.'_config'] = '
					var jempe_tinymce = false;
					var html_area_'.$name.'_config = {
					// Location of TinyMCE script
					script_url : "'.static_url().'tiny_mce/tiny_mce.js",
		
					// General options
					theme : "'.$theme.'",
					plugins : "'.$plugins.'",
					width : "'.$width.'",
					height : "'.$height.'",
					theme_advanced_resizing : false,
					theme_advanced_resizing_use_cookie : false,
		
					// Theme options
					'.$buttons.'
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
					language : "'.$CI->lang->line('jempe_fck_language').'", 
		
					// Example content CSS (should be your site CSS)
					//content_css : "jempe.css",
		
					// Drop lists for link/image/media/template dialogs
					external_link_list_url : "'.site_url('admin/tinymce_links.js').'",
					extended_valid_elements : "iframe[*], img[*]",
					relative_urls : true,
					document_base_url : "'.base_url().'",
					'.$image_url.',
					paste_preprocess : function(pl, o) {
						o.content = strip_tags( o.content , "<a><br><iframe><object><embed>" );
					},
					file_browser_callback : "jempe_fileBrowser"
				};
				';
	
				$this->jquery_functions['html_area_'.$name.'_config'] = '
					if($("textarea[name='.$name.']").length > 0)
					{
						$("textarea[name='.$name.']").tinymce(html_area_'.$name.'_config);
					}
				';
		
				return $this->form_textarea($name, $selected_value, $extra. ' title="htmlarea" rel="html_area_'.$name.'_config"');
			}
			else
			{

			}
	
		}
		else
		{
			return $selected_value;
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Drop-down Menu
	*
	* @access	public
	* @param	string the name of the form field
	* @param	array a key/value pair of options
	* @param	mixed the selected value it can be a string or array
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_dropdown($name = '', $options = array(), $selected = '', $extra = '')
	{
		if(is_array($selected))
		{
			$selected_value = $selected[$name];
		}
		else
		{
			$selected_value = $selected;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
	
			return form_dropdown($name, $options, $selected_value, $extra);
		}
		else
		{
			if(isset($options[$selected_value]))
			{
				return $options[$selected_value];
			}
			else
			{
				return '';
			}
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Drop-down Menu multiple
	*
	* @access	public
	* @param	string the name of the form field
	* @param	array a key/value pair of options
	* @param	array the selected values
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_dropdownmultiple($name = '', $options = array(), $selected_values = array(), $extra = '')
	{
		if( ! $this->verify_form)
		{
			$output = '<select name="'.$name.'[]" multiple="true" '.$extra.' >'; 
			foreach($options as $option => $value)
			{
				if( ! (array_search($option, $selected_values) === FALSE))
				{
					$selected = 'selected="selected"';
				}
				else
				{
					$selected = "";
				}
	
				$output .='<option value="'.$option.'" '.$selected.'>'.$value.'</option>'."\n";
			}

			$output .= '</select>';

			return $output;
		}
		else
		{
			$output = "";
			foreach($selected_values as $selected_value)
			{
				$output .= $options[$selected_value]."<br>";
			}

			return $output;
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Checkbox Field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the value attribute of input tag
	* @param	mixed the selected value it can be a string or array or bool
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_checkbox($name, $value, $checked = FALSE, $extra = '')
	{
		if(is_array($checked))
		{
			if(isset($checked[$name]))
			{
				if($checked[$name] == $value)
				{
					$ch_value = TRUE;
				}
				else
				{
					$ch_value = FALSE;
				}
			}
			else
			{
				$ch_value = FALSE;
			}
		}
		else
		{
			$ch_value = (bool) $checked;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
	
			return form_checkbox($name, $value, $ch_value, $extra).form_hidden('jempe_fields[]', $name);
		}
		else
		{
			if($ch_value)
			{
				return '&#10004;';
			}
		}
	}

	 // ------------------------------------------------------------------------

	 /**
	 * Check if there are checkbox that were not checked and assign 0 value
	 *
	 *
	 * @access	public
	 * @return	array
	 */
	function process_checkboxes()
	{
		foreach($_POST['jempe_fields'] as $field)
		{
			if( ! isset($_POST[$field]))
			{
				$_POST[$field] = 0;
			}
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Multiple Checkbox Field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	array a key/value pair of options
	* @param	array the selected values
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	
	function form_checkboxmultiple($name = '', $options = array(), $selected_array = array() , $extra='')
	{
		$CI =& get_instance();
		$CI->load->helper('form');
	
		if(isset($selected_array[$name]))
		{
			$selected_values = $selected_array[$name];
		}
		else
		{
			$selected_values = array();
		}
	
		$output = $this->checkboxmultiple_template["checkbox_open"]; 

		$search = array("{name}", "{value}", "{option_id}");

		$provided_extra = $extra;
	
		if( ! $this->verify_form)
		{
			foreach($options as $option => $value)
			{
				if( ! (array_search($option, $selected_values) === FALSE))
				{
					$selected = TRUE;
				}
				else
				{
					$selected = FALSE;
				}

				$option_id = 'jempe_checkbox_'.$name.'_'.$option;

				$extra = $provided_extra.' id="'.$option_id.'" ';
	
				$checkbox = form_checkbox($name."[]", $option, $selected, $extra);

				$replace = array($value, $checkbox, $option_id);
	
				$output .= str_replace($search, $replace, $this->checkboxmultiple_template["checkbox_row"]);
			}
	
		}
		else
		{
	
			foreach($selected_values as $selected_value)
			{
				if(isset($options[$selected_value]))
				{
					$replace = array($options[$selected_value], '', '');

					$output .= str_replace($search,$replace,$this->checkboxmultiple_template["checkbox_row"]);
				}
			}
	
		}
	
		$output .= $this->checkboxmultiple_template["checkbox_close"]; 
	
		return $output;
	}
		
	// ------------------------------------------------------------------------

	/**
	* Radio Field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the value attribute of input tag
	* @param	mixed the selected value it can be a string or array or bool
	* @param	string additional attributes for the input tag
	* @return	string
	*/
	
	function form_radio($name = '', $value = '', $checked = TRUE, $extra = '')
	{
		if(is_array($checked))
		{
			if(isset($checked[$name]))
			{
				if($checked[$name] == $value)
				{
					$selected_value = TRUE;
				}
				else
				{
					$selected_value = FALSE;
				}
			}
			else
			{
				$selected_value = FALSE;
			}
		}
		else
		{
			$selected_value = $checked;
		}
	
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
	
			return form_radio($name, $value, $selected_value, $extra);
		}
		else
		{
			if($selected == TRUE)
			{
				return $value;
			}
		}
	}
		
	// ------------------------------------------------------------------------

	/**
	* Multiple Radio Field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	array a key/value pair of options
	* @param	array the selected value
	* @param	string additional attributes for the input tag
	* @return	string
	*/
	
	function form_radiomultiple($name = '', $options = array(), $selected = array() , $extra='')
	{
		$CI =& get_instance();
		$CI->load->helper('form');

		if(isset($selected[$name]))
		{
			$selected_value = $selected[$name];
		}
		else
		{
			$selected_value = '';
		}

		$output = $this->radiomultiple_template["radio_open"]; 

		$search = array("{name}", "{value}", "{option_id}");
	
		if( ! $this->verify_form)
		{
			foreach($options as $option => $value)
			{
				if($option == $selected_value)
				{
					$option_selected = TRUE;
				}
				else
				{
					$option_selected = FALSE;
				}

				$option_id = 'jempe_checkbox_'.$name.'_'.$option;

				$checkbox_extra = $extra.' id="'.$option_id.'" ';
	
				$radio = form_radio($name, $option, $option_selected, $checkbox_extra);

				$replace = array($value, $radio, $option_id);
	
				$output .= str_replace($search, $replace, $this->radiomultiple_template["radio_row"]);
			}
	
		}
		else
		{
			$replace = array($options[$selected_value], '', '');
	
			$output .= str_replace($search, $replace, $this->radiomultiple_template["radio_row"]);
		}
	
		$output .= $this->radiomultiple_template["radio_close"]; 
	
		return $output;
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Calendar text field
	*
	* @access	public
	* @param	string the name of the form field
	* @param	mixed the selected value it can be a string or array or bool
	* @param	mixed jQuery UI calendar options
	* @param	string additional attributes for the input tag
	* @return	string
	*/
	
	function form_calendar($name = '', $value = '', $cal_options = array(), $extra = '')
	{
		$CI =& get_instance();
		$CI->load->helper('form');
	
		$this->form_jquery_ui('ui');
	
		if(is_array($value))
		{
			$selected_value = $value[$name];
		}
		else
		{
			$selected_value = $value;
		}

		if(strlen($selected_value))
		{
			$selected_value = date($CI->jempe_cms->date_format, strtotime($selected_value));
		}
		
		if( ! $this->verify_form)
		{
			$calendar_options = "";
	
			if(count($cal_options))
			{
				$calendar_options .= '{';
	
					foreach($cal_options as $option_name => $option_value)
					{
						if($calendar_options != '{')
						{
							$calendar_options .= ' , ';
						}
	
						$option_value = $this->phpvar_to_js($option_value);

						$calendar_options .= '"'.$option_name.'" :'.$option_value;
					}
	
				$calendar_options .= '}';
			}
			else
			{
				$calendar_options = 'false';
			}
	
			$field_id = 'jempe_calendar_' .$name ;
	
			$this->javascript_functions[] = '
				var '.$field_id.'_options = '.$calendar_options.';
			';
	
			$show_date_picker = 'jempe_show_date_picker($(this), \''.$field_id.'_options\')';

			$extra = $extra.' onclick="'.$show_date_picker.'" onfocus="'.$show_date_picker.'" ';
	
			return form_input($name, $selected_value, $extra);
		}
		else
		{
			return $selected_value;
		}
	}
	
	// ------------------------------------------------------------------------
	
	/**
	* Submit Button
	*
	* @access	public
	* @param	string the name of the form field
	* @param	string value attribute for the input tag
	* @param	string additional attributes for the input tag
	* @return	string
	*/	
	function form_submit($name = '', $value = '', $extra = '')
	{
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');

			return form_submit($name, $value, $extra);
		}
	}
		
	// ------------------------------------------------------------------------
	
	/**
	* Form Close Tag
	*
	* @access	public
	* @param	string text to add below the form close tag
	* @return	string
	*/	
	function form_close($extra = '')
	{
		if( ! $this->verify_form)
		{
			$CI =& get_instance();
			$CI->load->helper('form');
			return form_close($extra);
		}
	}

	
	// ------------------------------------------------------------------------	

	/**
	* Results Table
	*
	* @access	public
	* @param	array table data
	* @param	array columns data
	* @param	string message to show when there are no rows
	* @param	array pagination configuration
	* @param	string text to add in the first row, useful to add a Add button
	* @param	bool is it a jquery inline table
	* @param	string table id parameter
	* @return	string
	*/	
	function results_table($data, $columns, $no_results = "No results", $pagination = FALSE, $additional = FALSE, $jquery_table = FALSE, $table_id = FALSE)
	{
		if($jquery_table)
		{
			$table = str_replace('{id}', $table_id, $this->results_table_template['jquery_table_open']);
		}
		else
		{
			$table = $this->results_table_template['table_open'];
		}
	
		$table .= $this->results_table_template['table_head_open'].$this->results_table_template['table_title_row_open'] ;
	
		$total_columns = count($columns);
	
		foreach($columns as $column)
		{
			if( ! isset($column['style']))
			{
				$column["style"] = "";
			}

			$table .=  str_replace('{style}', $column['style'], $this->results_table_template['table_title_cell_open']).$column['title'].$this->results_table_template['table_title_cell_close'] ;
		}
	
		$table .= $this->results_table_template['table_title_row_close'].$this->results_table_template['table_head_close'].$this->results_table_template['table_body_open'] ;
	
		if($additional)
		{
			$table .= str_replace('{content}', $additional, str_replace('{columns}', $total_columns, $this->results_table_template['table_additional_row'])) ;	
		}
	
		if(count($data))
		{
			$row_number = 0;

			foreach($data as $row)
			{
				if($row_number / 2 == round($row_number / 2))
				{
					$row_class = 'unpair';
				}
				else
				{
					$row_class = 'pair';
				}

				$table .= str_replace('{style}', 'class="'.$row_class.'"', $this->results_table_template['table_row_open']);

				$row_number++;
	
				foreach($columns as $column)
				{
					if( ! isset($column['style']))
					{
						$column['style'] = "";
					}
	
					if(isset($column['label']))
					{
						$table .=  str_replace('{style}', $column['style'], $this->results_table_template['table_cell_open']).str_replace('{field}', $row[$column['field']], $column['label']).$this->results_table_template['table_cell_close'] ;
					}
					else
					{
						$table .=  str_replace('{style}', $column['style'], $this->results_table_template['table_cell_open']).$row[$column['field']].$this->results_table_template['table_cell_close'];
					}
				}
				$table .= $this->results_table_template['table_row_close'];
			}
	
		}
		else
		{
			if($no_results)
			{
				$table .= str_replace('{message}', $no_results, str_replace('{columns}', $total_columns, $this->results_table_template['table_no_results_row']));
			}	
		}
	
		if($jquery_table)
		{
			$table .= str_replace('{id}' ,$this->jquery_table_template['blank_row_id'], $this->results_table_template['jquery_table_row_open']);
			
				foreach($columns as $column)
				{
					if( ! isset($column['style']))
					{
						$column['style'] = "";
					}

					$table .=  str_replace('{style}', $column['style'], $this->results_table_template['table_cell_open']).$jquery_table[$column['field']].$this->results_table_template['table_cell_close'];
				}
	
			$table .= $this->results_table_template['table_row_close'];
		}
	
		if($pagination)
		{
			$CI =& get_instance();
			$CI->load->library('pagination');
	
			foreach($pagination as $name => $value)
			{
				$config[$name] = $value;
			}
			
			$CI->pagination->initialize($config);
	
			$table .= str_replace('{content}', $CI->pagination->create_links(), str_replace('{columns}', $total_columns, $this->results_table_template['table_pagination_row']));	
		}

		$table .= $this->results_table_template['table_body_close'].$this->results_table_template['table_close'];
	
		return $table;
	}
		
	// ----------------------------------------------------------------------
	
	/**
	* Form
	*
	* Create an array with all form fields and fields data
	*
	* @access	public
	* @param	array	form fields data
	* @param	array	selected values of each field
	* @return	string
	*/	
	function form($fields, $data)
	{	
		foreach($fields as $field)
		{
			if( ! isset($field['extra']))
			{
				$field['extra'] = '';
			}
			if( ! isset($field['options']))
			{
				$field['options'] = array();
			}
			if( ! isset($field['thumb']))
			{
				$field['thumb'] = '';
			}

			$form[$field['name']] = $field;
			$form[$field['name']]['field'] = $this->create_form_field($field['type'], $field['var_name'], $data, $field['options'], $field['extra'], $field['thumb']);

			if(isset($field['validation']))
			{
				$CI =& get_instance();
				$CI->load->library('form_validation');
				$CI->form_validation->set_rules($field['var_name'], $field['label'], $field['validation']);
			}
		}
		return $form;
	}
		
	// ----------------------------------------------------------------------

	/**
	* crea el campo de acuerdo al typo que se haya escogido
	*
	* @access	public
	* @param	string	field type (text, dropdown, htmlarea, etc)
	* @param	string	field name
	* @param	array default values for fields	
	* @param	array key/pair values for field options (works with dropdowns multiple checkboxes, etc)
	* @param	string value attribute for input tag (checkbox, radio fields)	
	* @param	string additional attributes for tag
	* @param	string thumb type
	* @param	bool verify form, convert all fields in texts
	* @return	string
	*/	
	function create_form_field($type, $field, $data, $options, $extra, $thumb = FALSE, $verify = FALSE)
	{
		$verify_state = $this->verify_form;
		$this->verify_form = $verify;
		$output = '';
	
		if($type == 'text' OR $type == 'url' OR $type == 'decimal' OR $type == 'input')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}
			$output = $this->form_input($field, $data, $extra); 
		}

		if($type == 'list' OR $type == 'dropdown')
		{
			if( ! isset($data))
			{
				$data[$field] = array('');
			}
			$output = $this->form_dropdown($field, $options, $data, $extra); 
		}

		if($type == 'bool')
		{
			if( ! isset($data))
			{
				$data[$field] = 0;
			}
			$output = $this->form_checkbox($field, 1, $data, $extra); 
		}

		if($type == 'checkbox')
		{
			if( ! isset($data))
			{
				$data[$field] = 0;
			}
			$output = $this->form_checkbox($field, $options, $data, $extra); 
		}

		if($type == 'dropdownmultiple')
		{
			if( ! isset($data))
			{
				$data[$field] = array("");
			}
			$output =  $this->form_dropdownmultiple($field, $options, $data,  'multiple="true"'); 
		}

		if($type == 'radiomultiple')
		{
			if( ! isset($data))
			{
				$data[$field] = array('');
			}
			$output = $this->form_radiomultiple($field, $options, $data); 
		}

		if($type == 'multiplecheckbox')
		{
			if( ! isset($data))
			{
				$data[$field] = array('');
			}
			$output = $this->form_checkboxmultiple($field, $options, $data, $extra); 
		}

		if($type == 'area' OR $type == 'textarea')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}
			$output = $this->form_textarea($field, $data, $extra); 
		}

		if($type == 'date' OR $type == 'calendar')
		{
			if( ! isset($data))
			{
				$data[$field] = "";
			}

			if(isset($options) && is_array($options))
			{
				$calendar_options = $options;
			}
			else
			{
				$calendar_options = array();
			}

			$output = $this->form_calendar($field, $data, $calendar_options, $extra); 
		}

		if($type == 'htmlarea')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}
			$output = $this->form_htmlarea($field, $data, $extra); 
		}

		if($type == 'hidden')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}
			$output = $this->form_hidden($field, $data, $extra); 
		}

		if($type == 'image')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}
			$output = $this->form_image($field, $data, $thumb, $extra); 
		}

		if($type == 'none')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}

			if($this->verify_form)
			{
				$output = $data[$field];
			}
			else
			{
				$output = '';
			}
		}

		if($type == 'label')
		{
			if( ! isset($data))
			{
				$data[$field] = '';
			}

			$output = $data[$field];
		}

		if($type == 'password')
		{
			$output = $this->form_password($field, '', $extra); 
		}

		if($type == 'html')
		{
			$output = str_replace('{field}', $data[$field], $options); 
		}

		if($type == 'file')
		{
			$output = $this->form_file($field, $data[$field], $options); 
		}
	
		$this->verify_form = $verify_state;
	
		return $output;
	}
		
	// ----------------------------------------------------------------------

	/**
	* Jquery script
	*
	* Add script tags for external js files
	*
	* @access	public
	* @return	string
	*/
	
	function form_jquery()
	{
		$output = "";

		if( ! $this->jquery_script)
		{
			$this->jquery_script = TRUE;

			if(ENVIRONMENT == 'development')
			{
				$jquery_path = static_url() . 'jquery/';
			}
			else
			{
				$jquery_path =  'https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/';
			}

			$output = '<script type="text/javascript" src="'.$jquery_path.'jquery.min.js"></script>'."\n";

			for($i = 0; $i < count($this->jquery_files); $i++)
			{
				if(strpos($this->jquery_files[$i], 'http') === 0)
				{
					$output .= '<script type="text/javascript" src="' .$this->jquery_files[$i] .'"></script>'."\n";
				}
				else
				{
					$output .= '<script type="text/javascript" src="'.static_url().'jquery/'.$this->jquery_files[$i].'"></script>'."\n";
				}
			}
	
			if($this->tinymce)
			{
				$output .='<script type="text/javascript" src="'.static_url().'tiny_mce/jquery.tinymce.js"></script>
				<script type="text/javascript" src="'.static_url().'tiny_mce/php_functions.js"></script>';
			}
	
			if($this->jquery_fancybox)
			{
				$output .='	<script type="text/javascript" src="'.static_url().'fancybox/jquery.mousewheel-3.0.2.pack.js"></script>
				<script type="text/javascript" src="'.static_url().'fancybox/jquery.fancybox-1.3.1.js"></script>
				<link rel="stylesheet" type="text/css" href="'.static_url().'fancybox/jquery.fancybox-1.3.1.css" media="screen" />';
			}
	
			return $output;
		}
	}

	// ----------------------------------------------------------------------
	
	/**
	* Jquery ready
	*
	* @access	public
	* @return	string
	*/
	
	function form_jquery_ready()
	{
		$out = "";
	
		if(count($this->jquery_css) > 0)
		{
			foreach($this->jquery_css as $css_file)
			{
				$out .= '<link rel="stylesheet" type="text/css" href="'.$css_file.'" />';
			}
		}
	
		if(isset($this->jquery_validation_functions))
		{
			foreach($this->jquery_validation_functions as $val_function)
			{
				$this->jquery_functions[] = $val_function;
			}
		}
	
		if( ! $this->jquery_window_ready && (count($this->jquery_functions) > 0 OR count($this->javascript_functions) > 0))
		{
			$this->jquery_window_ready = TRUE;
			$out .= '<script type="text/javascript" >';
	
			if(count($this->javascript_functions) > 0)
			{
				$out .= "\n" .implode("\n", $this->javascript_functions)."\n";
			}

			if(count($this->jquery_functions) > 0)
			{
				$out .= '$(document).ready(function() {'."\n".implode("\n", $this->jquery_functions)."\n".'});';
			}
	
			$out .= '</script>';
		}

		$out .= '<script type="text/javascript" src="'.static_url().'jquery/jempe_form.js"></script>';
	
		return $out;
	}

	// ----------------------------------------------------------------------

	/**
	* Jquery ui
	*
	* Add jquery external js files
	*
	* @access	public
	* @param	string name of the file
	* @return	string
	*/
	
	function form_jquery_ui($file)
	{
		if( ! isset($this->jquery_css['ui']))
		{
			$this->jquery_css['ui'] = static_url()."jquery/css/ui-darkness/jquery-ui-1.8.2.custom.css";
		}

		if( ! in_array($this->jquery_ui_source[$file], $this->jquery_files))
		{
			$this->jquery_files[] = $this->jquery_ui_source[$file];
		}
	}

	// ----------------------------------------------------------------------

	/**
	* sortable list
	*
	* @access	public
	* @param	string sortable list id
	* @param	string key/pair data for list
	* @param	string placeholder class name
	* @return	string
	*/
	
	function form_sortable_list($id, $data, $placeholder_class)
	{
		$this->form_jquery_ui('ui');
	
		$this->jquery_functions[] = '	
			$("#'.$id.'").sortable({
				placeholder: \''.$placeholder_class.'\'
			});
			$("#'.$id.'").disableSelection();
		';
	
		$output = str_replace('{id}', $id, $this->sortable_list_template['list_open']);
	
		foreach($data as $data_id => $data_name)
		{
			$search = array('{name}', '{value}');
			$replace = array($data_name, $id ."_" .$data_id);

			$output .= str_replace($search, $replace, $this->sortable_list_template['list_row']);
		}
	
		$output .= $this->sortable_list_template['list_close'];
	
		return $output;
	}

	// ----------------------------------------------------------------------
	
	/**
	* process sortable list data and return an array
	*
	* @access	public
	* @param	string sortable list id
	* @param	string data to process
	* @param	string reverse results?
	* @return	string
	*/
	
	function process_sortable_list($id, $data, $reverse = FALSE)
	{
		$sorted_data = array();
		$row_number = 1;
	
		if(count($data))
		{
			foreach($data as $data_id => $data_value)
			{
				if(strpos($data_id, $id."_") === 0)
				{
					$sorted_data[str_replace($id.'_', '', $data_id)] = $row_number;
					$row_number++;
				}
			}
		}
	
		if($reverse == TRUE)
		{
			$sorted_data = array_reverse($sorted_data);
		}
	
		return $sorted_data;
	}

	// ------------------------------------------------------------------------

	/**
	* Jquery table add boton Tabla para eliminar editar y crear registros
	*
	* @access	public
	* @param	string label of button
	* @param	string table id
	* @param	string additional attributes
	* @return	string
	*/
	
	function form_jquery_table_add_button($button_label, $table_id, $extra = "")
	{
		return '<a class="jempe_add_row_button '.$this->jquery_table_template["button_class"].'" onclick="jempe_add_row($(\'#'.$table_id.'\'))" ' .$extra .'>' .$button_label .'</a>';
	}

	// ------------------------------------------------------------------------

	/**
	* Jquery inline edit table
	*
	* Table that display data and can be edited using jQuery ajax functions
	*
	* @access	public
	* @param	array data to show in table
	* @param	array fields data
	* @param	string ajax url to add/edit rows
	* @param	string ajax url to delete rows
	* @param	string warning message that appears when trying to delete a row
	* @param	string message that appears when there are no records
	* @param	string table id
	* @param	string pagination config
	* @return	string
	*/
	function form_jquery_table($items_list, $fields, $edit_url, $delete_url, $delete_message, $no_results, $table_id, $pagination = FALSE)
	{
		$row_data = "";
	
		$table_data = $columns = $empty_row = $hidden_fields = array();

		//search for hidden fields

		foreach($fields as $field)
		{
			if($field['type'] == 'hidden')
			{
				$hidden_fields[] = $field['field'];
				$default_values[$field['field']] = $field['default'];
			}	
		}

		// fields order is important create items for the empty row
	
		foreach($fields as $field)
		{
			if( ! isset($field['extra']))
			{
				$field['extra'] = '';
			}
	
			if( ! isset($field['options']))
			{
				$field['options'] = array();
			}
	
			if( ! isset($field['value']))
			{
				$field['value'] = '';
			}
	
			if($field['type'] == 'id')
			{
				$id_field = $field['field'];
			}
			else if($field['type'] == 'delete')
			{
				$empty_row[$field['field']] = '
					<div class="jempe_edit" style="display:none;">
						<a onclick="jempe_cancel_edit()" class="'.$this->jquery_table_template["button_class"].'">'.$this->jquery_table_template["cancel"].'</a>
					</div>
					<div class="jempe_view">
						<a class="jempe_delete_row_button '.$this->jquery_table_template["button_class"].'" rel="'.$delete_url.'" href="javascript:void(0);" onclick="jempe_delete_row($(this).parent().parent().parent())">' .$field["label"] .'</a>
					</div>' ;
				unset($field['label']);
				$columns[] = $field;
	
			}
			else if($field['type'] == 'edit')
			{
				$empty_row[$field["field"]] = '
					<div class="jempe_edit" style="display:none;">
						<a class="jempe_save_changes '.$this->jquery_table_template["button_class"].'" onclick="jempe_save_row($(this).parent().parent().parent())">'.$this->jquery_table_template['save_changes'].'</a>
					</div>
					<div class="jempe_view">
						<a rel="'.$edit_url.'" class="jempe_edit_row_button '.$this->jquery_table_template["button_class"].'" onclick="jempe_show_edit_fields($(this).parent().parent().parent())" href="javascript:void(0);">'.$field['label'].'</a>
					</div>' ;
				unset($field['label']);
				$columns[] = $field;
			}
			else if($field['type'] != 'hidden')
			{
				$data[$field['field']] = $field['default'];
	
				$empty_row[$field['field']] = '
					<div class="jempe_edit" >
						' .$this->create_form_field($field['type'], $field['field'], $data, $field['options'], $field['extra']).'
					</div>
					<div style="display:none;" class="jempe_view'.$field['field'].' jempe_view">
					</div>' ;
				$columns[] = $field;
	
				if($field['type'] == 'list')
				{
					$tag = 'select';
				}
				else if($field['type'] == 'area')
				{
					$tag = 'textarea';
				}
				else
				{
					$tag = 'input';
				}

				if( ! isset($first_field))
				{
					$first_field = TRUE;
	
					//add id field in first column
					$empty_row[$field['field']] .= $this->form_hidden($id_field, '', 'class="jempe_id_field"');

					if(count($hidden_fields))
					{
						foreach($hidden_fields as $hidden_field)
						{
							$empty_row[$field['field']] .= $this->form_hidden($hidden_field, $default_values[$hidden_field]);
						}
					}
				}
			}
	
		}
	
		foreach($items_list as $item)
		{	
			$row_info = array();
			unset($first_row_field);
	
			foreach($fields as $field)
			{
				if( ! isset($field['extra']))
				{
					$field['extra'] = '';
				}
		
				if( ! isset($field['options']))
				{
					$field['options'] = array();
				}

				if($field['type'] == 'id')
				{
					$id_field = $field['field'];
				}
				else if($field['type'] == 'delete')
				{
					$row_info[$field['field']] = $empty_row[$field["field"]] ;
				}
				else if($field['type'] == 'edit')
				{
					$row_info[$field['field']] = $empty_row[$field["field"]] ;
				}
				else if($field['type'] != 'hidden')
				{
					$data[$field['field']] = $item[$field['field']];
		
					$row_info[$field['field']] = '
						<div style="display:none;" class="jempe_edit" >
							'.$this->create_form_field($field['type'], $field['field'], $data, $field['options'], $field['extra']) .'
						</div>
						<div class="jempe_view'.$field["field"] .' jempe_view">'.$this->create_form_field($field['type'], $field['field'], $data, $field['options'], $field['extra'], FALSE, TRUE).'
						</div>' ;
			
					if( ! isset($first_row_field))
					{
						$first_row_field = TRUE;
						//insert id field
						$row_info[$field['field']] .= $this->form_hidden($id_field, $item[$id_field], 'class="jempe_id_field"');

						if(count($hidden_fields))
						{
							foreach($hidden_fields as $hidden_field)
							{
								$row_info[$field['field']] .= $this->form_hidden($hidden_field, $item[$hidden_field]);
							}
						}
					}
				}
		
			}
	
			$table_data[] = $row_info;
		}

		if( ! $this->jquery_table)
		{
			$this->jquery_table = TRUE;

			$this->javascript_functions[] = '
				var editable_row = false;
				function jempe_show_edit_fields(row)
				{
					if(editable_row == false)
					{
						$("'.$this->jquery_table_template['add_button_id'].'").attr("disabled", "disabled");
						editable_row = row;
						row.attr("id", "jempe_editableRow");
						row.find(".jempe_view").hide();
						row.find(".jempe_edit").show();
					}
				}
				function jempe_cancel_edit()
				{
					editable_row = false;

					var row = $("#jempe_editableRow");

					$(".jempe_add_row_button").removeAttr("disabled");
	
					if(row.find(".jempe_id_field").val() > 0)
					{
						row.find(".jempe_view").show();
						row.find(".jempe_edit").hide();
						row.removeAttr("id");
					}
					else
					{
						$("#jempe_editableRow").remove();
					}
				}
				function jempe_delete_row(row)
				{
					var delete_message = "' .$delete_message .'";

					if(confirm(delete_message.replace("{field}", jQuery.trim(row.find("*[rel=jempe_name_field] .jempe_view").text()))))
					{
						var delete_url = row.find(".jempe_delete_row_button").attr("rel");

						row.find(".jempe_delete_row_button").attr("disabled","disabled");

						var row_id = row.find(".jempe_id_field").val();

						var row_data = new Object();

						eval("row_data."+row.find(".jempe_id_field").attr("name")+" = row_id");

						$.post(delete_url, row_data, function(xml)
						{
							row.find(".jempe_delete_row_button").removeAttr("disabled");
			
							if($("success", xml).text() == "1")
							{
								row.remove();
							}
						});
					}
				}
				function jempe_save_row(row)
				{
					row.find(".jempe_save_changes").attr("disabled", "disabled");
					var edit_url = row.find(".jempe_edit_row_button").attr("rel");

					var row_data = new Object();

					row.find("input[type=text], input[type=checkbox], input[type=radio]:checked, input[type=password], input[type=hidden], select, textarea").each( function()
					{
						var field_value = $(this).val();
						var field_name = $(this).attr("name");

						if(field_name.indexOf("[") == -1)
						{
							if($(this).attr("type") == "checkbox")
							{
								if($(this).is(":checked"))
								{
									eval("row_data."+$(this).attr("name")+" = field_value");
								}
								else
								{
									eval("row_data."+$(this).attr("name")+" = \'\'");
								}
							}
							else
							{
								eval("row_data."+$(this).attr("name")+" = field_value");
							}
						}
					});

					$.post(edit_url, row_data, function(xml) 
					{
						row.find(".jempe_save_changes").removeAttr("disabled");

						if($("error", xml).text().length > 0)
						{
							alert($("error", xml).text());
						}
						else
						{
							row.find("input, select, textarea").each( function()
							{
								var field_name = $(this).attr("name");

								if(field_name.indexOf("[") == -1)
								{
									var tag_name = $(this).get(0).tagName.toLowerCase();
									var new_value = $(field_name, xml).text();
									var field_type = $(this).attr("type");
	
									if((tag_name == "input" && field_type == "checkbox") || (tag_name == "input" && field_type == "radio"))
									{
										if($(this).val() == new_value)
										{
											$(this).attr("checked", true);
										}
										else
										{
											$(this).attr("checked", false);
										}
									}
									else if((tag_name == "input" && field_type == "password"))
									{
										$(this).val("");
									}
									else
									{
										$(this).val(new_value);
									}
	
									if(tag_name == "select")
									{
										row.find(".jempe_view"+field_name).html($(this).find("option:selected").text());
									}
									else if((tag_name == "input" && field_type == "password"))
									{
										row.find(".jempe_view"+field_name).html("*****");
									}
									else
									{
										row.find(".jempe_view"+field_name).html(new_value);
									}
								}
							});
							row.find(".jempe_view").show();
							row.find(".jempe_edit").hide();
							row.removeAttr("id");

							editable_row = false;
			
							$(".jempe_add_row_button").removeAttr("disabled");
						}
			
					});
				}
				function jempe_add_row(table)
				{
					var table_body = table.find("tbody");

					newRow = table.find("#jempe_blankRow").clone().prependTo(table_body).show();

					jempe_show_edit_fields(newRow);
				}
			';
		}
	
		return $this->results_table($table_data, $columns, FALSE, $pagination, FALSE, $empty_row, $table_id);	
	}

	// ------------------------------------------------------------------------
	
	/**
	* Process thumb
	*
	* Create thumb for an specific image
	*
	* @access	public
	* @param	string id of image (image_id column of jempe_images table)
	* @param	string thumb type
	* @param	string array with paremeters to crop thumb
	* @return	string
	*/
	
	function process_thumb($image_id, $thumb_name, $parameters = FALSE)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library("jempe_cms");
		$CI->load->library("jempe_images");
	
		$image_info = $CI->db->get_where("jempe_images", array("image_id" => $image_id));
		$image_info = $image_info->row_array();
	
		$image_manager_folder = upload_path().$CI->jempe_cms->upload_images_config["upload_path"];
	
		if( ! file_exists($image_manager_folder."thumbs/".$thumb_name))
		{
			mkdir($image_manager_folder."thumbs/".$thumb_name);
		}

		$thumb_config = $CI->jempe_cms->images_thumbs[$thumb_name];
	
		$destination = $image_manager_folder."thumbs/".$thumb_name."/".$image_info["image_file"];
		$source = $image_manager_folder."original/".$image_info["image_file"];
	
		if(file_exists($source))
		{
			copy($source, $destination);
	
			if(is_array($parameters))
			{
				$CI->load->library("image_lib");
		
				$config["image_library"] = "gd2";
				$config["source_image"] = $destination;
				$config["x_axis"] = $parameters["x_axis"];
				$config["y_axis"] = $parameters["y_axis"];
				$config["width"] = $parameters["width"];
				$config["height"] = $parameters["height"];
				$config["maintain_ratio"] = false;
				
				$CI->image_lib->initialize($config);
				
				$CI->image_lib->crop();
			}
		
			$image_size = getimagesize($destination);
		
			if($thumb_config["type"] == "proportional")
			{
				if( ! isset($thumb_config["width"]))
				{
					$thumb_config["width"] = $image_size[0];
				}
		
				if( ! isset($thumb_config["height"]))
				{
					$thumb_config["height"] = $image_size[1];
				}

				if( ! ($image_size[0] == $thumb_config['width'] && $image_size[1] == $thumb_config['height']))
				{
					$CI->jempe_images->process_proportional($destination, $thumb_config["width"], $thumb_config["height"]);
				}
			}

			if($thumb_config["type"] == "cropped")
			{
				if( ! ($image_size[0] == $thumb_config['width'] && $image_size[1] == $thumb_config['height']))
				{
					$CI->jempe_images->process_cropped($destination, $thumb_config["width"], $thumb_config["height"]);
				}
			}

			if($thumb_config["type"] == "background")
			{
				$CI->jempe_images->process_background($destination, upload_path().$thumb_config["image"], $thumb_config["width"], $thumb_config["height"]);
			}

			if($thumb_config["type"] == "watermark")
			{
				$CI->jempe_images->process_watermark($destination, upload_path(). $thumb_config["image"], $thumb_config["width"], $thumb_config["height"], $thumb_config["opacity"]);
			}

			if(isset($thumb_config["mask"]) && file_exists(upload_path().$thumb_config["mask"]) && getimagesize(upload_path().$thumb_config["mask"]))
			{
				$CI->jempe_images->process_mask($destination, upload_path().$thumb_config["mask"], $thumb_config["width"], $thumb_config["height"]);
			}

			$write_db = $CI->load->database('write', TRUE);

			$write_db->select('thumb_id');
			$thumb_exists = $write_db->get_where('jempe_thumbs', array('thumb_image' => $image_id, 'thumb_type' => $thumb_name));

			$thumb_size = getimagesize($destination);

			$thumb_data = array(
				'thumb_type' => $thumb_name,
				'thumb_image' => $image_id,
				'thumb_name' => $image_info['image_name'],
				'thumb_url' => upload_url(),
				'thumb_path' => str_replace(upload_path(), '', $destination),
				'thumb_width' => $thumb_size[0],
				'thumb_height' => $thumb_size[1],
				'thumb_filesize' => filesize($destination)
			);

			$CI->load->library('jempe_db');

			if($thumb_exists->num_rows() > 0)
			{
				$thumb_exists = $thumb_exists->row_array();

				$thumb_data['thumb_id'] = $thumb_exists['thumb_id'];

				$CI->jempe_db->update_except('jempe_thumbs', 'thumb_id', $thumb_data);
			}
			else
			{
				$thumb_data['thumb_id'] = $CI->jempe_db->insert_except('jempe_thumbs', $thumb_data);
			}

			if(is_array($CI->jempe_cms->execute_after_upload))
			{
				$post_upload_model = $CI->jempe_cms->execute_after_upload['Model'];
				$post_upload_function = $CI->jempe_cms->execute_after_upload['action'];
	
				$CI->load->Model($post_upload_model);
	
				$CI->$post_upload_model->$post_upload_function($thumb_data['thumb_id']);
			}

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// ------------------------------------------------------------------------
	
	/**
	* Delete thumb
	*
	* @access	public
	* @param	string image id
	* @param	string thumb name
	* @return	string
	*/
	
	function delete_thumb($image_id, $thumb_name)
	{
		$CI =& get_instance();
		$CI->load->database();
		$CI->load->library("jempe_cms");
	
		$image_info = $CI->db->get_where("jempe_images", array("image_id" => $image_id));
		$image_info = $image_info->row_array();
	
		if($image_info["image_type"] == "image")
		{
			$image_manager_folder = upload_path().$CI->jempe_cms->upload_images_config["upload_path"];

			if(file_exists($image_manager_folder."thumbs/".$thumb_name."/".$image_info["image_file"]))
			{
				unlink($image_manager_folder."thumbs/".$thumb_name."/".$image_info["image_file"]);
			}
		
			if(file_exists($image_manager_folder."original/".$image_info["image_file"]))
			{
				unlink($image_manager_folder."original/".$image_info["image_file"]);
			}
		}
		else
		{
			$image_manager_folder = upload_path().$CI->jempe_cms->upload_docs_config["upload_path"];

			if(file_exists($image_manager_folder."files/".$image_info["image_file"]))
			{
				unlink($image_manager_folder."files/".$image_info["image_file"]);
			}
		}
	}

	 // ------------------------------------------------------------------------

	 /**
	 * xml_results
	 *
	 * Convert an associative array in an xml string
	 *
	 * @access	public
	 * @param	array data to convert
	 * @return	string
	 */
	function xml_results($data)
	{
		$CI =& get_instance();
		$CI->load->helper("xml");
	
		$output = '<?xml version="1.0" encoding="'.$CI->config->item('charset').'" ?>'."\n"  .'<jempe_results>'."\n";

		$output .= $this->xml_item_data($data);
	
		$output .= "\n".'</jempe_results>';
	
		return $output;
	}

	 // ------------------------------------------------------------------------

	 /**
	 * xml item data
	 *
	 * Process every node of data of xml_results function
	 *
	 * @access	public
	 * @param	array data to convert
	 * @param	string name of previous node
	 * @return	string
	 */
	function xml_item_data($data, $previous_node_name = FALSE)
	{
		$output = '';

		foreach( $data as $var_name=>$value ){
			if(is_array($value))
			{
				if(is_numeric($var_name))
				{
					$var_name = preg_replace("/s$/","", $previous_node_name);
				}

				$output.= '<'.$var_name.'>'."\n".$this->xml_item_data($value, $var_name)."\n".'</'.$var_name.'>'."\n";
			}
			else
			{
				$output.= '<'.$var_name.'>'.xml_convert(html_entity_decode ($value)).'</'.$var_name.'>'."\n";
			}
		}

		return $output;
	}

	 // ------------------------------------------------------------------------

	 /**
	 * recaptcha field
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	function form_recaptcha()
	{
		$error = NULL;

		require_once(APPPATH.'libraries/recaptchalib.php');

		if($this->recaptcha_error !== FALSE)
		{
			$error = $this->recaptcha_error;
		}

		return recaptcha_get_html($this->recaptcha_public_key, $error);
	}


	 // ------------------------------------------------------------------------

	 /**
	 * Change textcaptcha question
	 *
	 * @access	public
	 * @param	integer
	 * @return	string
	 */
	function reset_textcaptcha()
	{
		$CI =& get_instance();
		if($CI->session->userdata("jempe_textcaptcha_id") > 0)
		{
			$CI->session->unset_userdata("jempe_textcaptcha_id");
		}
	}

	 // ------------------------------------------------------------------------

	 /**
	 * textcaptcha field
	 *
	 * @access	public
	 * @param	integer
	 * @return	string
	 */
	function form_textcaptcha()
	{
		$use_db_text_captcha = FALSE;

		$CI =& get_instance();
		if(function_exists("curl_init") && $CI->config->item('language') == 'english' && ! ($CI->session->userdata("jempe_textcaptcha_id") > 0))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.textcaptcha.com/".$this->textcaptcha_key);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$captcha = curl_exec($ch);
			curl_close($ch);

			if($captcha === FALSE)
			{
				log_message("error", curl_error($ch));
				$use_db_text_captcha = TRUE;
			}
			else
			{
				require_once(APPPATH.'libraries/phpQuery.php');
				phpQuery::newDocumentXML($captcha)->find('captcha');

				if(pq("question")->length > 0 && pq("answer")->length > 0)
				{
					$question = pq("question")->text();
					$md5_question = md5($question);

					$CI->load->library('jempe_db');
					$captcha_id = $CI->jempe_db->insert_except("jempe_text_captcha_questions", array(
						"tc_question_english" => $question,
						"tc_question_md5" => $md5_question					));

					$CI->session->set_userdata("jempe_textcaptcha_id", $captcha_id);

					for($i = 0; $i < pq("answer")->length; $i++)
					{
						$CI->jempe_db->insert_except("jempe_text_captcha_answers", array(
							"tc_answer_english" => pq("answer")->eq($i)->text(),
							"tc_answer_question" => $captcha_id
						));
					}
				}
				else
				{
					$use_db_text_captcha = TRUE;
				}
			}
		}
		else
		{
			$use_db_text_captcha = TRUE;
		}

		if($use_db_text_captcha && ! ($CI->session->userdata("jempe_textcaptcha_id") > 0))
		{
			$CI->db->order_by("tc_question_id", "random");
			$CI->db->select("tc_question_id");
			$CI->db->where("tc_question_".$CI->config->item("language")." IS NOT NULL", NULL, FALSE);
			$captcha_question_id = $CI->db->get('jempe_text_captcha_questions');

			if($captcha_question_id->num_rows() > 0)
			{
				$captcha_question_id = $captcha_question_id->rows_array();

				$CI->session->set_userdata("jempe_textcaptcha_id", $captcha_question_id["tc_question_id"]);
			}
		}

		$CI->db->select("tc_question_".$CI->config->item("language")." AS question", FALSE);
		$captcha_question = $CI->db->get_where('jempe_text_captcha_questions', array('tc_question_id' => $CI->session->userdata("jempe_textcaptcha_id")));

		$captcha_question = $captcha_question->row_array();

		return "<label>".$captcha_question["question"]."</label>".$this->form_input("jempe_textcaptcha", $CI->input->post("jempe_textcaptcha"));
	}

	 // ------------------------------------------------------------------------

	 /**
	 * Jquery Drag and Drop lists
	 *
	 *
	 * @access	public
	 * @param	string
	 * @param	array key/pair of options
	 * @param	array
	 * @param	string available list id tag
	 * @param	string selected list id tag
	 * @return	string
	 */
	function drag_and_drop_lists($name, $list, $selected, $available_list_id, $selected_list_id)
	{
		$this->form_jquery_ui('ui');

		$template = $this->drag_and_drop_lists_template;

		$available_items = str_replace(array('{id}', '{name}'), array($available_list_id, $name),  $template['available_list_open']);

		foreach($list as $item_id => $item_name)
		{
			if( ! in_array($item_id, $selected))
			{
				$available_items .= str_replace
				(
					array('{item_id}', '{item_name}'),
					 array($item_id, $item_name),
					$template['available_list_item']
				);
			}
		}

		$available_items .= $template['available_list_close'];

		$selected_items = str_replace(array('{id}', '{name}'), array($selected_list_id, $name),  $template['selected_list_open']);

		foreach($list as $item_id => $item_name)
		{
			if(in_array($item_id, $selected))
			{
				$list_item_name = $item_name.$this->form_hidden($name.'[]', $item_id);

				$selected_items .= str_replace
				(
					array('{item_id}', '{item_name}'),
					 array($item_id, $list_item_name),
					 $template['selected_list_item']
				);
			}
		}

		$selected_items .= $template['selected_list_close'];

		return str_replace(
			array('{init}', '{available_items}', '{selected_items}', '{select_all}', '{remove_all}'),
			array(
				"convert_to_drop_down_lists('".$name."', '".$available_list_id."', '".$selected_list_id."')",
				$available_items,
				$selected_items,
				"jempe_dd_select_all('".$available_list_id."', '".$selected_list_id."', '".$name."')", "jempe_dd_remove_all('".$available_list_id."', '".$selected_list_id."')"
			),
			$template['container_template']
		);
	}

	 // ------------------------------------------------------------------------

	 /**
	 * Convert a php variable to javascript
	 *
	 *
	 * @access	public
	 * @param	mixed variable to convert
	 * @return	string
	 */
	function phpvar_to_js($php_var)
	{
		if(is_string($php_var))
		{
			if(strpos($php_var, 'function(') === FALSE)
			{
				return '"'.$php_var.'"';
			}
			else
			{
				return $php_var;
			}
		}
		else if(is_bool($php_var))
		{
			if($php_var == TRUE)
			{
				return "true";
			}
			else
			{
				return "false";
			}
		}
		else if(is_float($php_var) OR is_int($php_var))
		{
			return $php_var;
		}
	}

	 // ------------------------------------------------------------------------

	 /**
	 * Create a google map page API V3
	 *
	 *
	 * @access	public
	 * @param	array google maps config
	 * @return	string
	 */
	function google_map_box($config = array())
	{
		if(isset($config['sensor']) && $config['sensor'])
		{
			$sensor = 'true';
		}
		else
		{
			$sensor = 'false';
		}

		

		return '
			<!DOCTYPE html>
			<html>
				<head>
					<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
					<style type="text/css">
						html { height: 100% }
						body { height: 100%; margin: 0px; padding: 0px }
						#map_canvas { height: 100% }
					</style>
					<script type="text/javascript"
						src="http://maps.google.com/maps/api/js?sensor='.$sensor.'">
					</script>
					<script type="text/javascript">
					function initialize() {
					var latlng = new google.maps.LatLng(-34.397, 150.644);
					var myOptions = {
					zoom: 8,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					var map = new google.maps.Map(document.getElementById("map_canvas"),
						myOptions);
					}
					</script>
			</head>
			<body onload="initialize()">
			<div id="map_canvas" style="width:100%; height:100%"></div>
			</body>
			</html>
		';
	}
}

// END Jempe_form Library

/* End of file jempe_form.php */
/* Location: ./application/libraries/jempe_form.php */