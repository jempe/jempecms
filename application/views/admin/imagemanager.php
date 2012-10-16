<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title><?= $title ?></title>
	<meta name="generator" content="jempe" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $this->config->item('charset'); ?>" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<link type="text/css" href="<?= static_url() ?>jempe_admin.css" rel="StyleSheet" />

	<?= $this->jempe_form->form_jquery() ?>
	<script type="text/javascript" src="<?= static_url() ?>jquery/image_manager.js"></script>
	<?= $this->jempe_form->form_jquery_ready() ?>
	<style>
		#jempe_popup , #jquery_popup{
			width:750px;
			height:550px;
		}
		a.jempe_button{
			 padding:4px;
		}
	</style>
  </head>
  <body  class="jempe_body" >
	<div id="jempe_manager_container" class="jempe_style">
	<?php $this->load->view($template , $manager_content )  ?>

		<div id="select_thumb_popup" class="jempe_body">
<?php
		if(is_array($thumbs))
		{
?>
			<div id="jempe_container" class="jempe_style">
			<h2><?= $this->lang->line('jempe_select_thumb_size') ?></h2>
<?php
			foreach($thumbs as $thumb_id)
			{
?>
				<p><a href="javascript:void(0);" onclick="insert_tinymce_images(insert_image_ids, '<?= $thumb_id ?>')"><?php
					$thumb_name = $this->lang->line('jempe_thumb_'.$thumb_id);

					if(strlen($thumb_name))
					{
						echo $thumb_name;
					}
					else
					{
						echo $thumb_id;
					}
				?></a></p>
<?php
			}
?>
				<p>&nbsp;</p>
			</div>
<?php
		}
?>
		</div>
	</div>
  </body>
</html>