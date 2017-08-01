// calculator

function commaSeparateNumber(val){
	while (/(\d+)(\d{3})/.test(val.toString())){
	  val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
	}
	return val;
}


calc_array = new Array();
var calcul=0;
var pas_ch=0;

function $id(id)
{
        return document.getElementById(id);
}

function f_calc(id,n)
{

        if(n=='ce')
        {
                init_calc(id);
        }
        else if(n=='=')
        {
                if(calc_array[id][0]!='=' && calc_array[id][1]!=1)
                {
                        eval('calcul='+calc_array[id][2]+calc_array[id][0]+calc_array[id][3]+';');
                        calc_array[id][0] = '=';
                        $id(id+'_result').value=calcul;
                        calc_array[id][2]=calcul;
                        calc_array[id][3]=0;
                }
        }
        else if(n=='+-')
        {
                $id(id+'_result').value=$id(id+'_result').value*(-1);
                if(calc_array[id][0]=='=')
                {
                        calc_array[id][2] = $id(id+'_result').value;
                        calc_array[id][3] = 0;
                }
                else
                {
                        calc_array[id][3] = $id(id+'_result').value;
                }
                pas_ch = 1;
        }
        else if(n=='nbs')
        {
                if($id(id+'_result').value<10 && $id(id+'_result').value>-10)
                {
                        $id(id+'_result').value=0;
                }
                else
                {
                        $id(id+'_result').value=$id(id+'_result').value.slice(0,$id(id+'_result').value.length-1);
                }
                if(calc_array[id][0]=='=')
                {
                        calc_array[id][2] = $id(id+'_result').value;
                        calc_array[id][3] = 0;
                }
                else
                {
                        calc_array[id][3] = $id(id+'_result').value;
                }
        }
        else
        {
                        if(calc_array[id][0]!='=' && calc_array[id][1]!=1)
                        {
                                eval('calcul='+calc_array[id][2]+calc_array[id][0]+calc_array[id][3]+';');
                                $id(id+'_result').value=calcul;
                                calc_array[id][2]=calcul;
                                calc_array[id][3]=0;
                        }
                        calc_array[id][0] = n;
        }
        if(pas_ch==0)
        {
                calc_array[id][1] = 1;
        }
        else
        {
                pas_ch=0;
        }

        //------------- sole pro / partnership ---------------------
		//net profit
		var np = jQuery('#calc_result').val();
		np = np.replace(/\D/g,"");
        np = parseFloat(np);
		var min_np = 113700;
		if (np >= 1000000000) {
			np = 0;
		}

		//show net

		jQuery('p.net_profit').text('$'+commaSeparateNumber(np.toFixed(2)));

		//return se taxable
		var se_taxable = (np*.9235).toFixed(2);

		//return se tax
			if (np > min_np){
				var min_se_tax = (min_np * .9235).toFixed(2);
				var dif_se_tax = ((np-min_np) * .9235).toFixed(2);
				var se_tax = ((min_se_tax * .153)+(dif_se_tax*.029)).toFixed(2);
			} else {

				var se_tax = (se_taxable*.153).toFixed(2);
			}

		//show se taxt

		jQuery('p.se_tax').text('$'+commaSeparateNumber(se_tax));

		//return net cash
		var ncash = (np-se_tax).toFixed(2);

		//---------------- LLC/S-corp -----------------------------
		var per_sal = jQuery("#percentValue").val();
		if(per_sal > 0){
		}else{
			per_sal = .6;
		}
		//return salary
		var sal = (np*per_sal).toFixed(2);
		if (np == 0) {
			sal = (0).toFixed(2);
		}

		//show salery
		jQuery('p.salary').text('$'+commaSeparateNumber(sal));

		//return fica
		if (sal > min_np){
			var min_sal = (min_np*.9235).toFixed(2);
			var dif_sal =((sal-min_np)*.9235).toFixed(2);
			var fica = ((min_sal*.153)+(dif_sal*.029)).toFixed(2);
		}else{
			var fica = ((sal*.9235)*.153).toFixed(2);
		}

		//show fica
		jQuery('p.fica_tax').text('$'+commaSeparateNumber(fica));

		// return net
		var net = sal-fica;

		//return dividend
		var dividend = (np-sal).toFixed(2);

		//show dividend
		jQuery('p.dividend').text('$'+commaSeparateNumber(dividend));

		//return net cash llc/scorp
		var ncash_llc = parseFloat(net)+parseFloat(dividend);

		//return cashback
		var cashback = (ncash_llc-ncash).toFixed(2);
		var tax_sav = (((parseFloat(se_tax)-parseFloat(fica))*100)/100).toFixed(2);

		//show tax savings
		jQuery('#tax-savings').text('$'+commaSeparateNumber(tax_sav));


        document.getElementById(id+'_result').focus();
        return true;
}
function add_calc(id,n)
{
        if(calc_array[id][1]==1)
        {
                $id(id+'_result').value=n;
        }
        else
        {
                $id(id+'_result').value+=n;
        }
        if(calc_array[id][0]=='=')
        {
                calc_array[id][2] = $id(id+'_result').value;
                calc_array[id][3] = 0;
        }
        else
        {
                calc_array[id][3] = $id(id+'_result').value;
        }
        calc_array[id][1] = 0;
        document.getElementById(id+'_result').focus();
        return true;
}
function init_calc(id)
{
        $id(id+'_result').value=0;
        calc_array[id] = new Array('=',1,'0','0',0);
       // document.getElementById(id+'_result').focus();
        return true;
}
function key_detect_calc(id,evt)
{
        if((evt.keyCode>95) && (evt.keyCode<106))
        {
                var nbr = evt.keyCode-96;
                add_calc(id,nbr);
        }
        else if((evt.keyCode>47) && (evt.keyCode<58))
        {
                var nbr = evt.keyCode-48;
                add_calc(id,nbr);
        }
         else if(evt.keyCode==110)
        {
                add_calc(id,'.');
        }
        else if(evt.keyCode==190)
        {
                add_calc(id,'.');
        }
        else if(evt.keyCode==188)
        {
                add_calc(id,'.');
        }
        else if(evt.keyCode==13)
        {
                f_calc(id,'=');
        }
        else if(evt.keyCode==46)
        {
                f_calc(id,'ce');
        }
        else if(evt.keyCode==8)
        {
                f_calc(id,'nbs');
        }
        else if(evt.keyCode==27)
        {
                f_calc(id,'ce');
        }
        return true;
}

jQuery(document).ready(function(){
	jQuery('.li-click').click(function(){
		jQuery('.li-click').removeClass('selected');
		jQuery(this).addClass('selected');
		jQuery("#percentValue").val(jQuery(this).attr('data-val'));
		f_calc('calc', '=');
	});
});
