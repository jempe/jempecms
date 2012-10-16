<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| CMS FIELD SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed for the CMS fields.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['jempe_cms_fields'] list of CMS fields containing name type and column names for every language.
|	['jempe_theme'] Name of the views folder that contains the CMS templates
|	['jempe_images_thumbs'] List of the thumbs types created
|	['jempe_presets'] list of the fields preset for different page types
*/

$config["jempe_cms_fields"] = array(
		'title' 	=> 	array("name" => "title", "type" => "text"),
		'url' 		=> 	array("name" => "url" , "type" => "url"),
		'link_name'	=>	array("name" => "link_name", "type" => "text"),
		'text'		=>	array("name" => "text", "type" => "htmlarea", "thumb" => "jempe")
	);

$config["jempe_theme"] = "jempe";

$config["jempe_upload_files_config"] = array(
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

$config['jempe_first_page_is_root'] = 1; //set the home page link, useful in case jempe home page is not the site home page

$config['jempe_cache_driver'] = array('adapter' => 'apc');


/* End of file jempe.php */
/* Location: ./application/config/jempe.php */
