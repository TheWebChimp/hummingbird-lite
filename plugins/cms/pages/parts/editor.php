<?php
	$id = isset($_GET['id']) ? $_GET['id'] : false;
	$type = isset($_GET['type']) ? $_GET['type'] : '';
	$post_type = $cms->getPostType($type);
	if ($id) {
		$post = $cms->getPost($id);
	} else {
		$post = false;
	}
?>
<form action="" method="post">
	<div class="row-fluid">
		<div class="span9">
			<div class="control-group">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" value="<?php echo $post ? $post->post_title : '' ?>" class="input-block-level">
			</div>
			<div class="control-group">
				<label for="title">Permalink</label>
				<div class="input-prepend">
					<span class="add-on"><?php $site->urlTo('/', true) ?><?php echo $post_type['prefix'] ? sprintf('%s/', $type) : '' ?></span>
					<input type="text" name="permalink" value="<?php echo $post ? $post->post_name : '' ?>" id="permalink">
				</div>
			</div>
			<div class="control-group">
				<label for="content">Content</label>
				<textarea name="content" id="content" class="wysiwyg input-block-level"><?php echo $post ? htmlentities($post->post_content) : '' ?></textarea>
			</div>
			<div class="control-group">
				<label for="excerpt">Excerpt</label>
				<textarea name="excerpt" id="excerpt" class="input-block-level"><?php echo $post ? $post->post_excerpt : '' ?></textarea>
			</div>
		</div>
		<div class="span3">
			<div>
				<p>
					<label for="status">Status</label>
					<select name="status" id="status" class="input-block-level">
						<option <?php echo $post && $post->post_status == 'draft' ? 'selected="selected"' : '' ?> value="draft">Draft</option>
						<option <?php echo $post && $post->post_status == 'publish' ? 'selected="selected"' : '' ?> value="publish">Published</option>
						<option <?php echo $post && $post->post_status == 'private' ? 'selected="selected"' : '' ?> value="private">Private</option>
					</select>
				</p>
				<p><button class="btn btn-success">Save post</button></p>
			</div>
		</div>
	</div>
</form>