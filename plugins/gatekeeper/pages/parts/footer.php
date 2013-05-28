		<footer>
			<hr>
			<p class="muted text-center">Powered by Hummingbird Gatekeeper</p>
		</footer>
	</div>
	<?php
		$site->registerScript('gatekeeper', $site->urlTo('/plugins/gatekeeper/js/plugin.js') );
		$site->includeScript('jquery');
		$site->includeScript('jquery.form');
		$site->includeScript('bootstrap');
		$site->includeScript('gatekeeper');
	?>
</body>
</html>