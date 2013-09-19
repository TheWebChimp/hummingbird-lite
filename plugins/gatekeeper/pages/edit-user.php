<?php
	$cur_dir = sprintf( '%s/parts', dirname(__FILE__) );
	$gatekeeper->requireLogin('admin', '/admin/users');

	$delete = isset( $_GET['delete'] );

	if ($delete) {
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		$gatekeeper->deleteUser($id);
		$site->redirectTo( $site->urlTo('/manage/gatekeeper') );
	}

	if ($_POST) {
		$id = isset($_POST['id']) ? $_POST['id'] : '';
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
		$role = isset($_POST['role']) ? $_POST['role'] : '';
		//
		$ret = array();
		if ($password == '' || $password == $confirm) {
			$fields = array(
				'name' => $name,
				'email' => $email,
				'nickname' => $nickname,
				'password' => $password,
				'role' => $role
			);
			if ($password == '') {
				unset( $fields['password'] );
			}
			$ret['result'] = $gatekeeper->updateUser($id, $fields);
		} else {
			$ret['result'] = false;
		}
		//
		if ( $site->isAjaxRequest() ) {
			echo json_encode($ret);
			exit;
		}
	}

	try {
		$dbh = $site->getDatabase();
		$id = isset($_GET['id']) ? $_GET['id'] : '';
		$sql = "SELECT id, name, email, nickname, password, registered, status, role FROM gk_user WHERE id = :id";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(':id', $id);
		$stmt->execute();
		$row = $stmt->fetch();
	} catch (PDOException $e) {
		echo 'Database error: ' . $e->getMessage();
		exit;
	}
?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>
			<ul class="breadcrumb">
				<li>
					<a href="<?php $site->urlTo('/admin/users', true) ?>">Users</a> <span class="divider">/</span>
				</li>
				<li class="active">
					Edit user
				</li>
			</ul>
			<div id="alert-ok" class="alert alert-success hide"> The user has been updated.</div>
			<div class="row">
				<form id="edit-user" method="post" class="form-horizontal span6">
					<input type="hidden" name="id" value="<?php echo $id ?>">
					<div class="control-group">
						<label class="control-label" for="name">Name</label>
						<div class="controls">
							<input type="text" name="name" class="input-block-level" id="name" value="<?php echo $row->name ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="email">Email</label>
						<div class="controls">
							<input type="text" name="email" class="input-block-level" id="email" value="<?php echo $row->email ?>">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="nickname">Nickname</label>
						<div class="controls">
							<input type="text" name="nickname" class="input-block-level" id="nickname" value="<?php echo $row->nickname ?>">
						</div>
					</div>
					<div id="toggle" class="control-group">
						<div class="controls">
							<a href="#">Change password</a>
						</div>
					</div>
					<div id="password-change" class="hide">
						<div class="control-group">
							<label class="control-label" for="password">Password</label>
							<div class="controls">
								<input type="password" name="password" class="input-block-level" id="password" autocomplete="off" value="">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="confirm">Confirm password</label>
							<div class="controls">
								<input type="password" name="confirm" class="input-block-level" id="confirm" autocomplete="off" value="">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="role">Role</label>
						<div class="controls">
							<select name="role" class="input-block-level" id="role">
								<option value=""></option>
								<?php
									$roles = $gatekeeper->getUserRoles();
									if ($roles):
										foreach ($roles as $role => $name):
								?>
								<option value="<?php echo $role ?>" <?php if ($row->role == $role) echo('selected="selected"'); ?>><?php echo $name ?></option>
								<?php
										endforeach;
									endif;
								?>
							</select>
						</div>
					</div>
					<div class="text-right">
						<a href="<?php $site->urlTo('/admin/users', true) ?>" class="btn">Cancel</a>
						<button class="btn btn-success">Save changes</button>
					</div>
				</form>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>