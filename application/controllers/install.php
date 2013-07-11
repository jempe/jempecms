<?php

Class Install extends CI_Controller{

	function __construct()
	{
		parent::__construct();
		$this->lang->load('jempe_install');
	}

function index(){


	$content["template"] = "requirements";
	$this->load->view('install/base',$content);
}

function create_db(){

	$this->load->dbforge();

	$field_types = array(
		'url' => array(
			'type' => 'VARCHAR',
			'constraint' => '250',
			'null' => FALSE
		),
		'text' => array(
			'type' => 'VARCHAR',
			'constraint' => '250',
			'null' => TRUE
		),
		'file' => array(
			'type' => 'VARCHAR',
			'constraint' => '250',
			'null' => TRUE
		),
		'md5' => array(
			'type' => 'VARCHAR',
			'constraint' => '32',
			'null' => TRUE
		),
		'area' => array(
			'type' => 'TEXT',
			'null' => TRUE,
		),
		'htmlarea' => array(
			'type' => 'TEXT',
			'null' => TRUE,
		),
		'image' => array(
			'type' => 'INT',
			'constraint' => 6,
			'null' => TRUE
		),
		'decimal' => array(
			'type' => 'DECIMAL',
			'constraint' => '10,2',
			'null' => TRUE
		),
		'date' => array(
			'type' => 'DATE',
			'null' => TRUE
		),
		'bool' => array(
			'type' => 'int',
			'constraint' => 1,
			'null' => FALSE,
			'default' => 0
		),
                );

	$content_fields['content_id'] = array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			);

	$table_keys = array(
		'jempe_sessions'			=>	'session_id',
		'jempe_structure'			=>	array('structure_id' => TRUE,'structure_user' => FALSE),
		'jempe_structure_bind'			=>	array('sb_structure' => FALSE, 'sb_parent' => FALSE),
		'jempe_users'				=>	array('user_id' => TRUE, 'user_active' => FALSE, 'user_session_id' => FALSE),
		'jempe_user_types'			=>	'user_type_id',
		'jempe_permissions'			=>	'permission_id',
		'jempe_user_type_permissions'		=>	array('utp_user' => FALSE, 'utp_permission' => FALSE),
		'jempe_user_type_page_permissions'	=>	array('utpp_user_type' => FALSE, 'utpp_structure' => FALSE),
		'jempe_images'				=>	array('image_id' => TRUE, 'image_user' => FALSE),
		'jempe_thumbs' 				=> 	array('thumb_id' => TRUE, 'thumb_image' => FALSE, 'thumb_type' => FALSE),
		'jempe_tags'				=>	array('tag_id' => TRUE, 'tag_user' => FALSE),
		'jempe_image_tags'			=>	array('it_tag' => FALSE, 'it_image' => FALSE),
		'jempe_content'				=>	array('content_id' => TRUE),
		'jempe_history'				=>	array('content_id' => FALSE),
		'jempe_email_log'			=>	array('email_id' => TRUE, 'email_user' => FALSE),
		'jempe_lang'				=>	array('lang_id' => TRUE, 'lang_user' => FALSE, 'lang_key' => FALSE),
		'jempe_text_captcha_questions'	=>	array('tc_question_id' => TRUE),
		'jempe_text_captcha_answers'	=>	array('tc_answer_id' => TRUE, 'tc_answer_question' => FALSE)
	);

	$available_languages = array($this->config->item('language') => $this->config->item('language'));

	foreach($this->config->item('jempe_cms_fields') as $field)
	{
		if(isset($field["languages"]) && is_array($field["languages"]) && count($field["languages"]) > 1)
		{
			$lang_fields = 0;
			foreach($field["languages"] as $language => $db_field)
			{
				$available_languages[$language] = $language;

				$content_fields[$db_field] =  $field_types[$field['type']];

				if($field['name'] == 'url')
				{
					$table_keys['jempe_content'][$db_field] = FALSE;
				}
			}
		}
		else
		{
			$content_fields['content_'.$field['name']] = $field_types[$field['type']];

			if($field['name'] == 'url')
			{
				$table_keys['jempe_content']['content_url'] = FALSE;
			}
		}
	}

	$content_fields['content_is_category'] = array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 0
			);

	$content_fields['content_template'] = array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			);
	$content_fields['content_template_general'] = array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			);
	$content_fields['content_structure'] = array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE,
				'default' => 0
			);

	$content_fields['content_user'] = array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE,
				'default' => 0
			);

	$content_fields['content_timestamp'] = array(
				'type' => 'TIMESTAMP'
			);
	$content_fields['content_fields_preset'] = array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			);

	$language_fields = array(
		'lang_id'=>array(
			'type' => 'INT',
			'constraint' => 6,
			'null' => FALSE,
			'auto_increment' => TRUE
		),
		'lang_key'=>array(
			'type' => 'VARCHAR',
			'constraint' => 250,
			'null' => TRUE
		),
		'lang_user'=>array(
			'type' => 'INT',
			'constraint' => 6,
			'null' => TRUE
		),
		'lang_type'=>array(
			'type' => 'VARCHAR',
			'constraint' => 10,
			'null' => TRUE
		),
	);

	$textcaptcha_question_fields = array(
		'tc_question_id'=>array(
			'type' => 'INT',
			'constraint' => 6,
			'null' => FALSE,
			'auto_increment' => TRUE
		),
		'tc_question_md5' => $field_types['md5'],
		'tc_question_english' => $field_types['text'],
		'tc_question_timestamp' => array(
			'type' => 'TIMESTAMP'
		),
	);
	$textcaptcha_answer_fields = array(
		'tc_answer_id'=>array(
			'type' => 'INT',
			'constraint' => 6,
			'null' => FALSE,
			'auto_increment' => TRUE
		),
		'tc_answer_question' => array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
		),
		'tc_answer_timestamp' => array(
			'type' => 'TIMESTAMP'
		),
	);

	foreach($available_languages as $available_language)
	{
		$language_fields['lang_line_'.$available_language] = $field_types['htmlarea'];
		$textcaptcha_question_fields['tc_question_'.$available_language] = $field_types['text'];
		$textcaptcha_answer_fields['tc_answer_'.$available_language] = $field_types['md5'];
	}

	$tables = array(

		"jempe_sessions"=>array(
			'session_id'=>array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'default' => 0
			),
			'ip_address'=>array(
				'type' => 'VARCHAR',
				'constraint' => '16',
				'null' => TRUE,
				'default' => 0
			),
			'user_agent'=>array(
				'type' => 'VARCHAR',
				'constraint' => '50',
				'null' => TRUE
			),
			'last_activity'=> array(
				'type' => 'INT',
				'constraint' => 10,
				'null' => TRUE,
				'unsigned' => TRUE,
				'default' => 0
			),
			'user_data'=>array(
				'type' => 'TEXT',
				'null' => TRUE
			),
		),
		"jempe_structure"=>array(
			'structure_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'structure_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'structure_blocked'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 0
			),
			'structure_user'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			)
		),
		'jempe_structure_bind'=>array(
			'sb_structure'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE,
				'default' => 0
			),
			'sb_parent'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE,
				'default' => 0
			),
			'sb_order'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE,
				'default' => 0
			)
		),
		'jempe_user_types'=>array(
			'user_type_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_type_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_type_update'=>array(
				'type' => 'TIMESTAMP',
				'null' => TRUE
			),
			'user_type_edited_by'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'user_type_active'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 1
			)
		),
		'jempe_users'=>array(
			'user_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'user_first_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_last_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_email'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_username'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_password' => $field_types['md5'],
			'user_type'=>array(
				'type' => 'INT',
				'constraint' => 4,
				'null' => TRUE
			),
			'user_reset_key'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			),
			'user_reset_expiration'=>array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'user_update'=>array(
				'type' => 'TIMESTAMP',
				'null' => TRUE
			),
			'user_created'=>array(
				'type' => 'DATETIME',
				'null' => TRUE
			),
			'user_edited_by'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'user_session_id'=>array(
				'type' => 'VARCHAR',
				'constraint' => '40',
				'null' => TRUE
			),
			'user_active'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 1
			)
		),
		'jempe_permissions'=>array(
			'permission_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'permission_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE
			)
		),
		'jempe_user_type_permissions'=>array(
			'utp_user'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'utp_permission'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'utp_permission_value'=>array(
				'type' => 'INT',
				'constraint' => 3,
				'null' => TRUE
			)
		),
		'jempe_user_type_page_permissions'=>array(
			'utpp_user_type'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'utpp_structure'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'utpp_new'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 0
			),
			'utpp_edit'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 0
			),
			'utpp_recur'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 0
			)
		),
		'jempe_images'=>array(
			'image_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'image_type'=>array(
				'type' => 'SET',
				'constraint' => "'image', 'file'",
				'null' => FALSE,
				'default' => 'image'
			),
			'image_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'image_file'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'image_extension'=>array(
				'type' => 'VARCHAR',
				'constraint' => 6,
				'null' => TRUE
			),
			'image_user'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'image_active'=>array(
				'type' => 'INT',
				'constraint' => 1,
				'null' => TRUE,
				'default' => 1
			),
			'image_timestamp'=>array(
				'type' => 'TIMESTAMP'
			)
		),
		'jempe_thumbs'=>array(
			'thumb_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'thumb_image'=>array(
				'type' => 'INT',
				'constraint' => 5,
				'null' => TRUE
			),
			'thumb_type'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'thumb_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'thumb_url'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'thumb_path'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'thumb_width'=>array(
				'type' => 'INT',
				'constraint' => 5,
				'null' => TRUE
			),
			'thumb_height'=>array(
				'type' => 'INT',
				'constraint' => 5,
				'null' => TRUE
			),
			'thumb_filesize'=>array(
				'type' => 'INT',
				'constraint' => 12,
				'null' => TRUE
			),
			'thumb_timestamp'=>array(
				'type' => 'TIMESTAMP'
			)
		),
		'jempe_tags'=>array(
			'tag_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'tag_name'=>array(
				'type' => 'VARCHAR',
				'constraint' => 250,
				'null' => TRUE
			),
			'tag_user'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			)
		),
		'jempe_image_tags'=>array(
			'it_tag'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'it_image'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			)
		),
		'jempe_email_log'=>array(
			'email_id'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => FALSE,
				'auto_increment' => TRUE
			),
			'email_user'=>array(
				'type' => 'INT',
				'constraint' => 6,
				'null' => TRUE
			),
			'email_from'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_to'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_reply_to'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_subject'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_message'=>array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'email_alt_message'=>array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'email_cc'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_bcc'=>array(
				'type' => 'VARCHAR',
				'constraint' => '250',
				'null' => TRUE
			),
			'email_attach'=>array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE
			),
			'email_debug'=>array(
				'type' => 'TEXT',
				'null' => TRUE
			),
			'email_timestamp'=>array(
				'type' => 'TIMESTAMP'
			)
		),
		'jempe_content' => $content_fields,
		'jempe_history' => $content_fields,
		'jempe_lang' => $language_fields,
		'jempe_text_captcha_questions' => $textcaptcha_question_fields,
		'jempe_text_captcha_answers' => $textcaptcha_answer_fields
	);

	if($user_additional_fields = $this->config->item('jempe_users_additional_fields'))
	{
		foreach($user_additional_fields AS $add_field_name => $add_field_value)
		{
			$tables['jempe_users'][$add_field_name] = $add_field_value;
		}
	}

	$content["content_template"] = "";

	$this->load->library('jempe_db');

	foreach($tables as $table=>$fields){

		$this->jempe_db->update_table($table , $fields , $table_keys[$table] );
		$content["content_template"]["created_tables"][] = $table;

	}

		$this->load->database();

		$users = $this->db->count_all('jempe_users');

		if($users == 0){
			$admin_data["user_username"]="admin";
			$admin_data["user_password"]= md5("test");
			$admin_data["user_type"]= 1;
			$this->db->insert('jempe_users',$admin_data);
		}

		$user_types = $this->db->count_all('jempe_user_types');

		if($user_types == 0){
			$this->db->insert('jempe_user_types',array('user_type_id'=>1,'user_type_name'=>'admin'));
			$this->db->insert('jempe_user_types',array('user_type_id'=>2,'user_type_name'=>'content_editor'));
		}

		$user_permissions = $this->db->count_all('jempe_permissions');

		if($user_permissions == 0){
			$this->db->insert('jempe_permissions',array('permission_id'=>1,'permission_name'=>'users'));
			$this->db->insert('jempe_permissions',array('permission_id'=>2,'permission_name'=>'change_password'));
			$this->db->insert('jempe_permissions',array('permission_id'=>3,'permission_name'=>'edit_pages_other_users'));
			$this->db->insert('jempe_permissions',array('permission_id'=>4,'permission_name'=>'edit_pages_same_user_type'));
			$this->db->insert('jempe_permissions',array('permission_id'=>5,'permission_name'=>'edit_own_pages'));
			$this->db->insert('jempe_permissions',array('permission_id'=>6,'permission_name'=>'images_other_users'));
			$this->db->insert('jempe_permissions',array('permission_id'=>7,'permission_name'=>'images_same_user_type'));
			$this->db->insert('jempe_permissions',array('permission_id'=>8,'permission_name'=>'own_images'));
			$this->db->insert('jempe_permissions',array('permission_id'=>9,'permission_name'=>'lang'));
		}

	$content["template"] = "tables";
// 	$this->load->view('install/base',$content);	
	var_dump($content);
}

}

?>