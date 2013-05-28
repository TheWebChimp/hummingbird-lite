<?php
	/**
	 * Generic archive template
	 */

	# Check the post type
	if ( isset($site->post_type) ) {

		# Get the post type data
		$type = $site->post_type;
		$post_type = $cms->getPostType($type);

		# Now set the body class and page title
		$site->addBodyClass( $site->toAscii( $post_type['plural_name'] ) );
		$site->setPageTitle( $site->getPageTitle( $post_type['plural_name'] ) );

	} else {

		# No post type, no service
		$site->redirectTo('/404');
	}
?>
<?php $site->getParts(array('header_html', 'header')) ?>

		<section>
			<h3><?php echo $post_type['plural_name'] ?></h3>
			<div class="posts">
			<?php
				$args = array(
					'post_type' => $type
				);
				$posts = $cms->getPosts($args);
				if ($posts):
			?>
				<?php foreach ($posts as $post): ?>
				<div class="post">
					<h4><a href="<?php $site->urlTo( $post_type['prefix'] ? sprintf('/%s/%s', $type, $post->post_name) : sprintf('/%s', $post->post_name) , true) ?>"><?php echo $post->post_title ?></a></h4>
					<p>Published <?php echo date('Y/m/d', strtotime($post->post_date) ) ?></p>
					<p><?php echo $post->post_excerpt ?></p>
				</div>
				<?php endforeach ?>
			<?php else: ?>
				<h4>There are no published <?php echo $post_type['plural_name'] ?>.</h4>
			<?php endif ?>
			</div>
		</section>

<?php $site->getParts(array('footer', 'footer_html')) ?>