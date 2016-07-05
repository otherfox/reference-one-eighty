(function($){

	var data = {};
	var el;
	$(document).ready(function(){

		$('.stripe-button-el').click(function(e){
			e.preventDefault();

			el = $(this);
			el.attr('disabled', true);

			var parent = el.closest('div.stripe-form-wrap');
			$('input', parent).each(function(i, el){
				var key = $(el).attr('name');
				var val = $(el).val();
				data[key] = val;
			});

			StripeCheckout.open({
				key:         data.pubkey,
				address:     data.stripe_address.toLowerCase()==='true',
				amount:      parseInt(data.amount),
				name:        data.stripe_name,
				description: data.stripe_desc,
				panelLabel:  data.stripe_label,
				token:       processToken,
				image: 		 data.stripe_img
			});
		});

	});

	function processToken(res){
		data['token'] = res['id'];
		for(var propName in res.card){

			// Make any address data first class data
			if(propName=="address_city"){data['city']=res.card[propName];}
			if(propName=="address_country"){data['country']=res.card[propName];}
			if(propName=="address_line1"){data['address1']=res.card[propName];}
			if(propName=="address_state"){data['state']=res.card[propName];}
			if(propName=="address_zip"){data['zip']=res.card[propName];}
		}
		data['action']		= 'stripe_plugin_process_card';


		var url = stripe_blog_url + '/wp-admin/admin-ajax.php';
		$.post(url, data, function(response){
			// Try to parse the response (expecting JSON).
			try {
				response = JSON.parse(response);
			} catch (err) {
				if(window.console && window.console.log) {
					console.log(err, response);
				}
				// Invalid JSON.
				if(!$(response).length) {
					response = { error: 'Server returned empty response during charge attempt'};
				} else {
					response = {error: 'Server returned invalid response:<br /><br />' + response};
				}
			}

			log(response);
						    				    				    	  	
			if(response['success']){
				// Redirect if a url is provided
				var urlInput = $('input[name="url"]', el);
				url = '';
				if(urlInput.length!=0){
					url = urlInput.val();
				} else {
					url = response['url'];
				}
				if(url!=undefined && url!=''){
					// redirect to another page
					var tempForm = $("<form action='" + url + "' method='post'></form>");
					for(var propName in response){
						var val = response[propName];
						tempForm.append("<input type='text' name='" + propName + "' value='" + val + "' />");
					}
					tempForm.append("<input type='submit' />");
					$('body').append(tempForm);
					$(tempForm).submit();
				}
			} else {
				// Show the error.
				alert('Error - ' + response['error']);
			}

			el.removeAttr('disabled');
		});
	}

	function log(msg){
    	if(window.console && window.console.log) {
	    	console.log(msg);
    	}
	}

})(jQuery);