<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <title><?= $title ?></title>
	<meta name="generator" content="jempe" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<link type="text/css" href="<?= static_url() ?>jempe_admin.css" rel="StyleSheet" />

	<?= $this->jempe_form->form_jquery() ?>
	<?= $this->jempe_form->form_jquery_ready() ?>
<?php if( !isset( $success ) ){ ?>
	<link type="text/css" href="<?= static_url() ?>jquery/jcrop.css" rel="StyleSheet" />
	<script language="Javascript">
	jQuery(function(){
		jQuery('#jempe_crop').Jcrop({
			bgOpacity:0.5,
			onChange: crop_coords
<?php if( count( $crop_setup ) ){ 
	foreach( $crop_setup as $crop_variable=>$crop_value ){ 
?>
			,<?= $crop_variable ?>: <?php if( is_array($crop_value) ) echo '[' .implode( ',' , $crop_value ) .']' ; else echo $crop_value; ?>
<?php 	} 

} ?>
		});
	});
	function crop_coords(coords)
	{
		$("#crop_x").val(coords.x);
		$("#crop_y").val(coords.y);
		$("#crop_height").val(coords.h);
		$("#crop_width").val(coords.w);
	}

	</script>
<?php } ?>
	<style>
		.jempe_button{
			border:1px solid #B1D028;
			font-size:12px;
		}
	</style>
  </head>
  <body class="jempe_body" >
	<div id="jempe_crop_buttons" style="margin:5px;">
<?php if(isset($success) ){ ?>
		<div style="float:left;width:250px;">
			<button class="jempe_button"  onclick="top.location.href='<?= $this->jempe_admin->admin_url('image_upload/' . $this->uri->segment(3) ) ?>'"><?= $this->lang->line('jempe_manager_crop_done') ?></button>
		</div>
		<div style="float:left;width:250px;">
			<form method="post" action="<?= $this->jempe_admin->admin_url('image_thumb/' . $this->uri->segment(3) .'/' .$this->uri->segment(4) ) ?>">
				<button class="jempe_button" ><?= $this->lang->line('jempe_manager_crop_again') ?></button>
			</form>
		</div>
<?php }else{ ?>
		<form method="post" action="<?= $this->jempe_admin->admin_url('image_thumb/' . $this->uri->segment(3) .'/' .$this->uri->segment(4) ) ?>">
			<input type="submit" class="jempe_button" name="crop" value="<?= $this->lang->line('jempe_button_save_changes') ?>" />
			<input type="hidden" name="crop_x" id="crop_x" />
			<input type="hidden" name="crop_y" id="crop_y" />
			<input type="hidden" name="crop_width" id="crop_width" />
			<input type="hidden" name="crop_height" id="crop_height" />
		</form>
<?php } ?>
	</div>
	<div style="float:left;">
		<table>
			<tr>
				<td style="width:740px;height:500px;" valign="middle" align="center">
	
		<table align="center">
			<tr>
				<td id="jempe_crop_image" valign="middle">
					<?php if( isset($success) ){ ?>
							<img src="<?= $image ?>?time=<?= time() ?>" id="jempe_crop"  />
					<?php }else{ ?>
	
						<img src="<?= $image ?>?time=<?= time() ?>" id="jempe_crop" <?php if( isset( $resize ) ){ ?> style="<?= $resize ?>" <?php } ?> />
	
					<?php } ?>
				</td>
			</tr>
		</table>
	
				</td>
			</tr>
		</table>
	</div>
	
  </body>
</html>

