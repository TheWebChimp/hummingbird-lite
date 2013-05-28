<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $site->getPageTitle() ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<?php $site->includeStyles() ?>
</head>
<body class="<?php $site->bodyClass() ?>">
	<div class="container" id="main">