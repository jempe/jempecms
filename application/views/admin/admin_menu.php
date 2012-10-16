<?php
 	echo $this->jempe_form->form_jquery();
	echo $this->jempe_form->form_jquery_ready(); 
?>
	<link type="text/css" href="<?php echo static_url() ?>jempe_inline_editor.css" rel="StyleSheet" />
<div style="position:fixed; width:100%; background:black; padding:0; margin:0; z-index:100; ">
	<div style="float:left; background:url(<?php echo static_url() ?>images/background_menu.png) repeat-x; ">
<?php 
		echo $this->jempe_admin->admin_menu_spacer;
		echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_index'), $this->jempe_cms->page_link($this->jempe_admin->admin_home_page()));
		echo $this->jempe_admin->admin_menu_spacer;

		if($this->jempe_admin->page_permission('new', $this->session->userdata('user_id'), 'insert'))
		{
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_new_page'), 'admin/new_page', 'jempe_new_page');
			echo $this->jempe_admin->admin_menu_spacer;
		 } 
?>
<?php 
		if(isset($id) && $id > 0 && $this->jempe_admin->page_permission($id, $this->session->userdata('user_id')))
		{ 
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_edit_page'), 'admin/page_edit/'.$id);
			echo $this->jempe_admin->admin_menu_spacer;
		} 
?>
<?php 
		if(isset($structure_blocked) && $structure_blocked == 0 && $this->jempe_admin->page_permission($id, $this->session->userdata('user_id')))
		{ 
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_delete_page'), 'admin/delete/'.$id, TRUE);
			echo $this->jempe_admin->admin_menu_spacer;
		} 
?>
<?php 
		if(isset($id) && $id > 0 && isset($jempe_order) && $jempe_order)
		{ 
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_change_order'), 'admin/edit_order/'.$id);
			echo $this->jempe_admin->admin_menu_spacer;
		}
?>
<?php 
		echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_delete_cache'), 'admin/delete_cache'); 
		echo $this->jempe_admin->admin_menu_spacer;
?>
<?php 		
		if($this->jempe_admin->user_permission('change_password', 'write'))
		{
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_change_admin_password'), 'admin/admin_password', TRUE);
			echo $this->jempe_admin->admin_menu_spacer;
?>
<?php 
		} 
?>
<?php 
		if($this->jempe_admin->user_permission('users', 'read'))
		{ 
			echo $this->jempe_admin->admin_menu_link($this->lang->line('jempe_button_users'), 'admin/users');
			echo $this->jempe_admin->admin_menu_spacer;
		}

		echo $this->jempe_admin->admin_menu_link( $this->lang->line('jempe_button_logout'), 'admin/logout');
?>
	</div>
</div>
<?php  

$jempe_edit_buttons_style = "color:#666;text-decoration:none;float:left;font-size:11px;padding:2px 4px;margin-right:2px;border:solid 1px #666;background:white;border-top:none;";

?>
<div id="jempe_edit_frame"></div>
<div id="jempe_edit_buttons" style="display:none;position:absolute;" >
	<a href="javascript:void(0);" onclick="jempe_edit_cancel()" style="<?= $jempe_edit_buttons_style ?>"><?= $this->lang->line('jempe_button_cancel') ?></a>
	<a href="javascript:void(0);" onclick="$('#form_inline_editor').submit();" style="<?= $jempe_edit_buttons_style ?>"><?= $this->lang->line('jempe_button_save_changes') ?></a>
</div>