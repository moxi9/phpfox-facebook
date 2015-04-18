
$Ready(function() {
	$('.image_object:not(.fb_built)[data-object="fb"]').each(function() {
		var t = $(this),
			i = new Image(),
			src = 'http://graph.facebook.com/' + t.data('src') + '/picture?type=large';

		t.addClass('fb_built');
		i.onload = function() {
			// p('image ready: ' + src);
			t.attr('src', src);
		};
		i.src = src;
	});

	if (!$('.fb_login_go').length) {
		$('#js_block_border_user_login-block form').before('<span class="fb_login_go"><i class="fa fa-facebook-official"></i>Facebook</span>');
	}

	$('.fb_login_go').click(function() {
		PF.url.send('/fb/login', true);
	});
});