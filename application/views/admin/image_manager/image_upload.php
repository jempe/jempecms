		<div id="jempe_manager_list_buttons" style="position:fixed;">
			<button id="cancel_edit" name="Delete"><?= $this->lang->line('jempe_manager_cancel_edit') ?></button>
		</div>
		<div class="jempe_manager_spacer"></div>
		<div id="jempe_manager_files" style="width:900px;margin-top:30px;">
			<?= $this->form_validation->error_string('<div class="jempe_error">' , '</div>' ) ?>
<?php if( isset($success) ){ ?>
			<div class="jempe_error"><?= $this->lang->line('jempe_manager_upload_success') ?></div>
<?php } ?>
<?php if( isset($error) ){ ?>
			<div class="jempe_error"><?= $error ?></div>
<?php } ?>
<?php if(isset($image)){

$image_size = getimagesize(upload_path().$this->jempe_cms->upload_images_config["upload_path"] ."original/" .$image );

if( $image_size[0] > $image_size[1] )
	$dim = "height";
else
	$dim = "width";

 ?>
			<div class="jempe_div_field">
				<a id="jempe_image_preview" href="javascript:void(0);" rel="<?= $image ?>" ><img src="<?= upload_url().$this->jempe_cms->upload_images_config["upload_path"] ?>original/<?= $image ?>?random=<?= time() ?>" style="<?= $dim ?>:300px;" /></a>
			</div>
<?php } ?>
			<div class="edit_image jempe_div_field" style="display:<?php if(!isset($image) ) echo "none"; else echo "block" ?>;"><button id="replace_image"><?= $this->lang->line('jempe_manager_replace_image') ?></button></div>
			<div style="display:<?php if(isset($image) ) echo "none"; else echo "block" ?>;" class="upload_image">
				<form enctype="multipart/form-data" method="post" action="<?= $this->jempe_admin->admin_url('image_upload/' . $this->input->post('image_id') ) ?>">
	
					<div class="jempe_div_field">
						<?= $this->lang->line('jempe_manager_select_file') ?>:<br />
						<input type="file" name="jempe_image" />
					</div>
	
					<?= $this->jempe_form->form_hidden_id('image_id' , $_POST , 'id="image_id"') ?>
					<div style="margin: 20px 0pt; float: left; width: 100%;">
						<input class="jempe_button" type="submit" name="upload" value="<?= $this->lang->line('jempe_manager_upload_file') ?>" />
					</div>
	
				</form>
			</div>

<?php if(isset($image) && $this->input->post('image_id') > 0 ){ ?>

			<div class="edit_image">
			<form method="post" action="<?= $this->jempe_admin->admin_url('image_upload/' . $this->input->post('image_id') ) ?>">

				<div class="jempe_div_field">
					<?= $this->lang->line('jempe_manager_column_image_name') ?>:<br />
					<?= $this->jempe_form->form_input('image_name',$_POST); ?>
				</div>

				<div class="jempe_div_field">
					<?= $this->lang->line('jempe_manager_tags') ?>:<br />
					<?= $this->jempe_form->form_input('jempe_tags',$this->input->post('jempe_tags') , ' id="jempe_tags" '); ?>
				</div>

				<?= $this->jempe_form->form_hidden_id('image_id' , $_POST) ?>
				<div style="margin: 20px 0pt; float: left; width: 100%;">
					<input class="jempe_button" type="submit" name="save" value="<?= $this->lang->line('jempe_button_save_changes') ?>" />
				</div>

			</form>
			<h3><?= $this->lang->line('jempe_manager_thumbs') ?></h3>

<?php foreach( $this->jempe_cms->images_thumbs as $thumb_name=>$thumb_config){ ?>
			<div class="jempe_div_field">
				<label><?= $thumb_name ?></label>
				<img src="<?= upload_url().$this->jempe_cms->upload_images_config["upload_path"] ?>thumbs/<?= $thumb_name ."/" .$image ?>?random=<?= time() ?>" />
			</div>	
			<div class="jempe_div_field">
				<label><a class="jempe_button" href="<?= $this->jempe_admin->admin_url('image_thumb/' . $this->input->post('image_id') .'/' .$thumb_name ) ?>" rel="popup"><?= $this->lang->line('jempe_manager_edit_thumb') ?></a></label>
			</div>
<?php } ?>

			</div>
<?php } ?>
			<p>&nbsp;</p>
		</div>