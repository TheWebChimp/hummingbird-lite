<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $site->getPageTitle() ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<script type="text/javascript">
		document.createElement('header');
		document.createElement('hgroup');
		document.createElement('nav');
		document.createElement('menu');
		document.createElement('section');
		document.createElement('article');
		document.createElement('aside');
		document.createElement('footer');
		var constants = {
			siteUrl: '<?php $site->urlTo("", true) ?>',
			ajaxUrl: '<?php $site->urlTo("/ajax", true) ?>'
		};
	</script>
	<?php $site->includeStyles() ?>
</head>
<body class="<?php $site->bodyClass() ?>">
	<div class="container" id="main">