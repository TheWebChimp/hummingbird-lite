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

	# Sample AJAX action
	function ajax_test() {
		echo '<pre>'.print_r($_REQUEST, true).'</pre>';
		exit;
	}
	$site->addAjaxAction('test', 'ajax_test');
?>