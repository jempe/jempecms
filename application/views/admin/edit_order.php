<form method="post" action="<?= $this->jempe_admin->admin_url('edit_order/' . $this->uri->segment(3) ) ?>">
	<h1><?=$this->lang->line('jempe_title_pages_list') ?></h1>

	<?php if( isset($success) ){ ?> <div style="margin-top:15px;" class="jempe_error"><?= $this->lang->line('jempe_message_order_changed') ?></div> <?php } ?>

	<div class="jempe_div_field">
		<?= $sortable_list ?>
	</div>
	<div style="float:left;  width:100%; margin:20px 0; ">
		<input type="hidden" name="change_order" value="1">
		<input type="hidden" name="sb_parent" value="<?= $this->input->post('sb_parent')  ?>">
		<input type="submit" value="<?=$this->lang->line('jempe_button_save_changes') ?>" class="jempe_button"  >
	</div>
</form>