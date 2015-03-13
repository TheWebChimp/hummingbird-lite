<?php
	/**
	 * Hummingbird Lite (WebChimp Flavor)
	 * Version: 	2.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
	 *				webchimp <github.com/webchimp>
	 */

	# Define the absolute path
	define( 'ABSPATH', dirname(__FILE__) );

	# Include required files
	include ABSPATH . '/include/config.inc.php';
	include ABSPATH . '/include/site.inc.php';

	# Initialize environment
	$site = new Site($settings);

	# Initialize plugins
	foreach ($site->getPlugins() as $plugin) {
		$file = $site->baseDir("/plugins/{$plugin}/plugin.php");
		include $file;
	}

	# External functions
	include $site->baseDir('/external/functions.inc.php');

	# Do routing
	$site->routeRequest();
?>