<?php 
	if(isset($error))
	{  
		echo $error; 
	} 
?>
<form action="<?php echo $this->jempe_admin->admin_url('new_page') ?>" id="new_page_form" method="post">
<?php 
	if($this->input->post('content_fields_preset'))
	{
		echo $this->jempe_form->form_hidden('content_fields_preset',$this->input->post('content_fields_preset'));
	}

	foreach($cms_fields as $field)
	{ 
?>
	<div class="jempe_div_field page_data_fields" >
		<label><?php echo $fields[$field]['label'] ?></label>
		<?php echo $fields[$field]['field'] ?><br>
	</div>
<?php 
	} 
?>
	<div class="jempe_div_field page_data_fields">
		<label><?php 
		if($fields['is_category']['type'] != 'hidden')
		{
			echo $fields['is_category']['label'];
		}
		?></label>
		<?php echo $fields['is_category']['field'] ?>
	</div>

	<div class="jempe_div_field template_fields" style="display:none;width:250px;">
		<label><?php echo $fields['template']['label'] ?></label>
		<?php echo $fields['template']['field'] ?>
	</div>
	<div class="jempe_div_field template_fields" id="template_previews" style="display:none;width:200px;">
<?php 
	foreach($templates_list as $template_file => $template_name)
	{ 
		if( ! $this->input->post('content_template') && ! isset($selected_template))
		{
			$selected_template = $template_file;
		}
		else
		{
			$selected_template = $this->input->post('content_template');
		}

		if(file_exists(JEMPEPATH.'admin/image_manager/templates/'.str_replace('.php', '.png', $template_file)))
		{
?>
			<img src="<?php echo base_url().'admin/image_manager/templates/'.str_replace('.php', '.png', $template_file) ?>" class="preview_<?php echo str_replace('.php', '', $template_file) ?>" <?php 
				if($selected_template != $template_file)
				{
					echo 'style="display:none;"';
				}
				?> />
<?php
		} 
	} 
?>
	</div>

	<div class="jempe_div_field categories_fields" style="display:none;">
		<label><?php echo $fields['parent']['label'] ?></label>
		<?php echo $fields['parent']['field'] ?><br>
	</div>

	<div style="float:left;  width:100%; margin:20px 0;" class="page_data_fields" >
		<input type="submit" value="<?php echo $this->lang->line('jempe_button_continue') ?>"  class="jempe_button" />
	</div>

	<div style="float:left;  width:100%; margin:20px 0; display:none;" class="template_fields">
		<input type="submit" value="<?php echo $this->lang->line('jempe_button_continue') ?>"  class="jempe_button" />
	</div>

	<div style="float:left;  width:100%; margin:20px 0;display:none;" class="categories_fields" >
		<input type="submit" value="<?php echo $this->lang->line('jempe_button_save_changes') ?>"  class="jempe_button" />
	</div>

</form>