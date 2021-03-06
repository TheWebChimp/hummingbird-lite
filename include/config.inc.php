<?php
	/**
	 * config.inc.php
	 * Here's where you configure your Hummingbird instance
	 */

	# Set the active profile
	define( 'PROFILE', 'development' );

	/**
	 * Site settings
	 * @var array 	Array with configuration options
	 */
	$settings = array(
		'development' => array(
			# Global settings
			'site_url' => 'http://localhost/hummingbird-lite/',
			# Database settings
			'db_driver' => 'sqlite', # none, sqlite or mysql, even mongodb :)
			'db_host' => 'localhost',
			'db_user' => 'root',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => ABSPATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'testing' => array(
			# Global settings
			'site_url' => 'http://dev.yoursite.com',
			# Database settings
			'db_driver' => 'none',
			'db_host' => '',
			'db_user' => '',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => ABSPATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'production' => array(
			# Global settings
			'site_url' => 'http://yoursite.com',
			# Database settings
			'db_driver' => 'none',
			'db_host' => '',
			'db_user' => '',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => ABSPATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'shared' => array(
			# General
			'site_name' => 'Site name',
			# Security settings
			'pass_salt' => '',
			'token_salt' => ''
		)
	);
?>