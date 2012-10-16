<form action="<?= $this->jempe_admin->admin_url('delete/' .$this->uri->segment(3)) ?>" method="post">
<?php if( isset($success) ){ ?>
	<p><?= $this->lang->line('jempe_message_page_deleted') ?></p>
<?php }else{ ?>
	<?php if( !isset( $page["structure_id"] ) ){ ?>
		<p><?= $this->lang->line('jempe_message_page_already_deleted') ?></p>
	<?php }else{ ?>
		<p><input type="hidden" name="structure_id" value="<?=$page["structure_id"] ?>"><?=$this->lang->line('jempe_message_want_delete') ?> <?=$page["content_title"] ?> ?</p>
	
	
		<div style="float:left;  width:100%; margin:20px 0; ">
			<input type="submit" name="delete" value="<?=$this->lang->line('jempe_button_delete') ?>">
		</div>
	<?php } ?>
<?php } ?>
</form>
