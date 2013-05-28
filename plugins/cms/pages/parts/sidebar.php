	<ul class="nav nav-list">
		<li><a href="<?php $site->urlTo('/admin/cms/manage', true) ?>">Dashboard</a></li>
		<?php
			$post_types = $cms->getPostTypes();
			if ($post_types):
				foreach ($post_types as $slug => $opts):
		?>
		<li class="nav-header"><?php echo $opts['plural_name'] ?></li>
		<li><a href="<?php $site->urlTo('/admin/cms/show?type=' . $slug, true) ?>">View all</a></li>
		<li><a href="<?php $site->urlTo('/admin/cms/new?type=' . $slug, true) ?>">Add new</a></li>
		<?php
				endforeach;
			endif;
		?>
		<li class="nav-header">Settings</li>
		<li><a href="<?php $site->urlTo('/admin/cms/settings', true) ?>">General settings</a></li>
	</ul>