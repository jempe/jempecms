<?php header("Content-Type: text/html; charset=" .$this->config->item('charset') ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?= $this->config->item('charset') ?>"/>
	<meta name="description" content=""/>
	<meta name="keywords" content=""/> 
	<meta name="author" content="author"/> 
	<meta name="generator" content="Jempe" />
	<link rel="stylesheet" type="text/css" href="<?= static_url() ?>jempe.css" media="screen"/>
	<title><?= $this->jempe_cms->clean_tags($title) ?></title>
	<?= $this->jempe_form->form_jquery() ?>
	<?= $this->jempe_form->form_jquery_ready() ?>
</head>

<body>
<?php $this->jempe_cms->show_menu($structure_id) ?>
<div class="container">

	<div class="top">
		<a href="<?= $this->jempe_cms->page_link(1) ?>"><span>Website.com</span></a>
	</div>
	
	<div class="header"></div>
		
	<div class="main">
<?php $this->load->view($template,$jempe_content) ?>
	</div>

	<div class="navigation">
<?php foreach($jempe_menu as $menu){ ?>
		<h1><a href="<?= $this->jempe_cms->page_link($menu["structure_id"]) ?>"><?= $menu["link_name"] ?></a></h1>
<?php if(isset($menu["jempe_pages"]) && count($menu["jempe_pages"]) ){ ?>
		<ul>
<?php 	foreach($menu["jempe_pages"] as $menu2){ ?>
			<li><a href="<?= $this->jempe_cms->page_link(array( $menu , $menu2)) ?>"><?= $menu2["link_name"] ?></a></li>
<?php } ?>
		</ul>
<?php  } } ?>

	</div>
	
	<div class="clearer"><span></span></div>

	<div class="footer"> <?= date('Y') ?> <a href="<?= $this->jempe_cms->page_link(1) ?>">Website.com</a>. <?= $this->lang->line('site_template_design') ?> <a href="http://templates.arcsin.se">Arcsin</a>. <?= $this->lang->line('site_powered_by') ?> <A target="_blank" href="http://www.jempe.org/">Jempe CMS</A>

	</div>

</div>

</body>

</html>