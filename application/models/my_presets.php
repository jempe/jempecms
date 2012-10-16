<?php

Class My_Presets extends CI_Model{
	
	function __construct(){
	
		$this->presets = array(
			'post' // list of preset functions
		);
	
	}
	
	function post()
	{
		if(count($_POST) > 0)
		{
			if(strlen(trim($this->input->post('content_title'))))
			{
				$_POST['content_url'] = url_title($this->input->post('content_title'));
		
				$_POST['content_link_name'] = $this->input->post('content_title');
		
			}
			else
			{
				$_POST['content_url'] = $_POST['content_link_name'] = time();
			}
		}
	}

}

// END My Presets Model

/* End of file base.php */
/* Location: /application/models/base.php */