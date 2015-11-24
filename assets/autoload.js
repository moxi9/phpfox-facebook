/*
PF.event.on('urer_image_url', function(obj, user) {
	var r = new RegExp("\{'fb':'([0-9]+)'\}", 'gi');
	user['user_image'].replace(r, function (match) {
		match = $.parseJSON(match.replace(new RegExp('\'', 'g'), '"'));
		//	obj.user_image = '<img src="http://graph.facebook.com/' + match.fb + '/picture?type=large">';
		return '';
	});
	p(user);
});
*/

/**
 * Load the routine when PHPfox is ready
 */
$Ready(function() {

	if (typeof($Cache.friends) == 'object') {
		$.each($Cache.friends, function(e, obj) {
			var r = new RegExp("\{'fb':'([0-9]+)'\}", 'gi');
			obj.user_image.replace(r, function (match) {
				match = $.parseJSON(match.replace(new RegExp('\'', 'g'), '"'));
				obj.user_image = '<img src="http://graph.facebook.com/' + match.fb + '/picture?width=200&height=200">';

				return '';
			});
		});
	}

	/**
	 * Loop thru all profile images with a connection to Facebook
	 */
	var checkImages = function() {
		$('.image_object:not(.fb_built)[data-object="fb"], img[src*="{\'fb\':"]:not(.fb_built)').each(function () {
			var t = $(this),
				i = new Image();

			if (t.prop('tagName').toLowerCase() == 'img') {
				var r = new RegExp("\{'fb':'([0-9]+)'\}", 'gi');
				var reg = t.attr('src').replace(r, function (match) {
					match = $.parseJSON(match.replace(new RegExp('\'', 'g'), '"'));
					t.data('src', match.fb);
					return '';
				});
			}

			var src = 'http://graph.facebook.com/' + t.data('src') + '/picture?type=large';

			t.addClass('fb_built');
			i.onload = function () {
				t.attr('src', src);
			};
			i.src = src;
		});
	};

	checkImages();

	// Add the FB login button
	if (!$('.fb_login_go').length) {
		$('#js_block_border_user_login-block form').before('<span class="fb_login_go"><i class="fa fa-facebook-official"></i>Facebook</span>');
	}

	// Click event to send the user to log into Facebook
	$('.fb_login_go').click(function() {
		PF.url.send('/fb/login', true);
	});
});
