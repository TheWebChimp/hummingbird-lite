<?php $site->getParts(array( 'shared/header-html', 'sticky-footer/header' )); ?>

		<section>
			<div class="inner">
				<h1><?php $site->getSiteTitle(true); ?></h1>
			</div>
		</section>

<?php $site->getParts(array( 'sticky-footer/footer', 'shared/footer-html' )); ?>