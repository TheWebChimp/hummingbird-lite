<?php
	/**
	 * Site settings
	 * @var array 	Array with configuration options
	 */
	$settings = array(
		'development' => array(
			# Global settings
			'site_url' => 'http://localhost/webchimp/GitHub/hummingbird-lite/',
			# Database settings
			'db_driver' => 'none', 		# none, sqlite or mysql
			'db_host' => 'localhost',
			'db_user' => 'root',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => ABSPATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array(
				'i18n',
				'gatekeeper'
			)
		),
		'production' => array(
			# Global settings
			'site_url' => 'http://elchangodelaweb.com/site',
			# Database settings
			'db_driver' => 'none',
			'db_host' => '',
			'db_user' => '',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => ABSPATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array(
				'i18n',
				'gatekeeper'
			)
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