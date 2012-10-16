<?php if(isset($success)){ ?>
	<div class="jempe_div_field">
		<label><?= $this->lang->line('jempe_message_password_changed') ?></label>
	</div>
<?php }else{ ?>
	<?php if(isset($error) ) { ?><div class="jempe_error"><?=$error; ?></div><?php } ?>
	<form action="<?= $this->jempe_admin->admin_url('admin_password') ?>" method="post">
	
		<div class="jempe_div_field">
			<label><?= $this->lang->line('jempe_button_old_password') ?></label>
			<input type="password" name="old_password" /><br>
		</div>
	
		<div class="jempe_div_field">
			<label><?= $this->lang->line('jempe_button_new_password') ?></label>
			<input type="password" name="user_password" /><br>
		</div>
	
		<div class="jempe_div_field">
			<label><?= $this->lang->line('jempe_button_confirm_password') ?></label>
			<input type="password" name="confirm_password" /><br>
		</div>
	
		<div style="float:left;  width:100%; margin:20px 0; ">
			<input type="hidden" name="user_id" value="<?= $this->input->post('user_id') ?>">
			<input type="submit" value="<?= $this->lang->line('jempe_button_save_changes' ) ?>"  class="jempe_button" />
		</div>
	</form>
<?php } ?>
