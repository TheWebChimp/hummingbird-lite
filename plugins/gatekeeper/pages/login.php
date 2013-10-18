<?php
	$return = isset($_REQUEST['return']) ? $_REQUEST['return'] : 'home';
	$reason = isset($_GET['reason']) ? $_GET['reason'] : false;

	if ($_POST) {
		$user = isset($_POST['user']) ? $_POST['user'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$remember = isset($_POST['remember']) ? $_POST['remember'] : '';
		if ( $gatekeeper->login($user, $password, $remember) ) {
			$redirect = sprintf('/%s', $return);
			$site->redirectTo($redirect);
		} else {
			$login_error = true;
		}
	}
?>
<?php $site->getParts(array('header_html', 'header')) ?>

		<section>
			<?php
				if ( $gatekeeper->getCurrentUserId() ):
					if ($reason == 'perm'):
			?>

			<p><strong>You are already signed in, but you don't have enough permissions to see this page.</strong></p>
			<?php 	else: ?>
			<p><strong>You are already signed in, <a href="<?php $site->urlTo('/logout', true) ?>">click here</a> to sign out.</strong></p>
			<?php 	endif; ?>

			<p><a href="<?php $site->urlTo('/', true); ?>">&laquo; Go back to the main page</a></p>

			<?php else: ?>

			<form method="post">
				<input type="hidden" name="return" value="<?php echo $return ?>">
				<?php if ( isset($login_error) ): ?>
				<p><strong>The user/password combination is not valid</strong></p>
				<?php endif; ?>
				<p>
					<label for="user">User</label>
					<input type="text" name="user" id="user">
				</p>
				<p>
					<label for="password">Password</label>
					<input type="password" name="password" id="password">
				</p>
				<p>
					<label for="remember"><input type="checkbox" id="remember" name="remember"> Remember me</label>
				</p>
				<p>
					<button>Login</button>
				</p>
			</form>

			<?php endif ?>
		</section>

<?php $site->getParts(array('footer', 'footer_html')) ?>