<?php

//================ GOOGLE =====================

// Google conversion codes

function conversion_code($atts) {
		extract( shortcode_atts( array(
		'page' => ''
		), $atts ) );

		if($page == "ppcstd2"){
			echo '<!-- Google Code for $29 Sale Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 991252231;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "i6aNCJGryQgQh57V2AM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/991252231/?label=i6aNCJGryQgQh57V2AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';
		}
		elseif($page == "ppcstd1"){
			echo '<!-- Google Code for $39 Sale Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 991252231;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "bTN4CIGtyQgQh57V2AM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/991252231/?label=bTN4CIGtyQgQh57V2AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';
		}
		elseif($page == "ppcstd3"){
			echo '<!-- Google Code for $49 Sale Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 991252231;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "W7xlCPGzzggQh57V2AM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/991252231/?label=W7xlCPGzzggQh57V2AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';
		}
}

add_shortcode('conversion','conversion_code');


//================ Enqueue Scripts ================

// fix some badly enqueued scripts with no sense of HTTPS
add_action('wp_print_scripts', 'enqueueScriptsFix', 100);
add_action('wp_print_styles', 'enqueueStylesFix', 100);

/**
* force plugins to load scripts with SSL if page is SSL
*/
function enqueueScriptsFix() {
    if (!is_admin()) {
        if (!empty($_SERVER['HTTPS'])) {
            global $wp_scripts;
            foreach ((array) $wp_scripts->registered as $script) {
                if (stripos($script->src, 'http://', 0) !== FALSE)
                    $script->src = str_replace('http://', 'https://', $script->src);
            }
        }
    }
}

/**
* force plugins to load styles with SSL if page is SSL
*/
function enqueueStylesFix() {
    if (!is_admin()) {
        if (!empty($_SERVER['HTTPS'])) {
            global $wp_styles;
            foreach ((array) $wp_styles->registered as $script) {
                if (stripos($script->src, 'http://', 0) !== FALSE)
                    $script->src = str_replace('http://', 'https://', $script->src);
            }
        }
    }
}


function my_scripts_method() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('google-script','https://reference180.com/wp-content/themes/re180v3/js/google.js');
	wp_enqueue_script('calc','https://reference180.com/wp-content/themes/re180v3/js/calc.js', array('jquery'));
	wp_enqueue_script('jqmap','https://reference180.com/wp-content/themes/re180v3/js/jquery.vmap.js', array('jquery'));
	wp_enqueue_script('jqmapusa','https://reference180.com/wp-content/themes/re180v3/js/jquery.vmap.usa.js', array('jquery', 'jqmap'));
	wp_enqueue_script('script','https://reference180.com/wp-content/themes/re180v3/js/script.js', array('jquery', 'jqmap', 'jqmapusa'));
	wp_enqueue_script('white-flash', 'https://reference180.com/wp-content/themes/re180v3/js/white-flash.js');
	wp_enqueue_style( 'style', 'https://reference180.com/wp-content/themes/re180v3/style.css' );
	wp_enqueue_style('mobile', 'https://reference180.com/wp-content/themes/re180v3/css/mobile.css', array(), '', 'only screen and (max-width: 760px) and (min-width: 320px), only screen and (max-device-width: 760px) and (min-device-width: 320px)');
}

add_action('wp_enqueue_scripts', 'my_scripts_method');


//================ MENUS ======================

add_theme_support( 'nav-menus' );
register_nav_menu('nv-menu-top', 'NV Top Navigation');
register_nav_menu('nv-menu-right', 'NV Main Navigation');


//=============================================


if ( function_exists('register_sidebars') ) {

	register_sidebars( array(
	'name' => 'Blog Sidebar',
	'id'=> 'blog-sidebar',
	'before_widget' => '
	<div id="%1$s" class="widget %2$s">',
	'after_widget' => '</div>
	',
	'before_title' => '
	<h2 class="widgettitle">',
	'after_title' => '</h2>'
	),
	array(
	'id'=> 'search',
	'name' => 'Search',
	'before_widget' => '
	<div id="%1$s" class="widget %2$s">',
	'after_widget' => '</div>
	',
	'before_title' => '
	<h2 class="widgettitle">',
	'after_title' => '</h2>
	'
	)
	);
}

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

//==============================================================
//this is where we get the information for the selected states

function get_state($stateID) {

					//clear vars
					$state = '';
					$entity = '';
					$code = '';

					//get info from url
					if(isset($_GET["state"])){$state = strip_tags($_GET["state"]);}
					if(isset($_GET["entity"])){$entity = strip_tags($_GET["entity"]);}
					if(isset($_GET["code"])){$code = strip_tags($_GET["code"]);}


					// get ID from var
					if(empty($stateID)) {
						$id = strip_tags($_GET["id"]);
					}
					else {
						$id = $stateID;
					}

					//get information from database for lsm form page
					if($state!=''&&$entity!=''){
						global $wpdb;
						$states = $wpdb->get_results("SELECT * FROM launch WHERE state_name='".$state."';");
						if($states==null){
							$requestURI = explode('/', $_SERVER['REQUEST_URI']);
							header("Location: http://".$_SERVER['HTTP_HOST'].'/'.$requestURI[1]);
						}
						else {
							return $states;
						}
					}

					//get state from state code

					if($id!=''){
						global $wpdb;
						$states = $wpdb->get_results("SELECT * FROM launch WHERE state='".$id."';");
						if($states==null){
							$requestURI = explode('/', $_SERVER['REQUEST_URI']);
							header("Location: http://".$_SERVER['HTTP_HOST'].'/'.$requestURI[1]);
						}
						else {
							return $states;
						}
					}

					// get information from database for lsm product delivery

					if($code!=''){
						global $wpdb;
						$states = $wpdb->get_results("SELECT * FROM launch WHERE inc_lsm_code='".$code."';");
						if($states==null){
							$states = $wpdb->get_results("SELECT * FROM launch WHERE llc_lsm_code='".$code."';");
							if($states==null){
								header("Location: http://reference180.com/");
							}
							else {
								return $states;
							}
						}
						else {
							return $states;
						}
					}

}

function get_entity($entityID) {
					//clear vars
					$entity = '';
					$code = '';

					//get info from url
					if(isset($_GET["entity"])){$entity = strip_tags($_GET["entity"]);}
					if(isset($_GET["code"])){$code = strip_tags($_GET["code"]);}

					//get information for lsm form
					if($entity!=''){
						$entity = substr($entity, -3);
						return $entity;
					}

					//get information for lsm product delivery
					if($code!=''){
						global $wpdb;

						$states = $wpdb->get_results("SELECT * FROM launch WHERE inc_lsm_code='".$code."';");

						if($states==null){
							$entity = 'LLC';
							return $entity;
						}
						else {
							$entity = 'INC';
							return $entity;
						}

					}

					//get information for wufoo form
					if(!empty($entityID)) {
						return strtoupper($entityID);
					}

}

function display_state_info() {

						$states = get_state($stateID);
						$entity = get_entity($entityID);

						if(isset($states)){
							if($entity=='LLC'){
								foreach ($states as $state) :
									if($state->llc_media_code!=''){
										$return_video_label = '[tabtext]Video[/tabtext]';
										$return_video = '[tab]<p><iframe id="lsm-video" width="650" height="366" frameborder="0" src="'.$state->llc_media_code.'"></iframe></p>[/tab]';
									}
									if($state->llc_lsm_wufoo_url!=''){
										$return_iframe_label = '[tabtext]State Details[/tabtext]';
										$return_iframe = '[tab]<p><iframe class="state-report" src="'.$state->llc_lsm_wufoo_url.'" frameborder="0" id="'.$state->state_name.'-report" marginheight="0" marginwidth="0" name="'.$state->state_name.'-report" height="800" width="100%"></iframe></p>[/tab]';
									}
									if($state->llc_pkg1!=''){
										$return_pkg1_label = '[tabtext]Package 1[/tabtext]';
										$return_pkg1 = '[tab]<div class="form pkg1"><div><h3>Package 1</h3>'.$state->llc_pkg1.'</div></div>[/tab]';

										if($state->llc_pkg1_image_url!='') {
											$return_pkg1 = '[tab]<div class="form pkg1"><img class="form-image" src="'.$state->llc_pkg1_image_url.'" alt="'.$state->state_name.' Package 1" width="240" /><div><h3>Package 1</h3>'.$state->llc_pkg1.'</div></div>[/tab]';
										}
									}
									if($state->llc_forms!=''){
										$return_forms_label = '[tabtext]Package 2[/tabtext]';
										$return_forms = '[tab]<div class="form pkg2"><div><h3>Package 2</h3>'.$state->llc_forms.'</div></div>[/tab]';

										if($state->llc_forms_image_url!='') {
											$return_forms = '[tab]<div class="form pkg2"><img class="form-image" src="'.$state->llc_forms_image_url.'" alt="'.$state->state_name.' Package 2" width="240" /><div><h3>Package 2</h3>'.$state->llc_forms.'</div></div>[/tab]';
										}
									}

									if($state->llc_irs!=''){

										$return_irs_label = '[tabtext]Package 3[/tabtext]';
										$return_irs = '[tab]<div class="form pkg3"><div><h3>Package 3</h3>'.$state->llc_irs.'</div></div>[/tab]';

										if($state->llc_irs_image_url!='') {
											$return_irs = '[tab]<div class="form pkg3"><img class="form-image" src="'.$state->llc_irs_image_url.'" alt="'.$state->state_name.' Package 3" width="240" /><div><h3>Package 3</h3>'.$state->llc_irs.'</div></div>[/tab]';
										}
									}

									if($return_video!=''){
										$return_string = '[tabs slidertype="top tabs"] [tabcontainer]'.$return_video_label.$return_iframe_label.$return_pkg1_label.$return_forms_label.$return_irs_label.'[/tabcontainer] [tabcontent]'.$return_video.$return_iframe.$return_pkg1.$return_forms.$return_irs.'[/tabcontent] [/tabs]';
									}
									else{
										$return_string = '[box]<h3 style="text-align:center;">Details coming soon ...</h3>[/box]';
									}
								endforeach;

								return $return_string;
							}
							if($entity=='INC'){
									foreach ($states as $state) :
									if($state->inc_media_code!=''){
										$return_video_label = '[tabtext]Video[/tabtext]';
										$return_video = '[tab]<p><iframe id="lsm-video" width="650" height="366" frameborder="0" src="'.$state->inc_media_code.'"></iframe></p>[/tab]';
									}

									if($state->inc_lsm_wufoo_url!=''){
										$return_iframe_label = '[tabtext]State Details[/tabtext]';
										$return_iframe = '[tab]<p><iframe class="state-report" src="'.$state->inc_lsm_wufoo_url.'" frameborder="0" id="'.$state->state_name.'-report" marginheight="0" marginwidth="0" name="'.$state->state_name.'-report" height="800" width="100%"></iframe></p>[/tab]';
									}

									if($state->inc_pkg1!=''){
										$return_pkg1_label = '[tabtext]Package 1[/tabtext]';
										$return_pkg1 = '[tab]<div class="form pkg1"><div><h3>Package 1</h3>'.$state->inc_pkg1.'</div></div>[/tab]';

										if($state->inc_pkg1_image_url!='') {
											$return_pkg1 = '[tab]<div class="form pkg1"><img class="form-image" src="'.$state->inc_pkg1_image_url.'" alt="'.$state->state_name.' Package 1" width="240" /><div><h3>Package 1</h3>'.$state->inc_pkg1.'</div></div>[/tab]';
										}
									}

									if($state->inc_forms!=''){
										$return_forms_label = '[tabtext]Package 2[/tabtext]';
										$return_forms = '[tab]<div class="form pkg2"><div><h3>Package 2</h3>'.$state->inc_forms.'</div></div>[/tab]';

										if($state->inc_forms_image_url!='') {
											$return_forms = '[tab]<div class="form pkg2"><img class="form-image" src="'.$state->inc_forms_image_url.'" alt="'.$state->state_name.' Package 2" width="240" /><div><h3>Package 2</h3>'.$state->inc_forms.'</div></div>[/tab]';
										}
									}

									if($state->inc_irs!=''){
										$return_irs_label = '[tabtext]Package 3[/tabtext]';
										$return_irs = '[tab]<div class="form pkg3"><div><h3>Package 3</h3>'.$state->inc_irs.'</div></div>[/tab]';

										if($state->inc_irs_image_url!='') {
											$return_irs = '[tab]<div class="form pkg3"><img class="form-image" src="'.$state->inc_irs_image_url.'" alt="'.$state->state_name.' Package 3" width="240" /><div><h3>Package 3</h3>'.$state->inc_irs.'</div></div>[/tab]';
										}
									}

									if($return_video!=''){
										$return_string = '[tabs slidertype="top tabs"] [tabcontainer]'.$return_video_label.$return_iframe_label.$return_pkg1_label.$return_forms_label.$return_irs_label.'[/tabcontainer] [tabcontent]'.$return_video.$return_iframe.$return_pkg1.$return_forms.$return_irs.'[/tabcontent] [/tabs]';
									}
									else{
										$return_string = '[box]<h3 style="text-align:center;">Details coming soon ...</h3>[/box]';
									}

								endforeach;
								return $return_string;
								}
						}
}

