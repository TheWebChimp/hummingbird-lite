<?php $site->getParts(array('header_html', 'header')) ?>

		<section>
			<h1><?php $site->getSiteTitle(true) ?></h1>
			<a href="<?php $i18n->urlTo('/home', true) ?>"><?php $i18n->translate('home.hello') ?></a>
		</section>

<?php $site->getParts(array('footer', 'footer_html')) ?>