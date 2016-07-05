(function($){
	$(document).ready(function(){
	
		// Hide elements that start off hidden
		$(".stripe-payment-receipt").hide();
		$(".stripe-payment-form input.state").hide();


		// Auto-populate
        var countries = $('.stripe-payment-form select.country');
        if( countries.length > 0){
            showCountries();
            countries.change(function(){
                var iso = $('select.country').val();
                showStates(iso);
            });
            countries.val( defaultCountry );
            showStates(defaultCountry);
            $('.stripe-payment-form select.state').val( defaultState );
        }

		// Automatically add the autocomplete='off' attribute to all the input fields
		$(".stripe-payment-form input").attr("autocomplete", "off");
		
		// Sanitize and validate all input elements
		$(".stripe-payment-form input").blur(function(){
			var input = $(this);
			sanitize(input);
			validate(input);
		});
		$(".stripe-payment-form select").change(function(){
			var input = $(this);
			sanitize(input);
			validate(input);
		});
		
		$('.stripe-payment-form .amountShown').blur(function(){
			var form = $(this).closest('form');
			var val_in_cents = Math.round($(this).val()*100);
			$('.amount', form).val(val_in_cents);
		});

		// Initial validation of the amount
		$('.stripe-payment-form .amountShown').blur();
		
		// Bind to the submit for the form
	    $('.stripe-payment-form').submit(function(event) {
	    	
	    	var form = $(this);
		    var data = form.serializeArray();
					
			// Set the public key for use by Stripe.com
			var stripePublishable = $(".pubkey", form).val();
			Stripe.setPublishableKey(stripePublishable);
	    
	    	// Check for configuration errors
	    	if($('.stripe-payment-config-errors', form).length>0) {
	    		alert('Fix the configuration errors before continuing.');
	    		return false;
	    	}    	
	    
			// Lock the form so no change or double submission occurs
			lock_form(form);    
	    
	    	// Trigger validation
	    	if(!validateForm(form)) {
	    		// The form is not valid…exit early
	    		unlock_form(form);
	    		return false;
	    	}
	   	
	    	// Get the form values
	    	var params = {};
	    	if($('.cardName', form).length != 0) {
	    		params['name'] = $('.cardName', form).val();
	    	} else {
	    		params['name'] 		= $('.fname', form).val() + ' ' + $('.lname', form).val();
	    	}
	    	params['number'] 	= $('.cardNumber', form).val();
	    	params['cvc']		= $('.cardCvc', form).val();
	    	params['exp_month'] = $('.cardExpiryMonth', form).val();
	    	params['exp_year']	= $('.cardExpiryYear', form).val();
            add_optional('.address1', 'address_line1', form, params);
            add_optional('.address2', 'address_line2', form, params);
            add_optional('.city', 'address_city', form, params);
            add_optional('.state', 'address_state', form, params);
            add_optional('.country', 'address_country', form, params);
            add_optional('.zip', 'address_zip', form, params);

	        // Get the charge amount and convert to cents
	        var amount = $('.amount', form).val();
	        	
	        // Validate card information using Stripe.com.
	        //	Note: createToken returns immediately. The card
	        //	is not charged at this time (only validated).
	        //	The card holder info is HTTPS posted to Stripe.com
	        //	for validation. The response contains a 'token'
	        //	that we can use on our server.
	        progress('Validating card data…', form);
	        
	        
	        Stripe.createToken(params, function(status, response){
	                	        	
			    if (response.error) {
			    	// Show the error and unlock the form.
			    	progress(response.error.message, form);
			    	unlock_form(form);
			    	return false;
			    }
			    
			    // Collect additional info to post to our server.
			    //	Note: We are not posting any card holder info.
			    //	We only include the 'token' provided by Stripe.com.
			    var charge = {};
			    for(var i=0;i<data.length;i++){
			    	var name = data[i].name;
			    	var val = data[i].value;
			    	charge[name] = val;
			    }
			    charge['state']			= $('.state:visible', form).val();
			    charge['token']			= response['id'];
			    charge['action']		= 'stripe_plugin_process_card';
			    progress('Submitting charge…', form);
			    var url = stripe_blog_url + '/wp-admin/admin-ajax.php';
			    $.post(url, charge, function(response){
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
			    		// Card was successfully charged. Replace the form with a
			    		//	dynamically generated receipt.
			    		showReceipt(response, form);
	   		    		progress('success', form);
			    	} else {
			    		// Show the error.
			    		progress('Error - ' + response['error'], form);
			    	}
			    	// Unlock the form.
			    	unlock_form(form);
			    });
	        });
	        
	        // Do not submit the form.
	        return false;
	    });
	});

    // Optional parametes
    function add_optional( selector, param_name, form, params ) {
        var el = $(selector, form);
        if(el.length != 0){
            params[param_name] = el.val();
        }
    }

	function log(msg){
    	if(window.console && window.console.log) {
	    	console.log(msg);
    	}
	}
	
	// Show the receipt
	function showReceipt(response, form) {
		var urlInput = $('input.url', form);
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
		} else {
			// Show the inline form
			var formWrap = form.closest('.stripe-form-wrap');
			formWrap.hide();
			var rcpt = formWrap.nextAll(".stripe-payment-receipt").hide();
			var html = rcpt.html();
			for(var propName in response){
				var token = '{' + propName + '}';
				var val = response[propName];
				html = html.replace(new RegExp(token, 'g'), val);
			}
			rcpt.html(html).show();
            $('html, body').animate({
                scrollTop: rcpt.offset().top - 40
            }, 2000);
		}
	}
	
	// Lock and unlock the form. This prevents changes or 
	//	double submissions during payment processing.
	function lock_form(form) {
		$("input", form).not('.disabled').attr("disabled", "disabled");
		$("select", form).attr("disabled", "disabled");
		$("button", form).attr("disabled", "disabled");
	}
	function unlock_form(form) {
		$("input", form).not('.disabled').removeAttr("disabled");
		$("select", form).removeAttr("disabled");
		$("button", form).removeAttr("disabled");
	}
	
	// Helper function to display progress messages.
	function progress(msg, form){
		$('.stripe-payment-form-row-progress span.stripe-payment-form-message', form).html(msg);
	}
	
	// Validation helpers.
	function validateForm(form) {
		var isValid = true;
		$("input,select", form).each(function(){
			sanitize($(this));
			isValid = validate($(this)) && isValid;
		});

		// Check password confirmations
		if( $("#pword1").length != 0 ) {
			var pword1 = $("#pword1", form).val();
			var pword2 = $("#pword2", form).val();
			if(pword1 != pword2 ) {
				var row = $('#pword2').closest('.stripe-payment-form-row');
				$('.stripe-payment-form-error', row).html('Not a match');
				isValid = false;
			}
		}

		return isValid;
	}
	function sanitize(elem) {
		var value = $.trim(elem.val());
		if(elem.hasClass("number")){
			value = value.replace(/[^\d]+/g, '');
		}
		if(elem.hasClass("amountShown")){
	        value = value.replace(/[^\d\.]+/g, '');
	        if(value.length) value = parseFloat(value).toFixed(2);
		}
		elem.val(value);
	}
	function validate(elem) {
		if(!elem.is(':visible')){
			return true;
		}
		var row = elem.closest('.stripe-payment-form-row');
		var error = $('.stripe-payment-form-error', row);
		var value = $.trim(elem.val());
		if(elem.hasClass("required") && !value.length){
			error.html('Required.');
			return false;
		}
		if(elem.hasClass("amountShown") && value<0.50){
			error.html('Minimum charge is $0.50');
			return false;
		}
		if(elem.hasClass("email") && !validateEmail(value)) {
			error.html('Invalid email.');
			return false;
		}
		error.html('');
		return true;
	}
	function validateEmail(email) { 
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	    return re.test(email);
	}
	
    function showCountries() {
        if( typeof countries !== 'undefined' ) {
            var el = $('select.country');
            for(var propertyName in countries){
                var country = countries[propertyName];
                var iso = country.country_iso_2char;
                var name = country.country_name;
                el.append('<option value="' + iso + '">' + name + '</option>');
            }
        }
    }
    function showStates(iso) {
        if( typeof countries !== 'undefined' ) {
            var country = countries[iso];
            var el = $('select.state').html('');
            if(country.states.length==0){
                el.append('<option value="*">* Country Wide</option>');
            } else {
                for(var propertyName in country.states){
                    el.append('<option value="' + propertyName + '">' + country.states[propertyName] + '</option>');
                }
            }
        }
    }
})(jQuery);