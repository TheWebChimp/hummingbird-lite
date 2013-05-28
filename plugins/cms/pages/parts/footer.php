		<footer>
			<hr>
			<p class="muted text-center">Powered by Hummingbird CMS</p>
		</footer>
	</div>
	<?php
		$site->registerScript('tinymce', $site->urlTo('/plugins/cms/js/tiny_mce/tiny_mce.js') );
		$site->registerScript('cms', $site->urlTo('/plugins/cms/js/plugin.js') );
		$site->includeScript('jquery');
		$site->includeScript('tinymce');
		$site->includeScript('bootstrap');
		$site->includeScript('cms');
	?>
</body>
</html>