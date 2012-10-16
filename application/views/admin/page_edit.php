<?php 
	if(isset($error))
	{  
		echo $error; 
	}
?>
<form action="<?php echo $this->jempe_admin->admin_url('page_edit') ?>" enctype="multipart/form-data" method="post">
<?php 
	if($this->input->post('content_fields_preset'))
	{
		echo $this->jempe_form->form_hidden('content_fields_preset', $this->input->post('content_fields_preset'));
	}

	foreach($cms_fields as $field)
	{ 
		if($fields[$field]['type'] == 'hidden')
		{
?>
		<?php echo $fields[$field]['field']; ?>
<?php
		}
		else
		{
?>
	<div class="jempe_div_field">
		<label><?php echo $fields[$field]['label'] ?></label>
		<?php echo $fields[$field]['field'] ?><br>
	</div>
<?php
		}
	} 
?>
		<div class="jempe_div_field">
			<label><?php 
				if($fields['is_category']['type'] != 'hidden')
				{
					echo $fields['is_category']['label']; 
				}
				?></label>
			<?php echo $fields['is_category']['field'] ?>
		</div>
<?php 		
		if($this->jempe_cms->field_preset('sb_parent') === FALSE)
		{ 
?>
		<div class="jempe_div_field">
			<label><?php echo $fields['parent']['label'] ?></label>
			<?php echo $fields['parent']['field'] ?><br>
		</div>
<?php 
		}
		else
		{ 
?>
		<input type="hidden" name="sb_parent[]" value="<?php echo $this->jempe_cms->field_preset('sb_parent') ?>" />
<?php
 		}
 
		if( ! $this->jempe_cms->field_preset('content_template'))
		{
?>
		<div class="jempe_div_field">
			<label><?php echo $fields['template']['label'] ?></label>
			<?php echo $fields['template']['field'] ?>
		</div>
<?php 
		}
		else
		{ 
?>
		<input type="hidden" name="content_template" value="<?php echo $this->jempe_cms->field_preset('content_template') ?>" />
<?php 
		} 

		if($fields['template_general']['type'] != 'hidden')
		{ 
?>
		<div class="jempe_div_field">
			<label><?php echo $fields['template_general']['label'] ?></label>
			<?php echo $fields['template_general']['field'] ?>
		</div>
<?php 
		}
		else
		{ 
			echo $fields["template_general"]['field'];
		} 

		if(isset($fields['id']))
		{ 
			echo $fields['id']['field']; 
		}
?>
		<div style="float:left;  width:100%; margin:20px 0; ">
			<input type="submit" value="<?php echo $this->lang->line('jempe_button_save_changes') ?>"  class="jempe_button" />
		</div>

</form>