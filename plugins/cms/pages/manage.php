<?php
	$cur_dir = dirname(__FILE__);

	if ( isset($gatekeeper) ) {
		$gatekeeper->requireLogin('admin', '/admin/cms');
	}
?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>
			<?php if ( $cms->isInstalled() ): ?>

			<div class="row">
				<div class="span3">
					<?php $site->getParts(array('sidebar'), $cur_dir) ?>
				</div>
				<div class="span9">
					<div class="alert alert-success">The CMS plugin is up and running</div>
					<?php
						$dbh = $site->getDatabase();
						try {
							$sql = "SELECT COUNT(id) AS total FROM cms_posts";
							$stmt = $dbh->prepare($sql);
							$stmt->execute();
							$ret = $stmt->fetch();
							$posts = $ret->total;
						} catch (PDOException $e) {
							$posts = 0;
						}
						$categories = count( $cms->getPostTypes() );
					?>
					<p>There are <?php echo $posts ?> total posts in <?php echo $categories ?> post types.</p>
				</div>
			</div>

			<?php else: ?>

			<div class="alert">The CMS plugin is not installed properly</div>
			<form method="post" action="<?php $site->urlTo('/admin/cms/settings', true) ?>">
				<input type="hidden" name="token" value="<?php $site->hashToken('install_cms', true) ?>">
				<button class="btn btn-success">Install plugin now</button>
			</form>

			<?php endif ?>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>