
// Jquery Functions

jQuery(function ($) {
	$(document).ready(function() {
	
	// Mobile Menu
	
		$('#mobile-menu-icon').click( function(){
			if($('#mobile-menu-icon img').attr('src')=='http://reference180.com/wp-content/themes/re180v3/images/mobile/mobile-menu-icon.png'){
				$('#mobile-menu-icon img').attr('src', 'http://reference180.com/wp-content/themes/re180v3/images/mobile/mobile-menu-icon-active.png');
			}
			else {
				$('#mobile-menu-icon img').attr('src', 'http://reference180.com/wp-content/themes/re180v3/images/mobile/mobile-menu-icon.png');
			}
			$('#nav-wrapper').toggle(); 
		});
	
	
	// State by State Menu
	
	  $(".menu .sub-menu ul.sub-menu").each(function() {
		var list = $(this);
		var size = 13;
		var current_size = 0;
		list.children().each(function() {
		if(!console.log){ console.log(current_size + ': ' + $(this).text());}
		  if (++current_size > size) {
			var new_list = $('<ul class="sub-menu"></ul>').insertAfter(list);
			list = new_list;
			current_size = 1;
		  }
		  list.append(this);
		});
	  });
		
		
	// Dashboard Clickable Div button
		$('div.myBox').click(function(){
			window.location = $(this).find("a").attr("href"); 
			return false;
		});
	
	// Pricing page
	
		$(".pricing .trigger, .pricing-2 .trigger").click( function () {
			var theId = parseInt($(this).attr('id').replace(/[^\d]/g, ''), 10);
			if(isNaN(theId)){ 
				if($(".toggle").css('display') == 'block'){
					$(".toggle").hide();
				}
				else {
					$(".toggle").show();
				}
			}
			else {
				var info = '#info'+theId;
				$(info).toggle();
			}
		});
	
	// Appointment Page
	
		
	
		var h_fname = $('#fname').val();
		var h_lname = $('#lname').val();
		var h_email = $('#email').val();
		
		if(h_fname) {
			$('#birs_client_name_first').val(h_fname);
		}
		if(h_lname) {
			$('#birs_client_name_last').val(h_lname);
		}
		if(h_email) {
			$('#birs_client_email').val(h_email);
		}
		
	
	// New Vision Free QSG
	
		$("#nv-qsg select").change(function () {
		if (!($("select#entity").val()=="choose") && !($("select#state").val()=="choose")) {
			if($("select#entity").val()=="llc"){
				$("#nv-qsg").attr("action", "/how-to-form-an-llc-in-"+$("select#state").val());
			}
			else {
				$("#nv-qsg").attr("action", "/how-to-incorporate-in-"+$("select#state").val());
			}
		}
		else {
			$("#nv-qsg").attr("action", "/free-quick-start-guides?error=true");
		}
		});
	
		if(window.top.location.href.indexOf("error") >= 0) {
			$(".error").css("display", "block");
		}
	
	
		
	// State by State LLC
	
		$("#lsm-static-llc select").change(function () {
		if (!($("select#state").val()=="choose")) {
			$("#lsm-static-llc").attr("action", "/launch/form-"+$("select#state").val()+"-llc");		
		}
		else {
			$("#lsm-static-llc").attr("action", window.top.location.href+"?error=true");
		}
		});
		
		$("#lsm-static-inc select").change(function () {
		if (!($("select#state").val()=="choose")) {
			$("#lsm-static-inc").attr("action", "/launch/how-to-incorporate-in-"+$("select#state").val());		
		}
		else {
			$("#lsm-static-inc").attr("action", window.top.location.href+"?error=true");
		}
		});
	
	
	// Product Page
		
		$('.scrollbottom').click( function(){
			$("html, body").animate({ scrollTop: $(document).height() }, "slow");
		});
	
		
	// Thank you redirect to fix iframe Issue 
	
		if ($(".redirect-thank-you").is('*')) {
			window.top.location.href = "http://reference180.com/thank-you-for-your-order/"; 
		}	
	
	// Calculator Segmented Button Salary/Dividend Ratio
		
		$("li.li-click").click(function() {
			$('li.li-click').removeClass("selected");
			$(this).addClass("selected");
			$("#percentValue").val($(this).data("val"));
			f_calc('calc','=');     
		});
	
	
	// Pricing page step 1
	
		$("select#entity").val('choose');
		$("select#state").val('choose');
		var std_base = $("#pkg option[value='std']").text();
		var dlx_base = $("#pkg option[value='dlx']").text();
		var prm_base = $("#pkg option[value='prm']").text();
		
		
		
		$(".step select").change(function () {
			if (!($("select#entity").val()=="choose") && !($("select#state").val()=="choose")) {
			if($("select#entity").val()=="llc"){
				var price = $("select#state").val();
				var price = price.substr(0, price.indexOf('_'));
			} else{
				var price = $("select#state").val();
				var price = price.substr(price.indexOf('_')+1, price.length);
			}
			var std = parseInt(price)+parseInt(std_base);
			var dlx = parseInt(price)+parseInt(dlx_base);
			var prm = parseInt(price)+parseInt(prm_base);			
			$(".pricing-2 .premium").html("<span>$</span>"+prm);
			$(".pricing-2 .deluxe").html("<span>$</span>"+dlx);
			$(".pricing-2 .standard").html("<span>$</span>"+std);
			$(".pricing-2 .fee").html("<span style='color:#ddd;font-weight:bold;'>includes</span> state filing fee");
			$(".prm a").attr('href', 'http://reference180.com/premium-formation-service/?state='+$("select#state option:selected").text()+'&entity='+$("select#entity").val());
			$(".dlx a").attr('href', 'http://reference180.com/deluxe-formation-service/?state='+$("select#state option:selected").text()+'&entity='+$("select#entity").val());
			$(".std a").attr('href', 'http://reference180.com/standard-formation-service/?state='+$("select#state option:selected").text()+'&entity='+$("select#entity").val());
		}
		else {	
			$(".pricing-2 .premium").html("<span>$</span>"+prm_base);
			$(".pricing-2 .deluxe").html("<span>$</span>"+dlx_base);
			$(".pricing-2 .standard").html("<span>$</span>"+std_base);
			$(".pricing-2 .fee").html("+ state filing fee");
			$(".prm a").attr('href', 'http://reference180.com/premium-formation-service/');
			$(".dlx a").attr('href', 'http://reference180.com/deluxe-formation-service/');
			$(".std a").attr('href', 'http://reference180.com/standard-formation-service/');
		}
		});
	
	// Secure Checkout
	
		$('input[name=fname]').val($('input[name=wfname]').val());
		$('input[name=lname]').val($('input[name=wlname]').val());
		$('input[name=email]').val($('input[name=wemail]').val());
		$('input[name=coupon]').val($('input[name=coupid]').val());
	
	
	// Map on homepage
		
		var states = {
			ak: "alaska",
			al: "alabama",
			ar: "arkansas",
			az: "arizona",
			ca: "california",
			co: "colorado",
			ct: "connecticut",
			dc: "district-of-columbia",
			de: "delaware",
			fl: "florida",
			ga: "georgia",
			hi: "hawaii",
			ia: "iowa",
			id: "idaho",
			il: "illinois",
			in: "indiana",
			ks: "kansas",
			ky: "kentucky",
			la: "louisiana",
			ma: "massachusetts",
			md: "maryland",
			me: "maine",
			mi: "michigan",
			mn: "minnesota",
			mo: "missouri",
			ms: "mississippi",
			mt: "montana",
			ne: "nebraska",
			nc: "north-carolina",
			nd: "north-dakota",
			nh: "new-hampshire",
			nj: "new-jersey",
			nm: "new-mexico",
			ny: "new-york",
			nv: "nevada",
			oh: "ohio",
			ok: "oklahoma",
			or: "oregon",
			pa: "pennsylvania",
			ri: "rhode-island",
			sc: "south-carolina",
			sd: "south-dakota",
			tn: "tennessee",
			tx: "texas",
			ut: "utah",
			va: "virginia",
			vt: "vermont",
			wa: "washington",
			wi: "wisconsin",
			wv: "west-virginia",
			wy: "wyoming",
		};
		
		$('#vmap').vectorMap({
			map: 'usa_en',
			backgroundColor: null,
			borderColor: '#ffffff',
			borderWidth: 3,
			color: '#C1DAE0',
			hoverColor: '#999999',
			selectedColor: '#666666',
			enableZoom: false,
			showTooltip: false,
			selectedRegion: null,
			onRegionClick: function(event, code, region, e) {
		
				// edit source to inclue mouse event info
		
				var stateName = states[code],
					url = location.protocol+"//"+location.host+"/",
					llcURL = url+"how-to-form-an-llc-in-"+stateName,
					incURL = url+"how-to-incorporate-in-"+stateName,
					booksURL = url+"accounting-plans-pricing",
					e = e || window.event,
					left = e.pageX - $(this).offset().left +110,
					top = e.pageY - $(this).offset().top + 100,
					element = $('#mapStateInfo'),
					popup;
				
				
				element.hide();
				element.css({
					top: top,
					left: left
				});
		
				popup = "<p><a href=\""+llcURL+"\">"+code.toUpperCase()+" LLC Info</a></p>";
				popup += "<p><a href=\""+incURL+"\">"+code.toUpperCase()+" INC Info</a></p></span>";
		
				element.children("#mapStateInfoInner").html(popup);
				element.show();
		
			}
		});
		
		$('#mapStateInfo .exit').click(function(e) {
			$(this).parent().hide();
		});
	
	});	//End Document Ready
}); //End jquery

