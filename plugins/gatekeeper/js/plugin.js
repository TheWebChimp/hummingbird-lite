jQuery(document).ready(function($) {

	// Edit user --------------------------------------------------------------
	$('.edit-user-page #edit-user #toggle a').on('click', function(e) {
		e.preventDefault();
		$('#edit-user #toggle').fadeOut(function() {
			$('#password-change').fadeIn();
		});
		return false;
	});

	$('.edit-user-page #edit-user').ajaxForm({
		dataType: 'json',
		beforeSubmit: function() {
			$('#edit-user .btn').prop('disabled', true);
			$('#edit-user .btn-success').text('Saving...');
		},
		success: function(response) {
			$('#edit-user .btn-success').text('Save changes');
			$('#edit-user .btn').prop('disabled', false);
			if (response.result) {
				$('#alert-ok').show();
			}
		}
	});

	// Add user ---------------------------------------------------------------
	$('.add-user-page #add-user').ajaxForm({
		dataType: 'json',
		beforeSubmit: function() {
			$('#add-user .btn').prop('disabled', true);
			$('#add-user .btn-success').text('Creating...');
		},
		success: function(response) {
			$('#add-user .btn-success').text('Create user');
			$('#add-user .btn').prop('disabled', false);
			if (response.result) {
				$('#alert-ok').show();
			}
		}
	});

});