function display_state_report() {

						$states = get_state($stateID);
						$entity = get_entity($entityID);

						if(isset($states)){
							if($entity=='LLC'){
								foreach ($states as $state) :
									if($state->llc_lsm_wufoo_url!=''){
										$return_iframe = '<p><iframe class="state-report" src="'.$state->llc_lsm_wufoo_url.'" frameborder="0" id="'.$state->state_name.'-report" marginheight="0" marginwidth="0" name="'.$state->state_name.'-report" height="800" width="100%"></iframe></p>';
									}

									$return_string = $return_iframe;

								endforeach;

								return $return_string;
							}
							if($entity=='INC'){
									foreach ($states as $state) :

									if($state->inc_lsm_wufoo_url!=''){
										$return_iframe = '<p><iframe class="state-report" src="'.$state->inc_lsm_wufoo_url.'" frameborder="0" id="'.$state->state_name.'-report" marginheight="0" marginwidth="0" name="'.$state->state_name.'-report" height="800" width="100%"></iframe></p>';
									}

									$return_string = $return_iframe;

								endforeach;
								return $return_string;
							}
						}


}

function display_state_forms() {
						$states = get_state($stateID);
						$entity = get_entity($entityID);

						$return_pkg1 = '';
						$return_forms = '';
						$return_irs = '';

						if(isset($states)){
							if($entity=='LLC'){
								foreach ($states as $state) :
									if($state->llc_pkg1!=''){

										$return_pkg1 = '<h3>Package 1</h3>'.$state->llc_pkg1;

										if($state->llc_pkg1_image_url!='') {
											$return_pkg1 = '<div class="form pkg1"><img class="form-image" src="'.$state->llc_pkg1_image_url.'" alt="'.$state->state_name.' Package 1" width="140" /><div><h3>Package 1</h3>'.$state->llc_pkg1.'</div></div>';
										}
									}
									if($state->llc_forms!=''){

										$return_forms = '<h3>Package 2</h3>'.$state->llc_forms;

										if($state->llc_forms_image_url!='') {
											$return_forms = '<div class="form pkg2"><img class="form-image" src="'.$state->llc_forms_image_url.'" alt="'.$state->state_name.' Package 2" width="140" /><div><h3>Package 2</h3>'.$state->llc_forms.'</div></div>';
										}
									}

									if($state->llc_irs!=''){

										$return_irs = '<h3>Package 3</h3>'.$state->llc_irs;

										if($state->llc_irs_image_url!='') {
											$return_irs = '<div class="form pkg3"><img class="form-image" src="'.$state->llc_irs_image_url.'" alt="'.$state->state_name.' Package 3" width="140" /><div><h3>Package 3</h3>'.$state->llc_irs.'</div></div>';
										}
									}

									$return_string = '<div id="forms">'.$return_pkg1.$return_forms.$return_irs.'</div>';

								endforeach;

								return $return_string;
							}
							if($entity=='INC'){
									foreach ($states as $state) :

									if($state->inc_pkg1!=''){

										$return_pkg1 = '<h3>Package 1</h3>'.$state->inc_pkg1;

										if($state->inc_pkg1_image_url!='') {
											$return_pkg1 = '<div class="form pkg1"><img class="form-image" src="'.$state->inc_pkg1_image_url.'" alt="'.$state->state_name.' Package 1" width="140" /><div><h3>Package 1</h3>'.$state->inc_pkg1.'</div></div>';
										}
									}

									if($state->inc_forms!=''){

										$return_forms = '<h3>Forms</h3>'.$state->inc_forms;

										if($state->inc_forms_image_url!='') {
											$return_forms = '<div class="form pkg2"><img class="form-image" src="'.$state->inc_forms_image_url.'" alt="'.$state->state_name.' Package 2" width="140" /><div><h3>Package 2</h3>'.$state->inc_forms.'</div></div>';
										}
									}

									if($state->inc_irs!=''){

										$return_irs = '<h3>IRS</h3>'.$state->inc_irs;

										if($state->inc_irs_image_url!='') {
											$return_irs = '<div class="form pkg3"><img class="form-image" src="'.$state->inc_irs_image_url.'" alt="'.$state->state_name.' Package 3" width="140" /><div><h3>Package 3</h3>'.$state->inc_irs.'</div></div>';
										}
									}


									$return_string = '<div id="forms">'.$return_pkg1.$return_forms.$return_irs.'</div>';

								endforeach;
								return $return_string;
							}
						}

}

function display_state_video($atts) {
					extract( shortcode_atts( array(
					'width' => '675',
					'height' => '380',
					), $atts ) );

						$states = get_state($stateID);
						$entity = get_entity($entityID);

						$return_video = '';

						if(isset($states)){
							if($entity=='LLC'){
								foreach ($states as $state) :
									if($state->llc_media_code!=''){
										$return_video = '<p><iframe id="lsm-video" width="'.$width.'" height="'.$height.'" frameborder="0" src="'.$state->llc_media_code.'"></iframe></p>';
									}

									$return_string = $return_video;

								endforeach;
								return $return_string;
							}
							if($entity=='INC'){
									foreach ($states as $state) :
									if($state->inc_media_code!=''){
										$return_video = '<p><iframe id="lsm-video" width="'.$width.'" height="'.$height.'" frameborder="0" src="'.$state->inc_media_code.'"></iframe></p>';
									}

									$return_string = $return_video;

								endforeach;
								return $return_string;
							}
						}
}

function display_state_inf_form($atts, $stateID, $ent) {
					extract( shortcode_atts( array(
					'type' => 'llc'
					), $atts ) );

					$states = get_state($stateID);

					if(isset($states)){
						foreach ($states as $state) :
							if($type=='llc'||$ent=='llc'){
									if($state->inf_llc_form!=''){$return_form = $state->inf_llc_form;}
									$return_string = $return_form;

							}
							if($type=='inc'||$ent=='inc'){
									if($state->inf_inc_form!=''){$return_form = $state->inf_inc_form;}
									$return_string = $return_form;
							}
							return $return_string;
						endforeach;
					}


}
function display_state_name($stateID) {

					if(!empty($stateID)){
						$states = get_state($stateID);
					}
					else if(empty($stateID)){

						if(isset($_GET["state"])){$stateID = strip_tags($_GET["state"]);
						$states = get_state($stateID);}
					}

						if(isset($states)){
								foreach ($states as $state) :

								if($state->state_name!=''){
									$return_name = $state->state_name;
								}

								$return_string = $return_name;

								endforeach;
								return $return_string;
						}

}

function display_state_code($stateID) {

					$states = get_state($stateID);

						if(isset($states)){
								foreach ($states as $state) :

								if($state->state!=''){
									$return_code = $state->state;
								}

								$return_string = $return_code;

								endforeach;
								return $return_string;
						}
}

