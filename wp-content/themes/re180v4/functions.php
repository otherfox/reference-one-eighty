<?php
/* enqueue js */

function re180_enqueue_styles() {
    wp_enqueue_script('main-js', get_stylesheet_directory_uri().'/js/main.js');
}

add_action('wp_enqueue_scripts', 're180_enqueue_styles');

//================Calculator =============================

//========================= Forms=========================

function get_d_forms($atts,$content = null) {
		extract( shortcode_atts( array(
		'type' => '',
		'id' => '',
		'name' => '',
		'action' => '',
		'method' => '',
		'target' => '',
		'class' => '',
		'style' => '',
		), $atts ) );
	if ($type=="cal"){
		$return = '<div id="calc" class="'.$class.'" style="'.$style.'">
		<table class="table_calc">
<tbody>
<tr>
<td colspan="3"><img class="alignleft size-full wp-image-4163" style="margin: -5px 0px -10px 5px;" alt="57x57logo" src="http://reference180.com/wp-content/uploads/2013/07/57x57logo.png" width="20" height="20" /></td>
</tr>
<tr>
<td class="calc_td_result" colspan="3"><input id="calc_result" class="calc_result" value="if (\''.$_GET['income'].'\'=\'\') {this.value=\'\'} else {this.value=\''.$_GET['income'].'\'}" type="text" name="income" onblur="if (this.value == \'\') {this.value = \'0\';}"
 onfocus="if (this.value == \'0\') {this.value = \'\';}" /></td>
</tr>
<tr>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',1);" type="button" value="1" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',2);" type="button" value="2" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',3);" type="button" value="3" /></td>
</tr>
<tr>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',4);" type="button" value="4" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',5);" type="button" value="5" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',6);" type="button" value="6" /></td>
</tr>
<tr>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',7);" type="button" value="7" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',8);" type="button" value="8" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',9);" type="button" value="9" /></td>
</tr>
<tr>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',0);" type="button" value="0" /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:add_calc(\'calc\',\'.\');" type="button" value="." /></td>
<td class="calc_td_btn"><input class="calc_btn" onclick="javascript:f_calc(\'calc\',\'ce\');" type="button" value="CE" /></td>
</tr>
<tr>
<td id="btcenter" class="calc_td_btn" colspan="3"><input class="calc_btn" onclick="javascript:f_calc(\'calc\',\'=\');" type="button" value="Show Me The Money" /></td>
</tr>
</tbody>
</table>
</div>
<script type="text/javascript">
     document.getElementById(\'calc\').onload=init_calc(\'calc\');
</script>';

		return $return;
	}
}

