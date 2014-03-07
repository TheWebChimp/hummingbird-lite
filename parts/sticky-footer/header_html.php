<!DOCTYPE html>
<html lang="en">
<head>
	<?php $site->metaTags(); ?>
	<title><?php echo $site->getPageTitle() ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<link rel="icon" href="<?php $site->urlTo('/favicon.png', true) ?>" type="image/png">
	<script type="text/javascript">
		var constants = {
			siteUrl: '<?php $site->urlTo("", true) ?>',
			ajaxUrl: '<?php $site->urlTo("/ajax", true) ?>'
		};
	</script>
	<?php $site->includeStyles() ?>
	<?php $site->includeScript('modernizr'); ?>
</head>
<body class="<?php $site->bodyClass() ?>">