function get_state_code($state_name) {

	$states = array('AK' => 'Alaska', 'AL' => 'Alabama', 'AR' => 'Arkansas', 'AZ' => 'Arizona', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District of Columbia', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'IA' => 'Iowa', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'MA' => 'Massachusetts', 'MD' => 'Maryland', 'ME' => 'Maine', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'NE' => 'Nebraska', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NV' => 'Nevada', 'NY' => 'New York', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VA' => 'Virginia', 'VT' => 'Vermont', 'WA' => 'Washington', 'WI' => 'Wisconsin', 'WV' => 'West Virginia', 'WY' => 'Wyoming');

	$key = array_search($state_name, $states);

	return $key;

}

function display_state_entity() {

					$states = get_state($stateID);
					$entity = get_entity($entityID);

						if(isset($states)){
							if($entity=='LLC'){
								$return_string = 'LLC';
								return $return_string;
							}
							if($entity=='INC'){
								$return_string = 'INC';
								return $return_string;
							}
						}
}

function display_entity_name($entityID) {
					$entity = get_entity($entityID);

					if($entity == "LLC"){
						$return = "LLC";
					}
					if($entity == "INC"){
						$return = "Corporation";
					}

					return $return;
}

function display_state_pkg_img($atts) {
					extract( shortcode_atts( array(
					'entity' => 'llc',
					'state' => 'al'
					), $atts ) );

					$states = get_state($state);


						if(isset($states)){
							if($entity=='llc'){
								foreach ($states as $state) :

								if($state->llc_qsg_cover!=''){
									$return_form_img = '<img class="form-image" src="'.$state->llc_qsg_cover.'" alt="How to Form an LLC in '.$state->state_name.'" width="180" />';
								}

								$return_string = $return_form_img;

								endforeach;
								return $return_string;
							}
							if($entity=='inc'){
								foreach ($states as $state) :

								if($state->inc_qsg_cover!=''){
									$return_form_img = '<img class="form-image" src="'.$state->inc_qsg_cover.'" alt="How to Incorporate in '.$state->state_name.'" width="180" />';
								}

								$return_string = $return_form_img;

								endforeach;
								return $return_string;
							}
						}

}

function get_entity_code($long_entity) {
	if(isset($long_entity) && !empty($long_entity)){
		if($long_entity == "Limited Liability Company") { $return = "LLC";}
		if($long_entity == "Corporation") { $return = "INC";}
	} else {
		$return = "";
	}
	return $return;
}

function display_wufoo_code($stateID,$entityID) {

			$states = get_state($stateID);
			$entity = get_entity($entityID);


			if(isset($states)){
				foreach($states as $state) :
					if($entity == 'LLC'){
						$wufoo_code = $state->llc_lsm_code;
					}
					if($entity == 'INC'){
						$wufoo_code = $state->inc_lsm_code;
					}
				endforeach;
				return $wufoo_code;
			}
}




// ================================= Custom Forms =============================

function get_custom_form($atts) {
		extract( shortcode_atts( array(
		'page' => '',
		'bt_value' => '',
		), $atts ) );


		// New Vision Quick Start Guide

		if($page == 'nv-qsg'){
			$return = '
			<form id="nv-qsg" action="/free-quick-start-guides" method="post" target="_top">
<table><tr><td>

<select  class="wide StandardI" id="state"  >

<option value="choose">Choose a state</option>
<option value="alabama">Alabama</option>
<option value="alaska">Alaska</option>
<option value="arizona">Arizona</option>
<option value="arkansas">Arkansas</option>
<option value="california">California</option>
<!--<option value="colorado">Colorado</option>-->
<option value="connecticut">Connecticut</option>
<option value="delaware">Delaware</option>
<!--<option value="district-of-columbia">District of Columbia</option>-->
<option value="florida">Florida</option>
<option value="georgia">Georgia</option>
<option value="hawaii">Hawaii</option>
<option value="idaho">Idaho</option>
<option value="illinois">Illinois</option>
<option value="indiana">Indiana</option>
<option value="iowa">Iowa</option>
<option value="kansas">Kansas</option>
<option value="kentucky">Kentucky</option>
<option value="louisiana">Louisiana</option>
<!--<option value="maine">Maine</option>-->
<option value="maryland">Maryland</option>
<option value="massachusetts">Massachusetts</option>
<option value="michigan">Michigan</option>
<option value="minnesota">Minnesota</option>
<option value="mississippi">Mississippi</option>
<option value="missouri">Missouri</option>
<!--<option value="montana">Montana</option>-->
<!--<option value="nebraska">Nebraska</option>-->
<option value="nevada">Nevada</option>
<!--<option value="new-hampshire">New Hampshire</option>-->
<option value="new-jersey">New Jersey</option>
<!--<option value="new-mexico">New Mexico</option>-->
<option value="new-york">New York</option>
<option value="north-carolina">North Carolina</option>
<!--<option value="north-dakota">North Dakota</option>-->
<option value="ohio">Ohio</option>
<option value="oklahoma">Oklahoma</option>
<option value="oregon">Oregon</option>
<option value="pennsylvania">Pennsylvania</option>
<!--<option value="rhode-island">Rhode Island</option>-->
<option value="south-carolina">South Carolina</option>
<!--<option value="south-dakota">South Dakota</option>-->
<option value="tennessee">Tennessee</option>
<option value="texas">Texas</option>
<option value="utah">Utah</option>
<!--<option value="vermont">Vermont</option>-->
<option value="virginia">Virginia</option>
<option value="washington">Washington</option>
<!--<option value="west-virginia">West Virginia</option> -->
<option value="wisconsin">Wisconsin</option>
<option value="wyoming">Wyoming</option>

</select>
<div  class="fieldclear"></div>

</td></tr>
<tr>
<td>
		<select class="wide StandardI" id="entity"  >
			<option value="choose">Choose an entity type</option>
			<option value="incoporate">Corporation</option>
			<option value="llc" >Limited Liability Company</option>
		</select>
</td></tr>
<tr><td>
	<div  class="fieldclear">
	<input class="FormSubmitButton" type="submit" value="'.$bt_value.'" />
	</div>
</td></tr></table></form>';
			return $return;
		}

		if($page == 'nv-opt-in-header') {

				$return = do_shortcode('<p>Download Your Free LLC Operating Agreement&nbsp;&rarr;&nbsp;</p>[wufoo username="reference180" formhash="m9nmagi1illdfs" autoresize="false" height="34" header="hide" ssl="true"]');

			return $return;
		}


		if($page == 'nv-sale-header') {
			$return =  do_shortcode('<p>Download Your Free LLC Operating Agreement&nbsp;&rarr;&nbsp;</p>[wufoo username="reference180" formhash="m9nmagi1illdfs" autoresize="false" height="34" header="hide" ssl="true"]');

/* <p class="sale"><span class="offer"><span class="off">$27</span> LLC/INC Package</span>&nbsp;&rarr;&nbsp; 24hr Processing, EIN, Operating Agreement/Bylaws, Registered Agent, and More!</p><a href="/sale" class="button opt-in-button right">Order Now</a> */

			return $return;
		}


}

//======================== Quick start guide sign up/ WISHLIST API =======================


// Generate Random User Password

function generatePassword($length=9, $strength=1) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}

	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

// Function that creates the form to add user to wishlist

function wl_api($atts) {

	extract( shortcode_atts( array(
		'form' => 'silver',
		'type' => 'silver',
		'state' => '',
		'entity' => '',
		'test' => '',
		'formhash' => 'msuq26t0hqafdr'
		), $atts ) );

	// Check to see if state and entity are empty. This us allows us to easily switch between the two different methods of sign up.

	if(empty($state) || empty($entity)) {

		include('wlmapiclass.php');

		$api = new wlmapiclass('http://members.reference180.com/', 'fd64ca7be77961f22bdee1716e49305d');
		$api->return_format = 'php'; // <- value can also be xml or json

		// Membership Level IDs

		$bronze_id = '1361231821';
		$silver_id = '1360097993';
		$gold_id = '1360101900';
		$platinum_id = '1360101935';


		// User Info

		$user_login = $_POST['email'];
		$user_pass = generatePassword();
		$first_name = $_POST['fname'];
		$last_name = $_POST['lname'];
		$user_email = $_POST['email'];

		// Get Membership Level

		if($form=='silver'){
			$level_id = $silver_id;
		}
		else {
			$level_id = $silver_id;
		}

		// Add User on Submit

		if(!empty($_POST)){

			$data = array('user_login' => $user_login, 'user_email'=> $user_email, 'user_pass'=>$user_pass, 'first_name' => $first_name, 'last_name' => $last_name, 'Levels' => $level_id, 'custom_member_type' => $type,
			'custom_qsg_state' => $state, 'custom_qsg_entity' => $entity, 'company' => '', 'address1' => '', 'address2'  => '', 'city'  => '', 'state' => '', 'zip' => '');

			$response = $api->post('/members', $data);
			$response = unserialize($response);

			if($response['success']==1){


						// Get User ID

						$users = $api->get('/members');
						$users = unserialize($users);

						foreach ($users['members']['member'] as $member) {
							if($member['user_email']==$user_email){
								$id = $member['id'];
							}
						}

						// Get User Info [for development purposes]

						// $user_data = $api->get('/members/'.$id);
						// $user_data = unserialize($user_data);
						// $user_data = print_r($user_data);

						// Email Client Login Details

						$to      = $user_email;
						$subject = 'Welcome to members.reference180.com';
						$message =
							"Dear ".$first_name.","."\r\n".
							"\r\n".
							"You have successfully registered as a silver member with member's advantage -- our private membership platform."."\r\n".
							"\r\n".
							"Please keep this information safe as it contains your login URL, user name, and password."."\r\n".
							"\r\n".
							"Login URL: http://members.reference180.com/login"."\r\n".
							"User Name: ".$user_login."\r\n".
							"Password: ".$user_pass."\r\n".
							"\r\n".
							"Be sure to drop by the site as we are continuously adding and revising content on the platform."."\r\n".
							"\r\n".
							"We look forward to helping you launch, grow, and manage your new business!"."\r\n".
							"\r\n".
							"To your success,"."\r\n".
							"\r\n".
							"customer service team"."\r\n".
							"reference180.com"."\r\n".
							"1401 w idaho street, suite 200"."\r\n".
							"boise, id 83702-5246"."\r\n".
							"800.440.8193 ext. 101";

						$message = wordwrap($message, 70, "\r\n");

						$headers = 'From: reference180.com <customerservice@reference180.com>' . "\r\n" .
							'Reply-To: customerservice@reference180.com' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();

						mail($to, $subject, $message, $headers);

						// Display Success Message

						$content = '<div class="thank-you">Thank you for joining our membership site!</br>We will send you an email with your login details.</div>';
			}
			else {

				$content =
				'<div class="error">' . $response['ERROR'].'</div>'.
				'<form action="" method="POST" id="'.$type.'-form">
				<div class="form-row">
					<label>First Name:</label>
					<input type="text" name="fname" value="'.$first_name.'"/>
				</div>
				<div class="form-row">
					<label>Last Name:</label>
					<input type="text" name="lname" value="'.$last_name.'"/>
				</div>
				<div class="form-row">
					<label>Email:</label>
					<input type="text" name="email" value="'.$user_email.'"/>
				</div>
				<div class="form-submit">
					<input type="submit" value="Sign Me Up!"/>
				</div>
			</form>';

			}
		}
		else {
			$content = $user_data.'<form action="" method="POST" id="'.$type.'-form">
				<div class="form-row">
					<label>First Name:</label>
					<input type="text" name="fname" value="'.$first_name.'"/>
				</div>
				<div class="form-row">
					<label>Last Name:</label>
					<input type="text" name="lname" value="'.$last_name.'"/>
				</div>
				<div class="form-row">
					<label>Email:</label>
					<input type="text" name="email" value="'.$user_email.'"/>
				</div>
				<div class="form-submit">
					<input type="submit" value="Sign Me Up!"/>
				</div>
			</form>';
		}
	}
	elseif(!empty($state)&&!empty($entity)){

		if(empty($test)){
			$atts = '';
			$ent = $entity;
			$stateID = $state;

			$content = display_state_inf_form($atts, $stateID, $ent);
		}

		elseif($test=='true'){
			$content = do_shortcode('[wufoo username="reference180" formhash="'.$formhash.'" autoresize="true" height="520" header="hide" ssl="true" defaultv="field7='.display_state_name($state)."&field8=".display_entity_name($entity).'&field9='.display_wufoo_code($state,$entity).'"]');
		}


	}
	return $content;
}

