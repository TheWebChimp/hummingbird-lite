<?php $site->getParts(array('sticky-footer/header_html', 'sticky-footer/header')) ?>

		<section>
			<div class="inner">
				<h1><?php $site->getSiteTitle(true) ?></h1>

				<?php if ( isset($i18n) ): ?>
				<a href="<?php $i18n->urlTo('/home', true) ?>"><?php $i18n->translate('home.hello') ?></a>
				<?php endif; ?>
			</div>
		</section>

<?php $site->getParts(array('sticky-footer/footer', 'sticky-footer/footer_html')) ?>