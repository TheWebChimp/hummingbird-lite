<?php
	/**
	 * Hummingbird Lite
	 * Version: 	2.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
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
		$file = sprintf('%s/plugins/%s/plugin.php', ABSPATH, $plugin);
		include $file;
	}

	# External functions
	include ABSPATH . '/external/functions.inc.php';

	# Do routing
	$site->routeRequest();
?>