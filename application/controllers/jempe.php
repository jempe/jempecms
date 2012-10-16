<?php

Class Jempe extends CI_Controller{

	function __construct()
	{
		parent::__construct();
// 		$this->output->enable_profiler(TRUE);
	}

	function index()
	{
		if($this->config->item('jempe_theme') != "")
		{
			$this->load->database();

			if($this->jempe_cms->cache_enabled)
			{
				$home_cache_key = $this->jempe_cms->cache_prefix.'jempe_home_exists';
			}

			if( ! isset($home_cache_key) OR (isset($home_cache_key) && ($home_page_exists = $this->cache->get($home_cache_key)) === FALSE))
			{
				$this->db->select('content_id');
				$index_page = $this->db->get_where('jempe_content', array('content_id' => 1));
				$home_page_exists = $index_page->num_rows();

				if(isset($home_cache_key))
				{
					$this->cache->save($home_cache_key, $home_page_exists, $this->jempe_cms->cache_time);
				}
			}

			if($home_page_exists == 0)
			{
				redirect('admin');
			}

			$this->load->library('jempe_cms');

			//create pages tree
			$this->jempe_cms->create_structure();
	
			// page data
			$data = $this->jempe_cms->page_data;
	
			// is there a model file with the same name of the template?
			if(isset($data["model"]))
			{
				$this->load->model($data["model"], '', TRUE);
				$data = $this->$data["model"]->action($data);
			}
	
			// is there a model file with the same name of the template?
			if(isset($data["model_template"]))
			{
				$this->load->model($data["model_template"], '', TRUE);
				$data = $this->$data["model_template"]->action($data);
			}

			// add admin menu if admin is logged in
			if($this->session->userdata("user_id"))
			{
				$this->load->library('jempe_form');
				$this->load->library('jempe_admin');
				$this->jempe_admin->admin_menu_functions();

				$this->jempe_form->javascript_functions[] = '
					var jempe_inline_editor_url = "'.site_url('admin/inline_field').'";
					var jempe_inline_save_url = "'.site_url('admin/save_field').'";
					var jempe_timeout_error = "'.$this->lang->line('jempe_error_ajax_timeout').'";
					var jempe_error_try_again = "'.$this->lang->line('jempe_error_ocurred').'";
				';

				$this->jempe_form->jquery_functions[] = '
					$(".jempe_edit_button").click( function(c){
						c.preventDefault();
					});
				';

				$this->jempe_form->form_htmlarea('lang_line_'.$this->config->item('language'), FALSE);

			}
			else
			{
				$data["jempe_admin_menu"] = "";
			}
	
			$this->load->view($this->jempe_cms->template, $data);

			// cache files only if admin is not logged in
			if($this->jempe_cms->cache && ! $this->session->userdata("user_id"))
			{
				$this->output->cache($this->jempe_cms->cache_time);
			}
		}
		else
		{
			redirect('admin/install');
		}
	}
}

// END Jempe Controller

/* End of file jempe.php */
/* Location: ./application/controllers/jempe.php */