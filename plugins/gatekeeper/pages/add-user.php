<?php
	$cur_dir = sprintf( '%s/parts', dirname(__FILE__) );
	$gatekeeper->requireLogin('admin', '/admin/users');

	if ($_POST) {
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
		$role = isset($_POST['role']) ? $_POST['role'] : '';
		//
		$ret = array();
		if ($password != '' && $confirm == $password) {
			$ret['result'] = $gatekeeper->createUser($name, $email, $nickname, $password, 1, $role);
		} else {
			$ret['result'] = false;
		}
		//
		if ( $site->isAjaxRequest() ) {
			echo json_encode($ret);
			exit;
		}
	}
?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>
			<ul class="breadcrumb">
				<li>
					<a href="<?php $site->urlTo('/admin/users', true) ?>">Users</a> <span class="divider">/</span>
				</li>
				<li class="active">
					Add user
				</li>
			</ul>
			<div id="alert-ok" class="alert alert-success hide"> The user has been created.</div>
			<div class="row">
				<form id="add-user" method="post" class="form-horizontal span6">
					<div class="control-group">
						<label class="control-label" for="name">Name</label>
						<div class="controls">
							<input type="text" name="name" class="input-block-level" id="name">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="email">Email</label>
						<div class="controls">
							<input type="text" name="email" class="input-block-level" id="email">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="nickname">Nickname</label>
						<div class="controls">
							<input type="text" name="nickname" class="input-block-level" id="nickname">
						</div>
					</div>
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
								<option value="<?php echo $role ?>"><?php echo $name ?></option>
								<?php
										endforeach;
									endif;
								?>
							</select>
						</div>
					</div>
					<div class="text-right">
						<a href="<?php $site->urlTo('/manage/users', true) ?>" class="btn">Cancel</a>
						<button class="btn btn-success">Create user</button>
					</div>
				</form>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>