<?php
	# Environment
	$base_dir = dirname(__FILE__);

	# Configuration
	@include $base_dir . '/include/config.inc.php';
	@include $base_dir . '/include/database.inc.php';
	@include $base_dir . '/include/functions.inc.php';

	# Check installation status
	if ( !isset($site_url) ) {
		die('Please check your configuration file');
		exit;
	} else {
		# Routing stuff, first get the site url
		$site_url = trim($site_url, '/');

		# Remove the protocol from it
		$domain = preg_replace('/^(http|https):\/\//', '', $site_url);

		# Now remove the path
		$segments = explode('/', $domain, 2);
		if (count($segments) > 1) {
			$domain = array_pop($segments);
		}

		# Get the request and remove the domain
		$request = trim($_SERVER['REQUEST_URI'], '/');
		$request = str_replace($domain, '', $request);
		$request = ltrim($request, '/');
		# Get the parameters
		$segments = explode('?', $request);
		if (count($segments) > 1) {
			$params = array_pop($segments);
		}

		# And the segments
		$segments = array_shift($segments);
		$segments = explode('/', $segments);

		# Find the requested page
		if (count($segments) == 0) {
			$slug = 'home';
		} else {
			$slug = array_pop($segments);
			if ( empty($slug) ) {
				$slug = 'home';
			}
		}
		$page = sprintf('%s/pages/%s.php', $base_dir, $slug);
		if ( !in_array($slug, $site_pages) || !file_exists($page) ) {
			# The page does not exist
			$slug = '404';
			$page = sprintf('%s/pages/%s.php', $base_dir, $slug);
			header('HTTP/1.0 404 Not Found');
		}

		# Display the requested page
		include $page;
	}
?>