add_shortcode('form', 'get_d_forms');
//================ Calculator for sole proprietor/partner vs LLC/Scorp ==================
function get_calc() {

		//------------- sole pro / partnership ---------------------
		//net profit
		$np = $_GET['income'];
		$min_np = 113700;
		if ($np >= 1000000000) {
			$np = 0;
		}
		//return se taxable
		$se_taxable = round($np*.9235,2);

		//return se tax
			if ($np > $min_np){
				$min_se_tax = round($min_np * .9235,2);
				$dif_se_tax =round(($np-$min_np) * .9235,2);
				$se_tax = round(($min_se_tax * .153)+($dif_se_tax*.029),2);
			}else{
				$se_tax = round($se_taxable*.153,2);
			}

		//return net cash
		$ncash = round($np-$se_tax,2);

		//---------------- LLC/S-corp -----------------------------
		$per_sal = $_POST['percentValue'];
		if($per_sal > 0){
		}else{
			$per_sal = .6;
		}
		//return salary
		$sal = round($np*$per_sal,2);
		if ($np == 0) {
			$sal = 0;
		}
		//return fica
		if ($sal > $min_np){
			$min_sal = round($min_np*.9235,2);
			$dif_sal =round(($sal-$min_np)*.9235,2);
			$fica = round(($min_sal*.153)+($dif_sal*.029),2);
		}else{
			$fica = round(($sal*.9235)*.153,2);
		}

		// return net
		$net = round($sal-$fica,2);

		//return dividend
		$dividend = round($np-$sal,2);

		//return net cash llc/scorp
		$ncash_llc = round($net+$dividend,2);

		//return cashback
		$cashback = $ncash_llc-$ncash;
		if(!empty($se_tax)){$tax_sav = round(($se_tax-$fica)/$se_tax,4);}

		$np = number_format($np,2,'.',',');
		$se_taxable = number_format($se_taxable,2,'.',',');
		$se_tax = number_format($se_tax,2,'.',',');
		$ncash = number_format($ncash,2,'.',',');
		$sal = number_format($sal,2,'.',',');
		$fica = number_format($fica,2,'.',',');
		$net = number_format($net,2,'.',',');
		$dividend = number_format($dividend,2,'.',',');
		$ncash_llc = number_format($ncash_llc,2,'.',',');
		$cashback = number_format($cashback,2,'.',',');
		$tax_sav = $tax_sav*100;

			return '
			<div class="calculation">
				<div class="colcalc">
					<h2>Sole Proprietor</h2>
					<div class="ltcol">
						<p>Net Profit:&nbsp;</p>
						<p>SE Tax:&nbsp;</p>
					</div>
					<div class="rtcol">
						<p class="calc_result calc_td_result net_profit">$'.$np.'</p>
						<p class="calc_result calc_td_result se_tax">$'.$se_tax.'</p>
					</div>
				</div>
			&nbsp;<hr style="line-height: 5px; margin: -5px 0px;" />

				<div class="colcalc">&nbsp;
					<h2>Ltd. Liability Co.</h2>
					<div class="ltcol">
						<p>Salary:&nbsp;</p>
						<p>Dividend:&nbsp;</p>
						<p>FICA Tax:&nbsp;</p>
					</div>
					<div class="rtcol">
						<p class="calc_result calc_td_result salary">$'.$sal.'</p>
						<p class="calc_result calc_td_result dividend">$'.$dividend.'</p>
						<p class="calc_result calc_td_result fica_tax">$'.$fica.'</p>

					</div>
				</div>
			<div class="colcalc">
				<h3 style="line-height:130%;">Salary/Dividend Ratio</h3>
			</div>
				<form method="POST" action="" target="_self" name="salaryDividend">
		<ul class="buttonGroup" id="buttonControl" name="buttonControl">
			<li data-val=".4" name="percent[]" class="li-click outer-left 40">40/60</li>
			<li data-val=".5" name="percent[]" class="li-click inner-center 50">50/50</li>
			<li data-val=".6" name="percent[]" class="li-click inner-center 60 selected">60/40</li>
			<li data-val=".7" name="percent[]" class="li-click inner-center 70">70/30</li>
			<li data-val=".8" name="percent[]" class="li-click outer-right 80">80/20</li>
		</ul>
		<input type="hidden" name="percentValue" id="percentValue" value=".6" />
		 </form>
		 </div><span style="line-height: 5px;font-size:5px;">&nbsp;</span>';
}

add_shortcode('calc', 'get_calc');

function get_savings() {

		//------------- sole pro / partnership ---------------------
		//net profit
		$np = $_GET['income'];
		$min_np = 113700;
		if ($np >= 1000000000) {
			$np = 0;
		}
		//return se taxable
		$se_taxable = round($np*.9235,2);

		//return se tax
			if ($np > $min_np){
				$min_se_tax = round($min_np * .9235,2);
				$dif_se_tax =round(($np-$min_np) * .9235,2);
				$se_tax = round(($min_se_tax * .153)+($dif_se_tax*.029),2);
			}else{
				$se_tax = round($se_taxable*.153,2);
			}

		//return net cash
		$ncash = round($np-$se_tax,2);

		//---------------- LLC/S-corp -----------------------------
		$per_sal = $_POST['percentValue'];
		if($per_sal > 0){
		}else{
			$per_sal = .6;
		}
		//return salary
		$sal = round($np*$per_sal,2);

		if ($np == 0) {
			$sal = 0;
		}
		//return fica
		if ($sal > $min_np){
			$min_sal = round($min_np*.9235,2);
			$dif_sal =round(($sal-$min_np)*.9235,2);
			$fica = round(($min_sal*.153)+($dif_sal*.029),2);
		}else{
			$fica = round(($sal*.9235)*.153,2);
		}

		// return net
		$net = round($sal-$fica,2);

		//return dividend
		$dividend = round($np-$sal,2);

		//return net cash llc/scorp
		$ncash_llc = round($net+$dividend,2);

		//return cashback
		$cashback = $ncash_llc-$ncash;
		if(!empty($se_tax)){$tax_sav = round(($se_tax-$fica)/$se_tax,4);}

		$cashback = number_format($cashback,2,'.',',');
		$tax_sav = $tax_sav*100;

			return '
			<div class="calculation">
				<div class="colcalc">
					<div class="ltcol">
						<p style="color:#000">Tax Savings:&nbsp;</p>
					<!--	<p style="color:#000"><strong>Tax Savings:&nbsp;</strong></p>-->
					</div>
					<div class="rtcol">
						<p id="tax-savings" class="calc_result calc_td_result">$'.$cashback.'</p>
					<!--	<p class="calc_result calc_td_result">'.$tax_sav.'%</p> -->
					</div>
				</div>
			</div>';
}

add_shortcode('savings', 'get_savings');

?>
