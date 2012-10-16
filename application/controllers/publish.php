<?php

class Publish extends CI_Controller{

	function __construct()
	{
		parent::__construct();
		$this->load->library('jempe_admin');
		$this->load->database();
		$this->jempe_cms->cache_enabled = FALSE;

		$this->folder = "/home/kastro/sites/kastro/s3files/";

		$this->sitemap = base_url() ."sitemap.xml";
		$this->orig_base_url = $this->base_url = base_url();

		$this->publish_url = "http://kastro.jempe.org/";
	}

	function tohtml()
	{
		if( ! $this->jempe_admin->is_authenticated())
		{
			redirect('/admin/login');
		}

		if( ! ($this->uri->segment(3) > 0))
		{
			$page_id = 1;
		}
		else
		{
			$page_id = $this->uri->segment(3);
		}

		$this->load->library('jempe_cms');
		$this->load->helper('file');

		$this->db->group_by("structure_id");
		$this->db->where("structure_id", $page_id);
		$this->db->from('jempe_structure_bind');
		$this->db->from('jempe_structure');
		$this->db->where('sb_structure = structure_id');
		$this->db->where('structure_id = content_id');
		$pages = $this->db->get('jempe_content');

		$this->config->set_item('base_url', $this->publish_url);

		if($pages->num_rows() > 0)
		{
			$page = $pages->row_array();

			$_SERVER["REQUEST_URI"] =  $uri = str_replace($this->jempe_cms->page_link(1), "/", $this->jempe_cms->page_link($page["structure_id"]));

			if($this->uri->segment(4) !== FALSE)
			{
				$_SERVER["REQUEST_URI"] .= "/".$this->jempe_cms->paginate_keyword."/".$this->uri->segment(4);
				$uri = $_SERVER["REQUEST_URI"];
				$_POST[$this->jempe_cms->paginate_keyword] = $this->uri->segment(4);
				$pag_number = $this->uri->segment(4);
			}

			$uri_segments = explode("/", $uri);

			$this->uri->segments = array();

			for($i = 1; $i < count($uri_segments); $i++)
			{
				if(strlen($uri_segments[$i]))
				{
					$this->uri->segments[$i] = $uri_segments[$i];
				}
			}

			$path_array = $this->uri->segments;

			$this->jempe_cms->create_structure();

			$data = $this->jempe_cms->page_data;

			if(isset($data["model"]))
			{
				$this->load->model($data["model"], '', TRUE);
				$data = $this->$data["model"]->action($data);
			}

			if(isset($data["model_template"]))
			{
				$this->load->model($data["model_template"], '', TRUE);
				$data = $this->$data["model_template"]->action($data);
			}

			$this->load->view($this->jempe_cms->template, $data);

			$path = $this->folder;

			if($data["structure_id"] != 1)
			{
				foreach($path_array as $folder)
				{
					$path .= $folder ."/";

					if( ! file_exists($path))
					{
						mkdir($path,0755);
					}
				}
			}

			write_file( $path .'index.html', $this->output->get_output());
			$this->output->set_output('');

			if(isset($this->pagination->cur_page) && $this->pagination->next_page_uri != FALSE)
			{
				if(isset($pag_number))
				{
					$pag_number = $this->pagination->next_page_uri;
				}
				else
				{
					$pag_number = $this->pagination->cur_page;
				}

				$this->config->set_item('base_url', $this->orig_base_url);

				echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
					<html>
					<head>
					<title>Your Page Title</title>
					<meta http-equiv="REFRESH" content="0;url='.site_url('admin/tohtml/'.$page_id.'/'.$pag_number).'"></HEAD>
					<BODY>
					Optional page text here.
					</BODY>
					</HTML>';

			}else{

				$this->db->order_by("structure_id" , "asc");
				$this->db->limit(1);
				$this->db->select("structure_id");
				$this->db->where("structure_id >" , $page_id );
				$this->db->from('jempe_structure_bind');
				$this->db->from('jempe_structure');
				$this->db->where('sb_structure = structure_id');
				$this->db->where('structure_id = content_id');
				$siguiente = $this->db->get('jempe_content');
		
				if( $siguiente->num_rows() > 0 )
				{
					$siguiente = $siguiente->row_array();

					$this->config->set_item('base_url', $this->orig_base_url);
		
					echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
					<html>
					<head>
					<title>Your Page Title</title>
					<meta http-equiv="REFRESH" content="0;url='.site_url('admin/tohtml/'.$siguiente["structure_id"]).'"></HEAD>
					<BODY>
					Optional page text here.
					</BODY>
					</HTML>';
				}
			}
		}
	}
}

?>