// map for home page

function get_map() {
	return '<div id="vmap"></div><div id="mapStateInfo"><span class="exit">X</span><span id="mapStateInfoInner"></span></div>';
}

//add shortcodes

function register_shortcodes(){
		add_shortcode('state-img', 'display_state_pkg_img');
		add_shortcode('state-name', 'display_state_name');
		add_shortcode('state-code','display_state_code');
		add_shortcode('state-entity','display_state_entity');
		add_shortcode('state-inf-form','display_state_inf_form');
		add_shortcode('state-info', 'display_state_info');
		add_shortcode('state-report', 'display_state_report');
		add_shortcode('state-forms', 'display_state_forms');
		add_shortcode('state-video', 'display_state_video');
		add_shortcode('custom_form', 'get_custom_form');
		add_shortcode('wl-api', 'wl_api');
		add_shortcode('map', 'get_map');
	}

// Register Short Codes

add_action( 'init', 'register_shortcodes');

//==============================================================
//				Pricing Custom Post Type


add_action( 'init', 'create_post_type' );

function create_post_type() {
	register_post_type( 'pricing',
		array(
			'labels' => array(
				'name' => __( 'Prices / Fees' ),
				'singular_name' => __( 'Prices / Fees' )
			),
		'public' => true,
		'taxonomies' => array('category'),
		'has_archive' => true,
		'supports' => array( 'title', 'excerpt', 'thumnail', 'custom-fields' )
		)
	);
	register_post_type( 'coupons',
		array(
			'labels' => array(
				'name' => __( 'Coupons' ),
				'singular_name' => __( 'Coupons' )
			),
		'public' => true,
		'taxonomies' => array('category'),
		'has_archive' => true,
		'supports' => array( 'title', 'excerpt', 'thumnail', 'custom-fields' )
		)
	);
}


//==============================================================
//				Pricing table Step 1


	function pricing_step_1($atts) {

	extract( shortcode_atts( array(
		'db' => 'false',
		'pkg' => ''
		), $atts ) );

			$args = array(
				'post_type' => 'pricing',
				'orderby' => 'title',
				'order' => 'ASC',
				'posts_per_page' => '-1'
			);

			$the_query = new WP_Query( $args );
			$llc_states = array();
			$inc_states = array();

				//Show posts
				if ( $the_query->have_posts() ) {

					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						$state = get_post_meta( get_the_ID(), 'state', true );
						$type = get_post_meta( get_the_ID(), 'type', true );
						$price = get_post_meta( get_the_ID(), 'price', true );
						$live = get_post_meta( get_the_ID(), 'live_on_website', true );
						$optional = get_post_meta( get_the_ID(), 'optional_state_fee', true );

						// check if the custom field has a value
						if( !empty($state) && !empty($type) && !empty($price) && $state != "choose" && $optional != 1) {
							if($type == 'llc'){
								$llc_states[$state]=$llc_states[$state]+$price;
							}
							if($type == 'inc'){
								$inc_states[$state]=$inc_states[$state]+$price;
							}
							if($type == 'both'){
								$llc_states[$state]=$llc_states[$state]+$price;
								$inc_states[$state]=$inc_states[$state]+$price;
							}

						}

						if( !empty($live) && $live!="na" && !empty($price)) {
							if($live == 'std'){
								$std_price = $price;
							}
							if($live == 'dlx'){
								$dlx_price = $price;
							}
							if($live == 'prm'){
								$prm_price = $price;
							}
							if($live == 'stdbk'){
								$stdbk_price = $price;
							}
							if($live == 'dlxbk'){
								$dlxbk_price = $price;
							}
							if($live == 'prmbk'){
								$prmbk_price = $price;
							}
						}

					}

				$option = "<option value=\"choose\" selected>Select a state</option>";

				foreach( $llc_states as $code=>$price) {
					$option = $option.'<option value="'.$price.'_'.$inc_states[$code].'">'.display_state_name($code).'</option>';
				}

				if($pkg==''){
					$return = "<select id=\"state\">".$option."</select>  <select id=\"entity\" selected><option value=\"choose\">Select an entity</option><option value=\"llc\">Limited Liability Company (LLC)</option><option value=\"inc\">Corporation (S or C)</option></select> <select id=\"pkg\" class=\"none\"><option value=\"std\">".$std_price."</option><option value=\"dlx\">".$dlx_price."</option><option value=\"prm\">".$prm_price."</option></select>";
				} elseif($pkg=="std"){
					$return = $std_price;
				} elseif($pkg=="dlx"){
					$return = $dlx_price;
				} elseif($pkg=="prm"){
					$return = $prm_price;
				}elseif($pkg=="stdbk"){
					$return = $stdbk_price;
				} elseif($pkg=="dlxbk"){
					$return = $dlxbk_price;
				} elseif($pkg=="prmbk"){
					$return = $prmbk_price;
				}

			}
			else {
				$return = "no prices";
			}

			return $return;

			wp_reset_postdata();

	}


add_shortcode('pricing_step_1', 'pricing_step_1');


function get_price($atts) {

	extract( shortcode_atts( array(
		'pkg' => ''
		), $atts ) );

			$args = array(
				'post_type' => 'pricing',
				'orderby' => 'title',
				'order' => 'ASC',
				'posts_per_page' => '-1'
			);

			$the_query = new WP_Query( $args );

			// Find Price
			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$price = get_post_meta( get_the_ID(), 'price', true );
					$live = get_post_meta( get_the_ID(), 'live_on_website', true );
					$sale_on = get_post_meta( get_the_ID(), 'sale', true );
					$sale_price = get_post_meta( get_the_ID(), 'sale_price', true );

					if($pkg == $live){
						if($sale == 1) {
							$return = $sale_price;
						} else {
							$return = $price;
						}
					}
				}
			}

			return $return;

			wp_reset_postdata();

	}


add_shortcode('price', 'get_price');


