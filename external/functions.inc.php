<?php
	/**
	 * functions.inc.php
	 * Add extra functions in this file
	 */

	# Basic set-up ---------------------------------------------------------------------------------

	# Include styles -------------------------------------------------------------------------------

	# Structure
	$site->registerStyle('reset', $site->baseUrl('/css/reset.css') );
	$site->registerStyle('boilerplate', $site->baseUrl('/css/boilerplate.css') );
	$site->registerStyle('sticky-footer', $site->baseUrl('/css/sticky-footer.css') );
	$site->registerStyle('mobile', $site->baseUrl('/css/mobile.css') );

	# Fonts
	$site->registerStyle('google.open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,300,700,800,800italic,400italic,300italic|Open+Sans+Condensed:300,700,300italic');

	$site->registerStyle('desktop', $site->baseUrl('/css/desktop.css'), array(

		# Structure
		'reset',
		'boilerplate',
		'sticky-footer',
		'mobile',

		# Fonts
		'google.open-sans'
	));
	$site->enqueueStyle('desktop');

	# Include scripts ------------------------------------------------------------------------------

	$site->registerScript('script', $site->baseUrl('/js/script.js'), array(
		'jquery'
	));
	$site->enqueueScript('script');

	# Include extra files
	include $site->baseDir('/external/utilities.inc.php');
	include $site->baseDir('/external/ajax.inc.php');

	# Meta tags
	$site->addMeta('UTF-8', '', 'charset');
	$site->addMeta('viewport', 'width=device-width, initial-scale=1');

	$site->addMeta('og:title', $site->getPageTitle(), 'property');
	$site->addMeta('og:site_name', $site->getSiteTitle(), 'property');
	$site->addMeta('og:description', $site->getSiteTitle(), 'property');
	$site->addMeta('og:image', $site->urlTo('/favicon.png'), 'property');
	$site->addMeta('og:type', 'website', 'property');
	$site->addMeta('og:url', $site->urlTo('/'), 'property');

	# Pages ----------------------------------------------------------------------------------------
	// $site->addPage('sample', 'sample-page');
?>