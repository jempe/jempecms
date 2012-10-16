<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
  <title><?= $title ?></title>
	<meta name="generator" content="jempe" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?= $this->config->item('charset'); ?>" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<link type="text/css" href="<?= static_url() ?>jempe_admin.css" rel="StyleSheet" />
	<?= $this->jempe_form->form_jquery() ?>
	<?= $this->jempe_form->form_jquery_ready() ?>
</head>
<body class="jempe_body" >
	<div id="jempe_container" class="jempe_style" >
		<h2><?= $title ?></h2>
	<?php if( isset( $submenu ) ){ ?>
		<div id="jempe_submenu" style="margin-top:10px;">
			<?php $this->load->view('admin/menu/' .$submenu ) ?>
		</div>
		<div class="jempe_block" >
			<div class="jempe_left"></div>
			<div class="jempe_right"></div>
		</div>
		<?php if( isset( $breadcrumb ) ){ ?>
		<div id="jempe_breadcrumb">
		<?php for( $i=0;$i<count($breadcrumb);$i++){  ?>
			<a href="<?= $this->jempe_admin->admin_url($breadcrumb[$i]["url"]) ?>" <?php if($i==count($breadcrumb)-1) echo 'style="font-weight:bold;"'; ?> ><?= $breadcrumb[$i]["name"] ?></a>
			<span ><?php if($i<count($breadcrumb)-1) echo '&raquo; '; ?></span>
		<?php } ?>
		</div>
		<?php }else{ ?>
		<div style="float:left;width:100%;height:25px;">&nbsp;</div>
		<?php } ?>
	<?php }else{ ?>
		<div class="jempe_block" style="margin:25px 0;">
			<div class="jempe_left"></div>
			<div class="jempe_right"></div>
		</div>
	<?php } ?>
		<?php $this->load->view($template,$admin_content) ?>
		<div class="jempe_block" style="margin-top:15px;">
			<div class="jempe_left"></div>
			<div class="jempe_right"></div>
		</div>
	</div>
</body>
</html>
