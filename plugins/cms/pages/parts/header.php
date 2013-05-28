<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $site->getPageTitle('CMS') ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<?php
		$site->includeStyle('bootstrap');
		$site->includeStyle('bootstrap-responsive');
		$site->addBodyClass('admin-cms');
	?>
</head>
<body class="<?php $site->bodyClass() ?>">
	<div class="container">
		<header>
			<?php if ( isset($gatekeeper) && $cur_user = $gatekeeper->getCurrentUser() ): ?>
			<h2>CMS <small class="pull-right"><br>Hi, <?php echo $cur_user->nickname ?><a href="<?php $site->urlTo('/logout', true) ?>" class="btn btn-link">Logout</a></small></h2>
			<?php else: ?>
			<h2>CMS</h2>
			<?php endif ?>
			<hr>
		</header>