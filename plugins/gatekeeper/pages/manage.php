<?php
	$cur_dir = sprintf( '%s/parts', dirname(__FILE__) );

	if ($_POST) {
		$token = isset($_POST['token']) ? $_POST['token'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
		$check = $site->hashToken('install_gatekeeper');
		if ($token == $check && !empty($password) && $password == $confirm) {
			if ( $gatekeeper->install() ) {
				$user_id = $gatekeeper->createUser('admin', $email, 'admin', $password, 1, 'admin');
				$site->redirectTo('/admin/users');
			}
		}
	}

	if ( $gatekeeper->isInstalled() ) {
		$gatekeeper->requireLogin('admin', '/admin/users');
	}

	$roles = $gatekeeper->getUserRoles();

?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>

			<?php if ( $gatekeeper->isInstalled() ): ?>

			<div class="alert alert-success">The Gatekeeper plugin is up and running</div>
			<p>
				<a class="btn" href="<?php $site->urlTo('/admin/users/add-user', true) ?>">Create new user</a>
			</p>
			<table class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Nickname</th>
						<th>Role</th>
						<th>Edit</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$dbh = $site->getDatabase();
						try {
							$sql = "SELECT id, name, email, nickname, password, registered, status, role FROM gk_user";
							$stmt = $dbh->prepare($sql);
							$stmt->execute();
							$rows = $stmt->fetchAll();
						} catch (PDOException $e) {
							echo 'Database error: ' . $e->getMessage();
						}
						if ($rows):
							foreach ($rows as $row):
					?>
					<tr>
						<td><?php echo $row->id ?></td>
						<td><?php echo $row->name ?></td>
						<td><?php echo $row->email ?></td>
						<td><?php echo $row->nickname ?></td>
						<td><?php echo $roles[$row->role] ?></td>
						<td><a href="<?php $site->urlTo('/admin/users/edit-user', true) ?>?id=<?php echo $row->id ?>">Edit</a></td>
						<td>
							<?php if ($row->name != 'admin'): ?>
							<a href="<?php $site->urlTo('/admin/users/edit-user', true) ?>?delete=true&amp;id=<?php echo $row->id ?>">Delete</a>
							<?php else: ?>
							<span class="muted">Delete</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php
							endforeach;
						endif;
					?>
				</tbody>
			</table>

			<?php else: ?>

			<div class="alert">The Gatekeeper plugin is not installed properly</div>
			<form method="post" class="form-horizontal">
				<input type="hidden" name="token" value="<?php $site->hashToken('install_gatekeeper', true) ?>">
				<h4>Administrator account</h4>
				<div class="control-group">
					<label class="control-label" for="email">Email</label>
					<div class="controls">
						<input type="email" name="email" id="email" autocomplete="off">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input type="password" name="password" id="password" autocomplete="off">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password_confirm">Confirm password</label>
					<div class="controls">
						<input type="password" name="password_confirm" id="password_confirm" autocomplete="off">
					</div>
				</div>
				<p>
					<button class="btn btn-success">Install plugin now</button>
				</p>
			</form>

			<?php endif ?>

		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>