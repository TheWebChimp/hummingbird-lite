<?php
	$cur_dir = sprintf( '%s/parts', dirname(__FILE__) );
	$type = isset($_GET['type']) ? $_GET['type'] : '';
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$limit = isset($_GET['per_page']) ? $_GET['per_page'] : 10;
	$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
	$post_type = $cms->getPostType($type);
	$status = array(
		'draft' => 'Draft',
		'publish' => 'Published',
		'private' => 'Private'
	);

	if ( isset($gatekeeper) ) {
		$gatekeeper->requireLogin('admin', '/admin/cms');
	}
?>
<?php $site->getParts(array('header'), $cur_dir) ?>

		<section>
			<div class="row">
				<div class="span3">
					<?php $site->getParts(array('sidebar'), $cur_dir) ?>
				</div>
				<div class="span9">
					<h4><?php echo $post_type['plural_name'] ?></h4>
					<?php
						$args = array(
							'post_type' => $type,
							'posts_per_page' => $limit,
							'paged' => $page,
							'order' => $order
						);
						$posts = $cms->getPosts($args);
						if ($posts):
					?>
					<p>
						<div class="input-append">
							<input type="text" id="per_page" value="<?php echo $limit ?>" class="input-mini">
							<span class="add-on"> per page</span>
						</div>
						<select name="order" id="order">
							<option <?php echo $order == 'desc' ? 'selected="selected"' : '' ?> value="desc">Descending</option>
							<option <?php echo $order == 'asc' ? 'selected="selected"' : '' ?> value="asc">Ascending</option>
						</select>
					</p>
					<table id="list" class="table table-bordered" data-type="<?php echo $type ?>" data-page="<?php echo $page ?>" data-limit="<?php echo $limit ?>" data-order="<?php echo $order ?>">
						<thead>
							<tr>
								<th>Title</th>
								<th>Date</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($posts as $post): ?>
							<tr>
								<td>
									<?php $id = sprintf('select-%d', $post->id) ?>
									<label class="checkbox" for="<?php echo $id ?>">
										<input type="checkbox" id="<?php echo $id ?>">
										<strong><?php echo $post->post_title ?></strong>
									</label>
									<a href="<?php $site->urlTo('/admin/cms/edit?type=' . $type . '&amp;id=' . $post->id, true) ?>">Edit</a>
									<span class="muted"> | </span>
									<a href="<?php $site->urlTo('/admin/cms/edit?type=' . $type . '&amp;id=' . $post->id, true) ?>" class="text-error">Delete</a>
									<span class="muted"> | </span>
									<a href="<?php $site->urlTo( $post_type['prefix'] ? sprintf('/%s/%s', $type, $post->post_name) : sprintf('/%s', $post->post_name) , true) ?>">View</a>
								</td>
								<td><?php echo date( 'Y/m/d', strtotime($post->post_date) ) ?></td>
								<td><?php echo $status[$post->post_status] ?></td>
							</tr>
						<?php endforeach ?>
						</tbody>
					</table>
					<?php
						$post_count = $cms->getPostCount($type);
						$pages = ceil($post_count / $limit);
					?>
					<div class="pagination">
						<ul class="pull-right">
							<?php for ($pag = 1; $pag <= $pages; $pag++): ?>
							<li><a href="<?php $site->urlTo( sprintf('/admin/cms/show?type=%s&amp;page=%d&amp;per_page=%d&amp;order=%s', $type, $pag, $limit, $order), true ) ?>"><?php echo $pag ?></a></li>
						<?php endfor ?>
						</ul>
					</div>
					<?php else: ?>
					<div class="alert">You haven't created any <?php echo strtolower($post_type['plural_name']) ?> yet, <a href="<?php $site->urlTo('/admin/cms/new?type=' . $type, true) ?>">click here</a> to create a new <?php echo strtolower($post_type['name']) ?>.</div>
					<?php endif ?>
				</div>
			</div>
		</section>

<?php $site->getParts(array('footer'), $cur_dir) ?>