<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title><?=$this->lang->line('jempe_page_login') ?></title>
	<meta name="generator" content="jempe" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="expires" content="-1" />
	<link type="text/css" href="<?= static_url() ?>jempe_admin.css" rel="StyleSheet" />
</head>
<body class="jempe_body" >


	<div id="jempe_login_container" class="jempe_style">

		<form method="post" action="<?php echo $this->jempe_admin->admin_url('login') ?>">
			<div id="jempe_login">
				<div id="jempe_logo_login" style="width:50%">
					<div>&nbsp;</div>
				</div>
				<div id="jempe_login_form">
					<div style="height:80px;  width:135px;">
						<label><?=$this->lang->line('jempe_field_user_username') ?></label><br>
						<input type="text" name="user_username" value="<?= $this->input->post('user_username') ?>" style="width:120px"  />
					</div>
					<div style="height:80px;  width:135px;">
						<label><?=$this->lang->line('jempe_field_user_password') ?></label><br>
						<input type="password" name="user_password" style="width:120px"  />
					</div>
					<div style="height:80px; width:120px;">
						<label>&nbsp;</label><br>
						<input type="submit" name="action" value="<?=$this->lang->line('jempe_button_login') ?>" class="jempe_button"  />
					</div><br>
					<div style="height:18px; width:48%;" class="jempe_error">
						<?php if(isset($error)) echo $error; ?>
					</div>
				</div>
			</div>
		</form>

	</div>




</body>

</html>
