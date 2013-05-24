<?php
	/**
	 * functions.inc
	 * Add any extra utility functions in this file
	 */

	# Include styles
	$site->registerStyle('style', $site->baseUrl('/css/style.css') );
	$site->enqueueStyle('style');

	# Include scripts
	$site->enqueueScript('jquery');

	# Add routes
	$site->addRoute('/ajax', 'Site::ajaxRequest');
	$site->addRoute('/:page', 'Site::getPage');

	# Add pages
	$site->addPage('home', 'home-page');

	# Sample AJAX action
	function ajax_test() {
		echo '<pre>'.print_r($_REQUEST, true).'</pre>';
		exit;
	}
	$site->addAjaxAction('test', 'ajax_test');

	# Localization
	$i18n->addLocale('en', ABSPATH . '/plugins/i18n/lang/enUS.php');
	$i18n->setLocale('en');
?>