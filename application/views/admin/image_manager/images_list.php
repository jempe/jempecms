		<div id="jempe_manager_list_buttons">
			<div style="float:right;width:5px;height:20px;"></div>
			<button id="delete_images" class="jempe_button" name="Delete"><?= $this->lang->line($labels_prefix.'_delete_images') ?></button>
		</div>
		<div id="jempe_manager_menu">
			<div class="jempe_manager_spacer" style="height:560px;" ></div>
				<div style="float:left; width:275px;padding-bottom:25px;">
					<form method="post" id="jempe_search_form" action="<?= site_url($this->uri->segment(1).'/'.$this->uri->segment(2)) ?>">
						<p><?= $this->lang->line($labels_prefix.'_search_image') ?>:<br /><?= $this->jempe_form->form_input( 'jempe_search' , $this->input->post('jempe_search') , 'id="search_image_field"' ); ?> <input type="submit" value="<?= $this->lang->line($labels_prefix.'_search') ?>" id="search_image_button" /></p>
					</form>
		
					<hr style="margin-top:20px;" />
<?php if( isset( $upload_fields ) ){ ?>
					<strong><?= $this->lang->line($labels_prefix.'_new_image') ?></strong><br>
					<form enctype="multipart/form-data" method="post" id="upload_images_form" action="<?= site_url($this->uri->segment(1).'/'.$this->uri->segment(2)) ?>">
					<input type="hidden" id="upload_quantity" name="quantity" value="1" />
					<div id="upload_fields">
						<p><input type="file" name="image_1" style="width:180px;" /></p>
					</div>
<?php
	if($this->uri->segment(2) != "file_manager")
	{
?>
					<p id="new_upload" ><a id="new_upload_button" href="javascript:void(0);"><?= $this->lang->line($labels_prefix.'_another_image') ?></a></p>
<?php
	}
?>

					<p>
						<div style="float:left;width:144px;height:20px;"></div>
						<input type="submit" value="<?= $this->lang->line($labels_prefix.'_upload_file') ?>" class="jempe_button" /></p>

					<p><label><?= $this->lang->line($labels_prefix.'_tags') ?>:</label> <?= $this->jempe_form->form_input('jempe_tags',$this->input->post('jempe_tags') , ' id="jempe_tags" style="width:260px;" '); ?></p>

					</form>

					<hr />
<?php } ?>

					<strong><?= $this->lang->line($labels_prefix.'_tags') ?></strong><br>
<?php if(count($tags) ){ ?>
					<ul>
<?php foreach( $tags as $tag ){ ?>
						<li><a href="javascript:void(0)" class="jempe_tag" rel="<?= $tag["tag_name"] ?>"><?= $tag["tag_name"] ?> (<?= $tag["cuantos"] ?>)</a></li>
<?php } ?>
					</ul>
<?php } ?><br /><br />
				</div>
			<div class="jempe_manager_spacer" ></div>
		</div>
		<div id="jempe_manager_files">
			<?= $this->form_validation->error_string('<div class="jempe_error">' , '</div>' ) ?>
			<?= $manager_files ?>
		</div>