//==============================================================
//				Order Forms


	function order_form ($atts, $content = null ) {
		extract( shortcode_atts( array(
		'id'=> ''
		), $atts ) );

		//get info from url
		$entity = strip_tags($_GET["entity"]);
		$state = strip_tags($_GET["state"]);

		if($id=="premium"){
			$wufoo_code = 'mteo05y0za6893';
		}
		if($id=="standard"){
			$wufoo_code = 'mjlqc2107xvu9h';
		}

		if($id=="deluxe"){
			$wufoo_code = 'mhqatns15fivvy';
		}

		if($id=="ppcstd2"){
			$wufoo_code = 'm65p2a70v6ylbw';
		}

		if($id=="ppcstd3"){
			$wufoo_code = 'm1j9nbdf0fyhmgm';
		}

		if($id=="ppcstd1"){
			$wufoo_code = 'm1xlkllr1lqol7x';
		}

		if($id=="on-the-fly"){
			$wufoo_code = 'm1n629n70l3qwz8';
		}

		if($id=="list-sale"){
			$wufoo_code = 'm14grs1809rbnk0';
		}

		if($id=="bookkeeping"){
			$wufoo_code = 'q1up4qpt0lb53o1';

		}


		if($entity=="llc"){
			$return = do_shortcode('[wufoo username="reference180" formhash="'.$wufoo_code.'" autoresize="true" height="1319" header="hide" ssl="true" defaultv="Field1=Limited Liability Company&Field2='.$state.'"]');
		}

		elseif($entity=="inc"){
			$return = do_shortcode('[wufoo username="reference180" formhash="'.$wufoo_code.'" autoresize="true" height="1319" header="hide" ssl="true" defaultv="Field1=Corporation&Field4='.$state.'"]');
		}

		elseif(empty($entity)) {
			$return = do_shortcode('[wufoo username="reference180" formhash="'.$wufoo_code.'" autoresize="true" height="1319" header="hide" ssl="true"]');
		}

		return $return;
	}

	add_shortcode('order_form', 'order_form');

	function secure_checkout () {

		// Old Method of getting Products

		if(isset($_GET["fid"]) && isset($_GET["eid"]) && isset($_GET["cid"])){

			$order_array = onboard_client();

			$fname = $order_array['fname'];
			$lname = $order_array['lname'];
			$email = $order_array['email'];
			if(isset($_GET['coupid'])){$coupid = urldecode(strip_tags($_GET['coupid']));}
			else{$coupid = "";}

			if(!empty($order_array['p1'])||!empty($order_array['p2'])||!empty($order_array['p3'])||!empty($order_array['p4'])||!empty($order_array['p5'])){
				$custom_products = array($order_array['p1'] => $order_array['p1amt'], $order_array['p2'] => $order_array['p2amt'], $order_array['p3'] => $order_array['p3amt'], $order_array['p4'] => $order_array['p4amt'], $order_array['p5'] => $order_array['p5amt']);
				$custom_products = array_filter($custom_products);
			}
			if(!empty($order_array['sub'])){
				$custom_sub = array('name' => $order_array['sub'], 'price'=> $order_array['subamt'], 'subid'=> $order_array['subid']);
				$custom_sub = array_filter($custom_sub);
			}

		} else {

			//get info from url
			if(isset($_GET["apt"])){$apt = urldecode(strip_tags($_GET["apt"]));}
			if(isset($_GET["pkg"])){$package = urldecode(strip_tags($_GET["pkg"]));}
			if(isset($_GET["e1"])){$state_llc = urldecode(strip_tags($_GET["e1"]));}
			if(isset($_GET["pub"])){$pub = urldecode(strip_tags($_GET["pub"]));}


			if(!empty($state_llc)) {
				$state_llc = $state_llc." LLC";
			}

			if(isset($_GET["e2"])){$state_inc = urldecode(strip_tags($_GET["e2"]));}

			if(!empty($state_inc)) {
				$state_inc = $state_inc." INC";
			}

			if(isset($_GET["ex"])){$expedite = urldecode(strip_tags($_GET["ex"]));}

			if(isset($_GET["p1"])){$registered_agent = urldecode(strip_tags($_GET["p1"]));}
			if(isset($_GET["p2"])){$crb = urldecode(strip_tags($_GET["p2"]));}
			if(isset($_GET["p3"])){$stdbooks = urldecode(strip_tags($_GET["p3"]));}
			if(isset($_GET["p4"])){$dlxbooks = urldecode(strip_tags($_GET["p4"]));}

			if(empty($stdbooks)&&!empty($dlxbooks)){
				$books = $dlxbooks;
			}
			elseif(!empty($stdbooks)&&!empty($dlxbooks)){
				$books = $dlxbooks;
			}
			elseif(!empty($stdbooks)&&empty($dlxbooks)){
				$books = $stdbooks;
			}
			elseif(empty($stdbooks)&&empty($dlxbooks)){
				$books = '';
			}

			$order_array = array($package, $state_llc, $state_inc, $expedite, $registered_agent, $crb, $books, $pub);
			if(isset($_GET['fname'])){$fname = urldecode(strip_tags($_GET['fname']));}
			else{$fname = "";}
			if(isset($_GET['lname'])){$lname = urldecode(strip_tags($_GET['lname']));}
			else{$lname = "";}
			if(isset($_GET['email'])){$email = urldecode(strip_tags($_GET['email']));}
			else{$email = "";}
			if(isset($_GET['coupid'])){$coupid = urldecode(strip_tags($_GET['coupid']));}
			else{$coupid = "";}
		}

		$order_array = array_filter($order_array);

		// Get Products from reference180.com wp-admin

		$args = array(
			'post_type' => 'pricing',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => '-1'
		);

		$the_query = new WP_Query( $args );
		$product_array = array();

		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$name = get_post_meta( get_the_ID(), 'order_form_label', true );
				$wufoo_name = get_post_meta( get_the_ID(), 'wufoo_field_id', true );
				$sub_id = get_post_meta( get_the_ID(), 'subscription_plan_id', true );
				$sale_on = get_post_meta( get_the_ID(), 'sale', true );

				$pay_type = get_post_meta( get_the_ID(), 'recurring', true );

				if($pay_type == true){
					$pay_type = ' / month';

				}
				if($pay_type == false){
					$pay_type = 'std';
				}

				$price = get_post_meta( get_the_ID(), 'price', true );
				$price = number_format($price, 2, '.', '');
				$sale_price = get_post_meta( get_the_ID(), 'sale_price', true );


				// get product array

				$product_array[$wufoo_name] = array('name'=>$name, 'price'=>$price, 'pay_type'=>$pay_type, 'sale_on' =>$sale_on , 'sale'=>$sale_price, 'sub_id'=>$sub_id);

			}

		}

		// Add Custom Products to Product Array

		if(isset($custom_products) && !empty($custom_products)){

			foreach( $custom_products as $name => $price){
				$product_array[$name] = array('name'=>$name, 'price'=>number_format($price, 2, '.', ''), 'pay_type'=>'std', 'sale_on' =>'' , 'sale'=>'', 'sub_id'=>'');
			}

		}

		// Add custom subscriptions to Product Array

		if(isset($custom_sub) && !empty($custom_sub)){

					$product_array[$custom_sub['name']] = array('name'=>$custom_sub['name'], 'price'=>$custom_sub['price'], 'pay_type'=>' / month', 'sale_on' =>'', 'sale'=>'', 'sub_id'=>$custom_sub['subid']);

		}

		// Get Product Data for User Selections

		$products_ordered = array();

		foreach( $order_array as $num => $product) {
			if(!empty($product)) {;
				$products_ordered[$product] = $product_array[$product];
			}
			$products_ordered = array_filter($products_ordered);
		}


		//

		if (is_array($product_ordered)) {



			foreach( $product_ordered as $product => $desc ){
				if(!empty($desc)) {
					$products_desc = $products_desc.' ,'.$desc['name'];
				}
			}
		}

		$products_desc = substr($products_desc, 2);

		// If Coupon ID Searxh DB for deal

		if(!empty($coupid)){

			$args = array(
			'post_type' => 'coupons',
			'orderby' => 'title',
			'order' => 'ASC',
			'posts_per_page' => '-1'
		);

		$the_query = new WP_Query( $args );
		$product_array = array();

			if ( $the_query->have_posts() ) {

				while ( $the_query->have_posts() ) {

					$the_query->the_post();
					$coupon_id = get_post_meta( get_the_ID(), 'coupon_id', true );

					if($coupon_id == $coupid) {
						$pids = get_post_meta( get_the_ID(), 'product', true );

						if(is_array($pids)){
							foreach($pids as $pid) {
								$pid = $pid;
							}
						}

						$pname = get_post_meta($pid, 'order_form_label', true);

						if(!empty($products_ordered[$pname])){
							$products_ordered[$pname]['sale_on'] = 1;
							$products_ordered[$pname]['sale'] = get_post_meta( get_the_ID(), 'sale_price', true );
						}

						//die();
					}

				}
			}
		}

		// Build Product Table
		if (is_array($products_ordered)) {
			foreach( $products_ordered as $product => $attr) {
				if(!empty($product)) {

					// If Sale Modify Product Total

					$attr['initial_price'] = '';

					if($attr['sale_on'] == 1) {
						$attr['initial_price'] = $attr['price'];
						$attr['price'] = $attr['sale'];
						$attr['sale_dif'] = $attr['initial_price'] - $attr['price'];
					}

					// Get Recurring totals

					if($attr['pay_type']!='std'){
						$rec_total = $rec_total + $attr['price'];
						$plan_id = $attr['sub_id'];
						$time = $attr['pay_type'];
					}
					else {
						unset($time, $plan_id);
					}


					// Get Today's Total

					$total = $total+$attr['price'];

					// Build Product Rows

					if(empty($attr['initial_price'])) {
						$product_rows = $product_rows.'<tr><td class="label">'.$attr['name'].'</td><td class="info">'.$attr['price'].$time.'</td></tr>';
					}
					elseif(!empty($attr['initial_price'])) {
						$product_rows = $product_rows.'<tr><td class="label">'.$attr['name'].'</td><td class="info">'.$attr['initial_price'].$time.'</td></tr>'
													.'<tr class="savings"><td class="label">$'.$attr['sale_dif'].' OFF '.$attr['name'].'</td><td class="info">-'.number_format($attr['sale_dif'], 2, '.', '').$time.'</td></tr>';
					}
				}
			}
		}

		$total_rows = '<tr  class="total"><td class="t-label">Today\'s Total</td><td class="t-info">'.number_format($total, 2, '.', '').'</td></tr>';

		$return = '<table class="stripe-payment-table"><thead><tr><th class="header">Item</th><th class="header">Price</th></tr></thead><tbody>'.$product_rows.$total_rows.'</tbody></table><input type="hidden" name="wfname" value="'.$fname.'"><input type="hidden" name="wlname" value="'.$lname.'"><input type="hidden" name="wemail" value="'.$email.'"><input type="hidden" name="coupid" value="'.$coupid.'">';


		// Build Payment Form

		if(!empty($rec_total)){
			$sub_total = $total - $rec_total;

			$payment_form = do_shortcode('[stripe_form_begin]
			<h3 class="rec">Order Summary</h3>'.$return.'
			<div class="hide">[stripe_form_plan_initial fee="'.$sub_total.'" description="'.$products_desc.'"]
			[stripe_form_plan_info plan="'.$plan_id.'"]</div>
			<div class="inline-form">[stripe_form_coupon]</div>
			<div class="inline-form">[stripe_form_billing_info short=true]</div>
			<div class="inline-form right-form">[stripe_form_payment_info]</div>
			[stripe_form_end]
			[stripe_form_receipt]
			<p><strong>Thank You, {fname} {lname}</strong></h4>
			<p><strong>Your payment of $'.$total.' has been submitted and your order will processed within the next 24 hours.</strong></p>
			<p>A receipt has been sent to <strong>{email}</strong>.</p>
			<p>Transaction ID: {id}</p>

			[/stripe_form_receipt]');

		}
		else {
			$payment_form = do_shortcode('[stripe_form_begin description="'.$products_desc.'"]
			<h3 class="std">Order Summary</h3>'.$return.'
			<div class="hide">[stripe_form_amount amount="'.$total.'"]</div>
			<div class="inline-form">[stripe_form_coupon]</div>
			<div class="inline-form">[stripe_form_billing_info short=true]</div>
			<div class="inline-form right-form">[stripe_form_payment_info]</div>
			[stripe_form_end]
			[stripe_form_receipt]
			<p><strong>Thank You, {fname} {lname}</strong></h4>
			<p><strong>Your payment of $ {amount} has been submitted and your order will processed within the next 24 hours.</strong></p>
			<p>A receipt has been sent to <strong>{email}</strong>.</p>
			<p>Transaction ID: {id}</p>

			[/stripe_form_receipt]');
		}

			return $payment_form;
	}

	add_shortcode('secure_checkout_form', 'secure_checkout');

	function secure_checkout_sidebar ($atts, $content = null ) {
		extract( shortcode_atts( array(
		'page'=> ''
		), $atts ) );

		if(empty($page)){

		if(isset($_GET["pkg"])){$package = urldecode(strip_tags($_GET["pkg"])); }
		if(isset($_GET["fid"])){
			$form = urldecode(strip_tags($_GET["fid"]));

			// custom
			if($form == 298){ $package = "custom";}

			// standard
			if( $form == 294){ $package = "standard";}

			// deluxe
			if($form == 293){ $package = "deluxe";}

			// premium
			if($form == 290){ $package = "premium";}

			// ppcstd1
			if($form == 297){ $pakage = 'ppcstd1';}

			// ppcstd2
			if($form == 295){ $package = 'ppcstd2';}

			// ppcstd3
			if($form == 296){ $package = 'ppcstd3';}

			// list-sale
			if($form == 299){ $package = 'list-sale';}
		}

		$service = urldecode(strip_tags($_GET["p3"]));
		}
		elseif(!empty($page)){
		$package = $page;
		}


		if($package == 'deluxe') {
			$return = '<p style="text-align: center;"><strong>Documents sent to state within 24 hours!</strong></p>

						<hr />

						<p><strong>Your Plan Includes:</strong></p>
						<ul>
							<li>Members Advantage</li>
							<li>Company name verification</li>
							<li>Document prep &amp; filing</li>
							<li>FREE shipping</li>
							<li>Registered Agent Service</li>
							<li>Publication notice</li>
							<li>IRS SS-4 filing (EIN)</li>
							<li>Incorporator / Organizer Statement</li>
							<li>Wave accounting software</li>
							<li>And more!</li>
						</ul>';
		}
		elseif($package == 'standard') {
			$return = '<p style="text-align: center;"><strong>Documents sent to state within 24 hours!</strong></p>

						<hr />

						<p><strong>Your Plan Includes:</strong></p>
						<ul>
							<li>24/7 document access</li>
							<li>Company name verification</li>
							<li>Document prep &amp; filing</li>
							<li>EIN IRS SS-4 filing</li>
							<li>Compliance alerts</li>
							<li>Registered agent</li>
							<li>FREE shipping</li>
							<li>And more!</li>
						</ul>';
		}
		elseif($package == 'premium') {
			$return = '<p style="text-align: center;"><strong>Documents sent to state within 24 hours!</strong></p>

						<hr />

			<p><strong>Your Plan Includes:</strong></p>
						<ul>
							<li>Members Advantage</li>
							<li>Company name verification</li>
							<li>Document prep &amp; filing</li>
							<li>FREE priority shipping</li>
							<li>Registered Agent Service</li>
							<li>Publication notice</li>
							<li>IRS SS-4 filing (EIN)</li>
							<li>IRS 2553/8832 filing</li>
							<li>Company Records Book</li>
							<li>Draft CRB documents</li>
							<li>Operating Agreement/Bylaws</li>
							<li>Periodic reports</li>
							<li>Quarterly filings</li>
							<li>Wave accounting software</li>
							<li>And more!</li>
						</ul>';
		}
		elseif($package=='premium-monthly'){
		$return = '<p><strong>Your Plan Includes:</strong></p>
						<ul>
							<li>Members Advantage</li>
							<li>Company name verification</li>
							<li>Document prep &amp; filing</li>
							<li>FREE shipping</li>
							<li>Registered Agent Service</li>
							<li>Publication notice</li>
							<li>IRS SS-4 filing (EIN)</li>
							<li>Incorporator / Organizer Statement</li>
							<li>IRS 2553 / 8832 filing</li>
							<li>Company records book</li>
							<li>Operating Agreement / Bylaws</li>
							<li>Periodic reports / State license</li>
							<li>Customized company records</li>
							<li>Wave accounting software</li>
							<li>And more!</li>
						</ul>';
		}
		elseif(empty($package)&&$service=="Premium Bookkeeping Payroll and Taxes"){
			$return = "<p><strong>Premium:</strong>
						<p><i>Bookkeeping</i></p>
						<ul>
							<li>Personal consultation</li>
							<li>Account set up</li>
							<li>Data capture</li>
							<li>Financial reporting</li>
							<li>Annual reconciliation</li>
						</ul>
						<p><i>Payroll</i></p>
						<ul>
							<li>Compliance & reporting</li>
							<li>Payroll processing</li>
							<li>Payroll fulfillment</li>
							<li>Tax filings & compliance</li>
							<li>Annual transmittals</li>
						</ul>
						<p><i>Taxes</i></p>
						<ul>
							<li>Personal consultation</li>
							<li>Tax minimization tips</li>
							<li>Prepare income tax return</li>
						</ul>";
		}
		elseif(empty($package)&&$service=="Deluxe Bookkeeping and Payroll"){
			$return = "<p><strong>Deluxe:</strong>
						<p><i>Bookkeeping</i></p>
						<ul>
							<li>Personal consultation</li>
							<li>Account set up</li>
							<li>Data capture</li>
							<li>Financial reporting</li>
							<li>Annual reconciliation</li>
						</ul>
						<p><i>Payroll</i></p>
						<ul>
							<li>Compliance & reporting</li>
							<li>Payroll processing</li>
							<li>Payroll fulfillment</li>
							<li>Tax filings & compliance</li>
							<li>Annual transmittals</li>
						</ul>";
		}
		elseif(empty($package)&&$service=="Bookkeeping Promotion"){
			$return = "<p><strong>Deluxe:</strong>
						<p><i>Bookkeeping</i></p>
						<ul>
							<li>Personal consultation</li>
							<li>Account set up</li>
							<li>Data capture</li>
							<li>Financial reporting</li>
							<li>Annual reconciliation</li>
						</ul>
						<p><i>Payroll</i></p>
						<ul>
							<li>Compliance & reporting</li>
							<li>Payroll processing</li>
							<li>Payroll fulfillment</li>
							<li>Tax filings & compliance</li>
							<li>Annual transmittals</li>
						</ul>";
		}
		elseif(empty($package)&&$service=="Standard Bookkeeping"){
			$return = "<p><strong>Standard:</strong>
						<p><i>Bookkeeping</i></p>
						<ul>
							<li>Personal consultation</li>
							<li>Account set up</li>
							<li>Data capture</li>
							<li>Financial reporting</li>
							<li>Annual reconciliation</li>
						</ul>";
		}
		elseif($package == 'ppcstd1' || $package == 'ppcstd2' || $package == 'ppcstd3' || $package == 'list-sale') {
			$return = '<p style="text-align: center;"><strong>Documents sent to state within 24 hours!</strong></p>

						<hr />

						<p><strong>Your Plan Includes:</strong></p>
						<ul>
							<li>24/7 document access</li>
							<li>Company name verification</li>
							<li>Document prep &amp; filing</li>
							<li>EIN IRS SS-4 filing</li>
							<li>Compliance alerts</li>
							<li>Registered agent</li>
							<li>FREE shipping</li>
							<li>And more!</li>
						</ul>';
		}
		else {
			$return = '<p style="text-align: center;"><strong>reference180.com</strong></p>

						<hr />

						<p>1401 W Idaho St—Suite 200<br>
						Boise, Idaho 83702</p>
						<p>800.440.8193<em> telephone</em><br>
						208.904.2988<em> facsimile</em></p>
						<p>Monday – Friday<br>
						8:00 am – 5:00 pm MST</p>';
		}

		return $return;

	}

	add_shortcode('secure_checkout_sidebar', 'secure_checkout_sidebar');


//==============================================================
//				Order Forms


function stripe_email_body( $data ) {

    // Set the title, message and footer
    //
    $title = 'Payment Received';
    $msg = 'Thank you for ordering from reference180.com. We will be in touch!';
	$footer ='<div style="text-align:center;">
    <p style="margin:0;padding:0;"><strong>Customer Service</strong></p>
	<p style="margin:0;padding:0;">reference180.com</p>
    <p style="margin:0;padding:0;">1401 West Idaho Street</p>
	<p style="margin:0;padding:0;">Suite 200</p>
    <p style="margin:0;padding:0;">Boise, Idaho 83702</p>
    <p style="margin:0;padding:0;">800.440.8193</p>
</div>';

    // Build the email content.
    //
    $table_style  = 'width: 80%; margin: 20px auto; border: 1px solid #666;';
    $header_style = 'padding: 25px 10px 5px; font-weight: bold; font-size: 16px; text-transform: uppercase; background-color: #f2f2f2;';
    $left_style   = 'text-align: right; white-space: nowrap; font-weight: bold;';
    $right_style  = 'width: 99%;';
    $body         = '<html>
					<body>
						<div style="padding:10px;background-color:#f2f2f2;">
							<div style="padding:10px;border:1px solid #eee;background-color:#fff;">
								<div style="margin:10px;">
								<h2>'.$title.'</h2>
									'.$msg.'
								</div>
								<table rules="all" style="'.$table_style.'" cellpadding="10">
									<!--table rows-->
								</table>
								'.$footer.'
							</div>
						</div>
					</body>
					</html>';

    // Dynamically create the rows of the table
    //
    $rows = "";
    if( !is_null( $data ) )
    {
        foreach( $data as $key => $val )
        {
            if( $val == "section" )
            {
                $rows .= "<tr><td style='$header_style' colspan=2>$key</td></tr>";
            }
            else
            {
                $rows .= "<tr><td style='$left_style'>" . $key . "</td><td style='$right_style''>" . $val . "</td></tr>";
            }
        }
    }
    $body = str_replace( "<!--table rows-->", $rows, $body );


	// This is where we will add the contact record to insighty, make their membership profile, etc.
	$success = stripe_get_wufoo_info($data);


    return $body;
}

function stripe_email_before_send(&$to, &$subject, &$body, &$headers) {

    // Prefix the subject with your company name.
    //
    $subject = $subject.' - reference180.com';

    // Alter the header here if you want
    //  to always add a From, ReplyTo, CC, BCC or other.
    //
    $headers .= "From: reference180.com <customerservice@reference180.com>\r\n";
    $headers .= "Reply-To: customerservice@reference180.com\r\n";
    $headers .= "Bcc: tenniesdesign@gmail.com,stennies@reference180.com\r\n";

    // return false to cancel sending the email
    return true;
}

//==============================================================
//				Pop up

	function hello() {
		$code = $_GET["contactId"];
		$calc = $_GET["income"];

		if(empty($calc)) {
			if(empty($code)){
				$return = '<a href="http://www.reference180.com/opt-in/" id="opt-in-trigger" class="fancybox-iframe" target="_blank" style="visibility:hidden;">click</a>';
			}
			else {
				$return = '<a href="http://www.reference180.com/opt-in/?submitted=true" id="fancybox-auto" class="fancybox-iframe" target="_blank">click</a>';
			}
		}
		else {
			$return = '';
		}

		return $return;
	}


add_shortcode('popup', 'hello');

//==============================================================
//		         Client Onboarding


//--------- Wufoo - get order details ---------

function get_wufoo_order($order_details) {

	if(!empty($order_details['form']) && !empty($order_details['entry']) && !empty($order_details['ip'])){
		$curl = curl_init('https://reference180.wufoo.com/api/v3/forms/'.$order_details['form'].'/entries.json?system=true&Filter1=EntryId+Is_equal_to+'.$order_details['entry']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, '6RHB-9MOD-7K42-EP9L:footastic');
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'reference180 request');

		$response = curl_exec($curl);
		$resultStatus = curl_getinfo($curl);

		if($resultStatus['http_code'] == 200) {
			$return = json_decode($response,TRUE);

			if($return['Entries'][0]['IP'] == $order_details['ip']){
				$return = $return['Entries'][0];
			} else {
				$return = "not valid ip";
			}

		} else {
			$return = 'Call Failed '.print_r($resultStatus);
		}

		curl_close($curl);

	} else {
		$return = "var not passed";
	}

	return $return;

}



