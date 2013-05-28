<?php
	$cur_dir = dirname(__FILE__);

	if ( isset($gatekeeper) ) {
		$gatekeeper->requireLogin('admin', '/admin/cms');
	}

	if ($_POST) {
		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$check = $site->hashToken('install_cms');
		if ($token == $check) {
			if ( $cms->install() ) {
				$site->redirectTo('/admin/cms/settings');
			}
		}
	}

?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>
			<div class="row">
				<div class="span3">
					<?php $site->getParts(array('sidebar'), $cur_dir) ?>
				</div>
				<div class="span9">
					<h4>General settings</h4>
				</div>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>