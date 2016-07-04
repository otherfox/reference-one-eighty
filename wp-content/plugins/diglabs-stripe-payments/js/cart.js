(function($){
	$(document).ready(function(){

        // Build the cart widget
        //
        var items = $('.diglabs-cart-widget').data('items');
        renderCartWidget(items);

		// Bind to the submit for the cart buttons
        //
	    $('form.diglabs-cart-item').submit(function(event) {

	    	var form = $(this);
            var url = form.attr('action');
		    var formData = form.serializeArray();

            form.find('img').show();
            var result = form.find('.diglabs-cart-item-status');
            result.html('');

            var data = {};
            for(var i=0;i<formData.length;i++){
                var name = formData[i].name;
                var val = formData[i].value;
                data[name] = val;
            }
            data['action'] = 'stripe_plugin_cart_add';

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
                        response = { error: 'Server returned empty response'};
                    } else {
                        response = {error: 'Server returned invalid response:<br /><br />' + response};
                    }
                }

                var itemCount = 0;
                for(var i=0;i<response.items.length;i++){
                    itemCount += response.items[i].count;
                }

                form.find('img').hide();

                if(response['success']){
                    renderCartWidget( response.items );
                    result.html('<em>Added to your cart.</em>');
                } else {
                    result.html('<em>There was an error adding this item.</em>');
                }
            });

	        // Do not submit the form.
	        return false;
	    });
	});

    function renderCartWidget(items){
        var zeroCost = $('.diglabs-cart-widget').data('zero');
        var ul = $('.diglabs-cart-widget ul');
        ul.html('');
        var finalTotal = 0;
        if(items!=null && items.length>0 ){
            for(var i=0;i<items.length;i++){
                var item = items[i];
                var unit = item.unit_cost.toFixed(2);
                var unitStr = zeroCost.replace('0.00', unit);
                var total = item.count * item.unit_cost;
                finalTotal += total;
                var totalStr = zeroCost.replace('0.00', total.toFixed(2) );
                var li = $('#diglabs_item_id_' + item.id);
                li = $('<li id="diglabs_item_id_' + item.id + '"></li>');
                li.append('<span class="diglabs-cart-widget-info">' + item.info + '</span>');
                var cost = $('<span class="diglabs-cart-widget-cost"></span>');
                cost.append('<span class="diglabs-cart-widget-qty">' + item.count + '</span>');
                cost.append(' X ');
                cost.append('<span class="diglabs-cart-widget-unit">' + unitStr + '</span>');
                cost.append(' = ');
                cost.append('<span class="diglabs-cart-widget-total">' + totalStr + '</span>');
                li.append(cost);
                ul.append(li);
            }
        } else {
            ul.html('<li>Your cart is empty.</li>');
        }
        var finalTotalStr = zeroCost.replace('0.00', finalTotal.toFixed(2) );
        $('.diglabs-cart-widget p span.diglabs-cart-widget-total').html(finalTotalStr);
    }

	function log(msg){
    	if(window.console && window.console.log) {
	    	console.log(msg);
    	}
	}

})(jQuery);