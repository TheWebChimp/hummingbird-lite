<?php
	# Environment
	$base_dir = dirname(__FILE__);

	# Configuration
	@include $base_dir . '/include/config.inc.php';
	@include $base_dir . '/include/database.inc.php';
	@include $base_dir . '/include/functions.inc.php';

	# Variables
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$actions = array();

	# --------------------------------------------------------------------------

	function foo() {
		echo 'bar';
		exit;
	}
	array_push($actions, 'foo');

	# --------------------------------------------------------------------------

	# Call the requested action
	if (in_array($action, $actions)) {
		call_user_func($action);
	}
?>