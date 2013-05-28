<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $site->getPageTitle('Users') ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<?php
		$site->includeStyle('bootstrap');
		$site->includeStyle('bootstrap-responsive');
		$site->addBodyClass('admin-gatekeeper');
	?>
</head>
<body class="<?php $site->bodyClass() ?>">
	<div class="container">
		<header>
			<?php
				$cur_user = $gatekeeper->getCurrentUser();
				if ($cur_user):
			?>
			<h2>Users <small class="pull-right"><br>Hi, <?php echo $cur_user->nickname ?><a href="<?php $site->urlTo('/logout', true) ?>" class="btn btn-link">Logout</a></small></h2>
			<?php else: ?>
			<h2>Users</h2>
			<?php endif ?>
			<hr>
		</header>