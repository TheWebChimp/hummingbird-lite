<?php
	$return = isset($_REQUEST['return']) ? $_REQUEST['return'] : 'home';

	if ( $gatekeeper->logout() ) {
		$redirect = sprintf('/%s', $return);
		$site->redirectTo($redirect);
	}
?>