<?php
	/**
	 * Generic post template
	 */

	# Check the post id
	if ( isset($site->post_id) ) {

		# Ok, get the post data
		$post = $cms->getPost($site->post_id);
		$post_type = $cms->getPostType($post->post_type);

		# Add body classes and set page title
		$site->addBodyClass(array(
			$post->post_type,
			sprintf('%s-%s', $post->post_type, $post->id),
			$post->post_name
		));
		$site->setPageTitle( $site->getPageTitle( $post->post_title ) );

	} else {

		# Hey! No post id, that's an error!
		$site->redirectTo('/404');
	}
?>
<?php $site->getParts(array('header_html', 'header')) ?>

		<section>
			<h3><?php echo htmlentities($post->post_title) ?></h3>
			<article>
				<?php echo $post->post_content ?>
			</article>
		</section>

<?php $site->getParts(array('footer', 'footer_html')) ?>