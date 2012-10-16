	<h1><?=$this->lang->line('jempe_title_user_type') ?></h1>
	<?= $this->form_validation->error_string('<div class="jempe_error">' , '</div>') ?>
<?php if(isset( $success )){ ?>
	<div class="jempe_error"><?= $this->lang->line('jempe_message_user_type_saved') ?></div>
<?php } ?>
	<form method="post" action="<?= $this->jempe_admin->admin_url('edit_user_type/' . $this->uri->segment(3) ) ?>">

	<div class="jempe_div_field">
		<label><?= $this->lang->line('jempe_column_user_type_name') ?></label>
		<?= $this->jempe_form->form_input('user_type_name', $_POST ) ?>
	</div>
	<?= $this->jempe_form->form_hidden_id('user_type_id', $_POST) ?>
	<div class="jempe_div_field">
		<h1><?= $this->lang->line('jempe_permissions_list') ?></h1>
	</div>
	<div class="jempe_div_field">
		<table >
			<thead>
				<tr>
					<th><?= $this->lang->line('jempe_column_permission') ?></th>
<?php if($this->jempe_form->verify_form){ ?>
					<th width="100"><?= $this->lang->line('jempe_permissions') ?></th>
<?php }else{ ?>
					<th width="100"><?= $this->lang->line('jempe_column_no_access') ?></th>
					<th width="100"><?= $this->lang->line('jempe_column_read') ?></th>
					<th width="100"><?= $this->lang->line('jempe_column_write') ?></th>
<?php } ?>
				</tr>
			</thead>
			<tbody>
<?php foreach( $permissions as $permission ){ ?>
				<tr>
					<td>
						<?= $permission["permission_name"] ?>
					</td> 
					<?= $this->jempe_form->form_radiomultiple( 'permission_' .$permission["permission_id"] , $permission_types , $_POST  ) ?>
				</tr>
<?php } ?>
			</tbody>
		</table>

	</div>

	<div class="jempe_div_field">
		<h1><?= $this->lang->line('jempe_permission_pages') ?></h1>
	</div>

	<div id="permissions_tree" class="jempe_div_field">
		<?= $permissions_tree ?>
	</div>

<?php if(!$this->jempe_form->verify_form){ ?>
	<div style="float:left;  width:100%; margin:20px 0; ">
		<input type="submit" value="<?= $this->lang->line('jempe_button_save_changes' ) ?>"  class="jempe_button" />
	</div>
<?php } ?>

	</form>

