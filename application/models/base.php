<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Base extends CI_Model{

	function action($data)
	{
		$this->load->library('jempe_form');
		$menu_pages = $this->jempe_cms->child_pages(0);
	
		$data['jempe_menu'] = array();
	
		for($i=0; $i < count($menu_pages); $i++)
		{
			$page = $menu_pages[$i];
	
			$submenu_pages = $this->jempe_cms->child_pages($page['structure_id']);
	
			for($j = 0; $j < count($submenu_pages); $j++)
			{
				$page['jempe_pages'] = $submenu_pages;	
			}

			$data['jempe_menu'][] = $page;	
		}
	
		return $data;
	}
}

// END Base Model

/* End of file base.php */
/* Location: /application/models/base.php */