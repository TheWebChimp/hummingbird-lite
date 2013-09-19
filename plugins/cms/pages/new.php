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
		if ( $post_id = $cms->insertPost($args) ) {
			$site->redirectTo('/admin/cms/edit?type='.$type.'&id='.$post_id);
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
					<h4>New <?php echo $post_type['name'] ?></h4>
					<?php $site->getParts(array('editor'), $cur_dir) ?>
				</div>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>