/**
 * Load the routine when PHPfox is ready
 */
$Ready(function() {

	/**
	 * Loop thru all profile images with a connection to Facebook
	 */
	$('.image_object:not(.fb_built)[data-object="fb"]').each(function() {
		var t = $(this),
			i = new Image(),
			src = 'http://graph.facebook.com/' + t.data('src') + '/picture?type=large';

		t.addClass('fb_built');
		i.onload = function() {
			t.attr('src', src);
		};
		i.src = src;
	});

	// Add the FB login button
	if (!$('.fb_login_go').length) {
		$('#js_block_border_user_login-block form').before('<span class="fb_login_go"><i class="fa fa-facebook-official"></i>Facebook</span>');
	}

	// Click event to send the user to log into Facebook
	$('.fb_login_go').click(function() {
		PF.url.send('/fb/login', true);
	});
});