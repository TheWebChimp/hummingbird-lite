<?php
	$cur_dir = sprintf( '%s/parts', dirname(__FILE__) );
	$type = isset($_GET['type']) ? $_GET['type'] : '';
	$post_type = $cms->getPostType($type);
	if (!$post_type) {
		$site->redirectTo('/admin/cms');
	}

	if ( isset($gatekeeper) ) {
		$gatekeeper->requireLogin('admin', '/admin/cms');
	}

	if ($_POST) {
		$id = isset($_GET['id']) ? $_GET['id'] : false;
		$content = isset( $_POST['content'] ) ? $_POST['content'] : '';
		$excerpt = isset( $_POST['excerpt'] ) ? $_POST['excerpt'] : '';
		$permalink = isset( $_POST['permalink'] ) ? $_POST['permalink'] : '';
		$title = isset( $_POST['title'] ) ? $_POST['title'] : '';
		$status = isset( $_POST['status'] ) ? $_POST['status'] : '';
		$args = array(
			'post_content' => $content,
			'post_title' => $title,
			'post_name' => $permalink,
			'post_excerpt' => $excerpt,
			'post_type' => $type,
			'post_status' => $status
		);
		if ( $cms->updatePost($id, $args) ) {
			$message = sprintf('The %s has been updated.', $post_type['name']);
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
					<h4>Edit</h4>
					<?php if ( isset($message) ): ?>
					<div class="alert alert-success"><?php echo $message ?></div>
					<?php endif ?>
					<?php $site->getParts(array('editor'), $cur_dir) ?>
				</div>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>