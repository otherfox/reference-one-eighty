(function($){

    $(document).ready(function(){

        showCountries();
        showStates();
        $('select.country').change(function(){
            showStates();
        });

        $('.deleteTax').live('click', function(){
            var li = $(this).parent();
            var id = $('span', li).data('id');
            removeState(id);
            li.fadeOut('fast', function(){
                $(this).remove();
                removeState()
            });
            return false;
        });
        $('#addTax').click(function(e){
            e.preventDefault();
            var country = $('#country').val();
            if(country==''){
                alert('Select a country from the drop down list.');
                return false;
            }
            var state = $('#state').val();
            if(state==""){
                alert('Select a state from the drop down list.');
                return false;
            }
            var rate = parseFloat($('#taxRate').val());
            if(rate<0.0){
                alert('Tax rate must be greater 0 percent.');
                return false;
            }
            if(rate>100.0){
                alert('Tax rate must be less than 100 percent.');
                return false;
            }
            
            addTax(country, state, rate);
            return false;
        });

        $('#stripe-payments-admin-wrap form').submit(function(e){
            var data = $(this).serializeArray();
            return true;
        });

        renderAll();
    });

    function addTax(country, state, rate){

        // Remove copies
        removeState(country, state);

        // Add data
        if(taxData==undefined ||
            Object.prototype.toString.call( taxData ) === '[object Array]') {
            taxData = {};
        }
        if(taxData[country]==undefined) {
            taxData[country] = {};
        }
        var states = taxData[country];
        states[state] = rate;

        // Render all
        renderAll();
    }

    function removeState(country, state){
        var states = taxData[country];
        if(states==undefined){
            // Nothing to delete
            return;
        }
        var copy = {};
        for(var propertyName in states){
            if(propertyName!=state){
                copy[propertyName] = states[propertyName];
            }
        };
        taxData[country] = copy;
    }

    function renderAll(){
        $('ul.taxdata').html('');
        if(typeof taxData !== 'undefined'){
            $.each(taxData, function(country, states){
                renderCountry(country, states);
            });
        }
    }

    function renderCountry(country, states){
        $.each(states, function(state, rate){
            renderTax(country, state, rate)
        });
    }

    function renderTax(country, state, rate){
        var countryName = countries[country].country_name;
        var stateName = state=='*' ? '* Country Wide' : countries[country].states[state];
        var li = $('<li>')
                    .append('<input type="hidden" name="countries[]" value="' + country + '" />')
                    .append('<input type="hidden" name="states[]" value="' + state + '" />')
                    .append('<input type="hidden" name="rates[]" value="' + rate + '" />')
                    .append('<span class="rate">' + rate.toFixed(2) + '%</span><span class="state">' + stateName + '</span><span class="country">' + countryName + '</span>')
                    .append('&nbsp;<a class="diglabs-btn-red deleteTax">X</a>');
        $('ul.taxdata').append(li);
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
    function showStates() {
        if( typeof countries !== 'undefined' ) {
            var iso = $('select.country').val();
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