//--------- Insightly - contact and organization creation ---------

function get_insightly() {

		$curl = curl_init("https://api.insight.ly/v2/Contacts");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERPWD, 'ccb812ed-5526-485f-9b06-84474a8dfc66:footastic');
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_USERAGENT, 'reference180 request');

		$response = curl_exec($curl);
		$resultStatus = curl_getinfo($curl);

		if($resultStatus['http_code'] == 200) {
			$return = json_decode($response,TRUE);
		} else {
			$return = 'Call Failed '.print_r($resultStatus);
		}

		curl_close($curl);

		return $return;
}

function put_insightly($client_order_details){

			// Get Wufoo Order Details

			$fname = $client_order_details["Field13"];
			$lname = $client_order_details["Field14"];
			$primary_contact = $client_order_details["Field13"].' '.$client_order_details["Field14"];

			//get entity and State Code
			if(isset($client_order_details["Field2"]) && !empty($client_order_details["Field2"]) && $client_order_details["Field2"] != '-Choose One-'){ $state_code = get_state_code($client_order_details["Field2"]); $entity = "LLC";}
			if(isset($client_order_details["Field4"]) && !empty($client_order_details["Field4"]) && $client_order_details["Field4"] != '-Choose One-'){ $state_code = get_state_code($client_order_details["Field4"]); $entity = "INC";}

			// get company name
			if($entity=="LLC"){$company_name = $client_order_details["Field140"].' '.$client_order_details["Field145"];}
			if($entity=="INC"){$company_name = $client_order_details["Field648"].' '.$client_order_details["Field483"];}

			//get service level
			$form_array = array('294' => 'standard', '293' => 'deluxe', '290' => 'premium', '297' => 'ppcstd1', '295' => 'ppcstd2', '296' => 'ppcstd3', '299' => 'list-sale');
			$service_level = $entity.' - '.$form_array[$client_order_details['FormID']];


			//get other managers
			if($entity =="LLC"){
				$fname2 = $client_order_details["Field167"];
				$lname2 = $client_order_details["Field168"];
				$fname3 = $client_order_details["Field155"];
				$lname3 = $client_order_details["Field156"];
				$fname4 = $client_order_details["Field3322"];
				$lname4 = $client_order_details["Field3323"];
				$crole = "Member";
			} elseif($entity =="INC"){
				$fname2 = $client_order_details["Field496"];
				$lname2 = $client_order_details["Field497"];
				$fname3 = $client_order_details["Field507"];
				$lname3 = $client_order_details["Field508"];
				$fname4 = $client_order_details["Field3324"];
				$lname4 = $client_order_details["Field3325"];
				$crole = "Owner";
			}

			//get address
			if($client_order_details["Field25"]==""){
				$street = $client_order_details["Field15"].', '.$client_order_details["Field16"];
				$city = $client_order_details["Field17"];
				$state = $client_order_details["Field18"];
				$zip = $client_order_details["Field19"];
				$m_street = $client_order_details["Field125"].', '.$client_order_details["Field126"];
				$m_city = $client_order_details["Field127"];
				$m_state = $client_order_details["Field128"];
				$m_zip = $client_order_details["Field129"];
			} else {
				$street = $client_order_details["Field15"].', '.$client_order_details["Field16"];
				$city = $client_order_details["Field17"];
				$state = $client_order_details["Field18"];
				$zip = $client_order_details["Field19"];
			}

			//get phone
			$phone = $client_order_details["Field22"];
			$email = $client_order_details["Field21"];

			$postConParams = '{
			 "FIRST_NAME": "'.$fname.'",
			  "LAST_NAME": "'.$lname.'",
			  "ADDRESSES": [
			  	{
				  "ADDRESS_TYPE": "WORK",
				  "STREET": "'.$street.'",'. 								//street
				  '"CITY": "'.$city.'",'. 									//city
				  '"STATE": "'.$state.'",'. 								//state
				  '"POSTCODE": "'.$zip.'",'. 								//postcode
				  '"COUNTRY": ""
				},
				{
				  "ADDRESS_TYPE": "MAIL",
				  "STREET": "'.$m_street.'",'. 								//street
				  '"CITY": "'.$m_city.'",'. 								//city
				  '"STATE": "'.$m_state.'",'. 								//state
				  '"POSTCODE": "'.$m_zip.'",'. 								//postcode
				  '"COUNTRY": ""
				}
			  ],
			  "CONTACTINFOS": [
				{
				  "TYPE": "PHONE",
				  "SUBTYPE": "",
				  "LABEL": "MOBILE",
				  "DETAIL": "'.$phone.'"'. 									//phone number ex 333.333.3333
				'},
				{
				  "TYPE": "EMAIL",
				  "SUBTYPE": "",
				  "LABEL": "WORK",
				  "DETAIL": "'.$email.'"'. 									//email address
				'}
			  ],
			  "TAGS": [
				{
				  "TAG_NAME": "'.$state_code.'"'. 						 //state code
				'}
			  ]
			}';

			$postCon2Params = '{
			  "FIRST_NAME": "'.$fname2.'",
			  "LAST_NAME": "'.$lname2.'"
			}';

			$postCon3Params = '{
			  "FIRST_NAME": "'.$fname3.'",
			  "LAST_NAME": "'.$lname3.'"
			}';

			$postCon4Params = '{
			  "FIRST_NAME": "'.$fname4.'",
			  "LAST_NAME": "'.$lname4.'"
			}';


			if(empty($client_order_details['Contact_ID'])) {
				$ref = curl_init('https://api.insight.ly/v2/Contacts');
				curl_setopt($ref, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ref, CURLOPT_POST, true);
				curl_setopt($ref, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ref, CURLOPT_POSTFIELDS, $postConParams);
				curl_setopt($ref, CURLOPT_USERPWD, 'ccb812ed-5526-485f-9b06-84474a8dfc66:footastic');
				curl_setopt($ref, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ref, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ref, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ref, CURLOPT_USERAGENT, 'reference180.com');

				$record = curl_exec($ref);
				$resultStatus = curl_getinfo($ref);

				if($resultStatus['http_code'] == 201) {
					$record = json_decode($record,TRUE);
					$cid = $record['CONTACT_ID'];

				} else {
					$record = 'Call Failed :'.$resultStatus;
				}

				curl_close($ref);

			} else {
				$cid = $client_order_details['Contact_ID'];
			}

			if(empty($client_order_details['Contact_ID_2']) && !empty($fname2) && !empty($lname2)) {
				$ref = curl_init('https://api.insight.ly/v2/Contacts');
				curl_setopt($ref, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ref, CURLOPT_POST, true);
				curl_setopt($ref, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ref, CURLOPT_POSTFIELDS, $postCon2Params);
				curl_setopt($ref, CURLOPT_USERPWD, 'ccb812ed-5526-485f-9b06-84474a8dfc66:footastic');
				curl_setopt($ref, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ref, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ref, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ref, CURLOPT_USERAGENT, 'reference180.com');

				$record = curl_exec($ref);
				$resultStatus = curl_getinfo($ref);

				if($resultStatus['http_code'] == 201) {
					$record = json_decode($record,TRUE);

					$cid2 = $record['CONTACT_ID'];
					$crole2 = $crole;
				} else {
					$return2 = 'Call Failed :'.$resultStatus;
				}

				curl_close($ref);

			} elseif(!empty($client_order_details['Contact_ID_2']) && !empty($fname2) && !empty($lname2)) {
				$cid2 = $client_order_details['Contact_ID_2'];
				$crole2 = $crole;
			}

			if(empty($client_order_details['Contact_ID_3']) && !empty($fname3) && !empty($lname3)) {
				$ref = curl_init('https://api.insight.ly/v2/Contacts');
				curl_setopt($ref, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ref, CURLOPT_POST, true);
				curl_setopt($ref, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ref, CURLOPT_POSTFIELDS, $postCon3Params);
				curl_setopt($ref, CURLOPT_USERPWD, 'ccb812ed-5526-485f-9b06-84474a8dfc66:footastic');
				curl_setopt($ref, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ref, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ref, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ref, CURLOPT_USERAGENT, 'reference180.com');

				$record = curl_exec($ref);
				$resultStatus = curl_getinfo($ref);

				if($resultStatus['http_code'] == 201) {
					$record = json_decode($record,TRUE);

					$cid3 = $record['CONTACT_ID'];
					$crole3 = $crole;
				} else {
					$return3 = 'Call Failed :'.$resultStatus;
				}

				curl_close($ref);

			} elseif(!empty($client_order_details['Contact_ID_3']) && !empty($fname3) && !empty($lname3)) {
				$cid3 = $client_order_details['Contact_ID_3'];
				$crole3 = $crole;
			}

			$postOrgParams = '{
			  "ORGANISATION_NAME": "'.$company_name.'",'. 					//Company Name
			  '"OWNER_USER_ID": 472672,'. 									//stennies id
			  '"VISIBLE_TO": "EVERYONE",
			  "ORGANISATION_FIELD_1": "'.$entity.'",'. 						//entity
			  '"ORGANISATION_FIELD_2": "'.$state_code.'",'. 				//state code
			  '"ORGANISATION_FIELD_6": "'.$primary_contact.'",'.					//primary contact
			  '"ORGANISATION_FIELD_7": "'.date("Y-m-d H:i:s").'",'. 		//order date
			  '"ADDRESSES": [
			  	{
				  "ADDRESS_TYPE": "WORK",
				  "STREET": "'.$street.'",'. 								//street
				  '"CITY": "'.$city.'",'. 									//city
				  '"STATE": "'.$state.'",'. 								//state
				  '"POSTCODE": "'.$zip.'",'. 								//postcode
				  '"COUNTRY": ""
				},
				{
				  "ADDRESS_TYPE": "MAIL",
				  "STREET": "'.$m_street.'",'. 								//street
				  '"CITY": "'.$m_city.'",'. 									//city
				  '"STATE": "'.$m_state.'",'. 								//state
				  '"POSTCODE": "'.$m_zip.'",'. 								//postcode
				  '"COUNTRY": ""
				}
			  ],
			  "CONTACTINFOS": [
				{
				  "TYPE": "PHONE",
				  "SUBTYPE": "",
				  "LABEL": "MOBILE",
				  "DETAIL": "'.$phone.'"'. 									//phone number ex 333.333.3333
				'},
				{
				  "TYPE": "EMAIL",
				  "SUBTYPE": "",
				  "LABEL": "WORK",
				  "DETAIL": "'.$email.'"'. 									//email address
				'}
			  ],
			  "TAGS": [
				{
				  "TAG_NAME": "'.$state_code.'"'. 						 //state code
				'},
				{
				  "TAG_NAME": "'.$service_level.'"'. 						//entity - service level ex LLC - Standard
				'}
			  ],
			  "LINKS": [
						{
						  "CONTACT_ID": '.intval($cid).',
						  "ROLE": "'.$crole.'"
						},
						{
						  "CONTACT_ID": '.intval($cid2).',
						  "ROLE": "'.$crole2.'"
						},
						{
						  "CONTACT_ID": '.intval($cid3).',
						  "ROLE": "'.$crole3.'"
						}
					  ]
			}';

				$ref = curl_init('https://api.insight.ly/v2/Organisations');
				curl_setopt($ref, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ref, CURLOPT_POST, true);
				curl_setopt($ref, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ref, CURLOPT_POSTFIELDS, $postOrgParams);
				curl_setopt($ref, CURLOPT_USERPWD, 'ccb812ed-5526-485f-9b06-84474a8dfc66:footastic');
				curl_setopt($ref, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ref, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ref, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ref, CURLOPT_USERAGENT, 'reference180.com');

				$record = curl_exec($ref);
				$resultStatus = curl_getinfo($ref);

				if($resultStatus['http_code'] == 201) {
					$return = json_decode($record,TRUE);

				} else {
					$return = $resultStatus;
				}

				curl_close($ref);

				return true;

}


//--------- Wishlist - login created and receipt uploaded ---------


function make_member($member_details) {

}

//--------- Onboarding - complete tasks and notify ---------

function onboard_client(){

	//get info from url
	if(isset($_GET["fid"])){$form = urldecode(strip_tags($_GET["fid"]));}
	if(isset($_GET["eid"])){$entry = urldecode(strip_tags($_GET["eid"]));}
	if(isset($_GET["cid"])){$ip = urldecode(strip_tags($_GET["cid"]));}

	//build order array
	$order_details = array('form' => $form, 'entry' => $entry, 'ip' => $ip);

	$client_details = get_wufoo_order($order_details);

	if(!empty($client_details['Field4'])){$client_details['Field4'] = $client_details['Field4']." INC";}
	if(!empty($client_details['Field2'])){$client_details['Field2'] = $client_details['Field2']." LLC";}

	$order_array = array();

	foreach($client_details as $title=>$value){
		array_push($order_array, $value);
	}

	//add fname lname and email to array
	$order_array['fname']=$client_details['Field13'];
	$order_array['lname']=$client_details['Field14'];
	$order_array['email']=$client_details['Field21'];


	// on-the-fly

	if(isset($form) && $form == 298){

		if(isset($client_details['Field3446']) && !empty($client_details['Field3446']) && isset($client_details['Field3447']) && !empty($client_details['Field3447'])) { $order_array['p1'] = $client_details['Field3446']; $order_array['p1amt'] = $client_details['Field3447']; }
		if(isset($client_details['Field3448']) && !empty($client_details['Field3448']) && isset($client_details['Field3451']) && !empty($client_details['Field3451'])) { $order_array['p2'] = $client_details['Field3448']; $order_array['p2amt'] = $client_details['Field3451']; }
		if(isset($client_details['Field3454']) && !empty($client_details['Field3454']) && isset($client_details['Field3450']) && !empty($client_details['Field3450'])) { $order_array['p3'] = $client_details['Field3454']; $order_array['p3amt'] = $client_details['Field3450']; }
		if(isset($client_details['Field3453']) && !empty($client_details['Field3453']) && isset($client_details['Field3449']) && !empty($client_details['Field3449'])) { $order_array['p4'] = $client_details['Field3453']; $order_array['p4amt'] = $client_details['Field3449']; }
		if(isset($client_details['Field3452']) && !empty($client_details['Field3452']) && isset($client_details['Field3455']) && !empty($client_details['Field3455'])) { $order_array['p5'] = $client_details['Field3452']; $order_array['p5amt'] = $client_details['Field3455']; }
		if(isset($client_details['Field3468']) && !empty($client_details['Field3468']) && isset($client_details['Field3460']) && !empty($client_details['Field3460']) && isset($client_details['Field3470']) && !empty($client_details['Field3470'])) { $order_array['sub'] = $client_details['Field3468']; $order_array['subid'] = $client_details['Field3460']; $order_array['subamt'] = $client_details['Field3470']; }

	}

	// standard
	if(isset($form) && $form == 294){ array_push($order_array, 'Standard Formation');}

	// deluxe
	if(isset($form) && $form == 293){ array_push($order_array, 'Deluxe Formation');}

	// premium
	if(isset($form) && $form == 290){ array_push($order_array, 'Premium Formation');}

	// ppcstd1
	if(isset($form) && $form == 297){ array_push($order_array, 'ppcstd1');}

	// ppcstd2
	if(isset($form) && $form == 295){ array_push($order_array, 'ppcstd2');}

	// ppcstd3
	if(isset($form) && $form == 296){ array_push($order_array, 'ppcstd3');}

	// Sale
	if(isset($form) && $form == 299){ array_push($order_array, 'list-sale');}


	return $order_array;
}

function stripe_get_wufoo_info($data){
	$fend = strrpos($data["Name"], ' ');
	$lstart = strrpos($data["Name"], ' ')+1;
	$fname = substr($data["Name"], 0, $fend);
	$lname = substr($data["Name"], $lstart);
	$email = $data["Email"];

	$order_details = array('fname'=>$fname,'lname'=>$lname,'email'=>$email);

	$form_array = array(294, 293, 290, 297, 295, 296, 299);
	$dates = array();
	$client_order_details = array();
	unset($cid,$cid2,$cid3,$cid4);

	if(!empty($order_details['fname']) && !empty($order_details['lname']) && !empty($order_details['email'])){

		foreach($form_array as $num => $form){
			$curl = curl_init('https://reference180.wufoo.com/api/v3/forms/'.$form.'/entries.json?system=true&Filter1=Field21+Is_equal_to+'.$order_details['email']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERPWD, '6RHB-9MOD-7K42-EP9L:footastic');
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($curl, CURLOPT_USERAGENT, 'reference180 request');

			$response = curl_exec($curl);
			$resultStatus = curl_getinfo($curl);

			if($resultStatus['http_code'] == 200) {
				$client_details[$form] = json_decode($response,TRUE);

			} else {
				$client_details = 'Call Failed '.print_r($resultStatus);
			}

			curl_close($curl);
		}

	} else {
		$client_details = "var not passed";
	}

	foreach($form_array as $num => $form){
		if(is_array($client_details[$form]['Entries'])){
			foreach($client_details[$form]['Entries'] as $num2 => $val){
				$dates[$form] = $val['DateUpdated'];
			}
		}
	}

	$mostRecent= 0;
	$now = time();

	if(is_array($dates)){
		foreach($dates as $form => $date){
				$curDate = strtotime($date);

				if ($curDate > $mostRecent && $curDate < $now) {
					$mostRecent = $curDate;
					$mostRecentForm = $form;
				}
		}
	}

	if(is_array($client_details[$mostRecentForm]['Entries'])){
		foreach($client_details[$mostRecentForm]['Entries'] as $num =>$val) {
				$client_order_details = $val;
		}
	}

	if(!empty($mostRecentForm)){$client_order_details['FormID']=$mostRecentForm;}


	$contact_records = get_insightly();


	//search contact records for owners

	if(is_array($contact_records)){
		foreach( $contact_records as $number => $value ) {
			foreach( $value['CONTACTINFOS'] as $num => $val) {
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if($val['DETAIL'] == $email){
						if(empty($cid)){$cid=$value['CONTACT_ID'];}
					}
			}
			if( $value['FIRST_NAME'] == $client_order_details['Field167'] && $value['LAST_NAME'] == $client_order_details['Field168']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid2)){$cid2=$value['CONTACT_ID'];}
			} elseif( $value['FIRST_NAME'] == $client_order_details['Field496'] && $value['LAST_NAME'] == $client_order_details['Field497']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid2)){$cid2=$value['CONTACT_ID'];}
			} elseif( $value['FIRST_NAME'] == $client_order_details['Field155'] && $value['LAST_NAME'] == $client_order_details['Field156']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid3)){$cid3=$value['CONTACT_ID'];}
			} elseif( $value['FIRST_NAME'] == $client_order_details['Field507'] && $value['LAST_NAME'] == $client_order_details['Field508']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid3)){$cid3=$value['CONTACT_ID'];}
			} elseif( $value['FIRST_NAME'] == $client_order_details['Field3322'] && $value['LAST_NAME'] == $client_order_details['Field3323']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid4)){$cid4=$value['CONTACT_ID'];}
			} elseif( $value['FIRST_NAME'] == $client_order_details['Field3324'] && $value['LAST_NAME'] == $client_order_details['Field3325']){
					// Need to check to see if if a preivous contact exists at this address and if so grab an ID to add to the organisation
					if(empty($cid4)){$cid4=$value['CONTACT_ID'];}
			}
		}
	}


	if(!empty($cid)){ $client_order_details['Contact_ID'] = $cid;}
	if(!empty($cid2)){ $client_order_details['Contact_ID_2'] = $cid2;}
	if(!empty($cid3)){ $client_order_details['Contact_ID_3'] = $cid3;}
	if(!empty($cid4)){ $client_order_details['Contact_ID_4'] = $cid4;}

	$success = put_insightly($client_order_details);

	//$success = "block";

	return $success;
}

//add_shortcode('onboard_client', 'send_test_data');

?>
