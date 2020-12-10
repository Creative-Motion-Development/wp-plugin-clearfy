jQuery(function($) {

	function subscribeWidget() {
		var form = $('#wbcr-factory-subscribe-widget-form');
		form.submit(function(ev) {
			ev.preventDefault();
			var agree = form.find('[name=agree_terms]:checked');
			if( agree.length === 0 ) {
				return;
			}

			$.ajax({
				method: "POST",
				url: "https://clearfy.pro/wp-json/mailerlite/v1/subscribe/",
				data: {email: $('.wbcr-factory-subscribe-widget-field').val()},
				success: function(data) {
					if( !data.message ) {
						if( data.subscribed ) {
							$(".wbcr-factory-subscribe-widget-msg.success").show();
						} else {
							$(".wbcr-factory-subscribe-widget-msg.success2").show();
						}
					} else {
						alert('Something went wrong :(');
						console.error(data.message);
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		});
	}

	subscribeWidget();

});