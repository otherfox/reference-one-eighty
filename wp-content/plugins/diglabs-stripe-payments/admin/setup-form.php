<?php

    $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
    $supported_countries = $country_helper->supported_sites();
	$settings = new DigLabs_Stripe_Helpers_Settings();

	if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if( isset( $_POST[ 'register' ] ) )
        {
            if( isset( $_REQUEST[ 'reg' ] ) )
            {
                unset( $_REQUEST[ 'reg' ] );
            }
            $username = trim( $_POST[ 'username' ] );
            $password = trim( $_POST[ 'password' ] );
            $siteurl = site_url();

            $url = "http://diglabs.com/api/plugin/register.php";
            $params = "username=$username&password=$password&url=$siteurl";
            $user_agent = "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)";
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $result = curl_exec( $ch );
            curl_close( $ch );

            if( $result == "ok" )
            {
                $settings->setDownloadKey( $password );
                ?>
                <div class="updated"><p><strong><?php _e('Registration complete.'); ?></strong></p></div>
                <?php
            }
            else
            {
                ?>
                <div class="error"><p><strong><?php _e('Registration failed.'); ?></strong></p></div>
                <?php
            }
        }

        if( isset( $_POST[ 'save' ] ) )
        {
            // API Keys
            //
            $settings->setLivePublicKey( trim( $_POST['live_public_key'] ) );
            $settings->setLiveSecretKey( trim( $_POST['live_secret_key'] ) );
            $settings->setTestPublicKey( trim( $_POST['test_public_key'] ) );
            $settings->setTestSecretKey( trim( $_POST['test_secret_key'] ) );

            // Other options.
            //
            $is_live_keys = isset( $_POST[ 'is_live_keys' ] );
            $settings->setIsLive( $is_live_keys );
            $is_auto_plan = isset( $_POST[ 'is_auto_plan' ] );
            $settings->setIsAutoPlan( $is_auto_plan );

            // Webhook URL
            //
            $settings->setWebHookUrl( trim( $_POST['webhook_url'] ) );

            // Currency
            //
            $country_iso = trim( $_POST['stripe_country'] );
            $settings->setCountryIso( $country_iso );
            $country = $country_helper->country( $country_iso );
            $currency_iso = $country->currency_iso_3char;
            $settings->setCurrencySymbol( $currency_iso );

            // Tax rates.
            //
            $copy       = array();
            $rates      = isset( $_POST['rates'] ) ? $_POST['rates'] : null;
            $countries  = isset( $_POST['countries'] ) ? $_POST['countries'] : null;
            $states     = isset( $_POST['states'] ) ? $_POST['states'] : null;
            if( is_array( $rates ) && is_array( $countries ) && is_array( $states ) )
            {
                if( count( $rates )==count( $countries ) && count( $countries )==count( $states ) )
                {
                    for($i=0;$i<count( $rates );$i++)
                    {
                        $rate = floatval( $rates[$i] );
                        $country_code = trim( $countries[$i] );
                        $state_code = trim( $states[$i] );

                        if( !isset( $copy[ $country_code ] ) )
                        {
                            $copy[ $country_code ] = array();
                        }
                        $copy[ $country_code ][ $state_code ] = $rate;
                    }
                }
            }
            $settings->setTaxData($copy);

            ?>
            <div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
            <?php
        }
	}
    $base = DigLabs_Stripe_Payments::GlobalInstance();
    $countries = file_get_contents( $base->paths->plugin() . '/classes/i18n/countries.json');
	$taxData = $settings->getTaxData();
?>

<script type="text/javascript">
    var countries = <?php echo $countries; ?>;
	var taxData = <?php echo json_encode($taxData); ?>;
</script>

<div id="stripe-payments-admin-wrap" class="wrap1">
    <?php if( !$settings->downloadKey || isset( $_REQUEST[ 'reg' ] ) ) { ?>
        <div class="diglabs-info">
            <h2>Plugin Registration</h2>
            <h3>This is a one-time process that will enable you to access updates to your plugin.</h3>
            <p>
                When you purchased this plugin, you received an email providing you with a <strong>username</strong>
                and <strong>password</strong>. Something similar to this one:<br />
                <img src="http://diglabs.com/api/plugin/downloads/email_receipt.jpg" alt="email receipt" />
                <br />
                Enter the <strong>username</strong> and <strong>password</strong> below.
            </p>
            <form class="diglabs-form diglabs-inline" name="registration_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <p>
                    <label for="username">Username:</label>
                    <input type="text" name="username" />
                </p>
                <p>
                    <label for="password">Password:</label>
                    <input type="text" name="password" />
                </p>
                <p class="submit">
                    <input class="diglabs-btn-green" type="submit" name="register" value="<?php _e('Register'); ?>" />
                </p>
            </form>
        </div>
    <?php } ?>

    <form class="diglabs-form diglabs-inline" name="stripe_payment_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <h2>Stripe API Keys</h2>
		<p class="info">Log into <a href="http://stripe.com" target="_blank">stripe.com</a> to access your keys and determine the 3-letter ISO code for currency.</p>
        <h4>Test Keys</h4>
		<p>These keys are configured to a test account and <strong>will not</strong> result in actual credit card charges.</p>
        <p>
            <label for="test_secret_key">Secret Key:</label>
            <input type="text" name="test_secret_key" value="<?php echo $settings->testSecretKey; ?>" />
        </p>
        <p>
            <label for="test_public_key">Publishable Key:</label>
            <input type="text" name="test_public_key" value="<?php echo $settings->testPublicKey; ?>" />
        </p>
		<h4>Live Keys</h4>
		<p>These keys are configured to a real account and <strong>will</strong> result in actual credit card charges.</p>
        <p>
            <label for="live_secret_key">Secret Key:</label>
            <input type="text" name="live_secret_key" value="<?php echo $settings->liveSecretKey; ?>" />
        </p>
        <p>
            <label for="live_public_key">Publishable Key:</label>
            <input type="text" name="live_public_key" value="<?php echo $settings->livePublicKey; ?>" />
        </p>
		<h2>Payment Notification - Web Hook URL</h2>
		<p>The following URL defines the callback that Stripe uses payment notifications.</p>
        <p>
            <label for="webhook_url">Webhook URL:</label>
            <code><?php echo home_url(null, null, 'https').'/'; ?></code>
            <input type="text" name="webhook_url" value="<?php echo $settings->webHookUrl; ?>" />
            <?php
            $page = get_page_by_title( $settings->webHookUrl );
            $permalink = get_permalink($page->ID);
            ?>
        </p>
		<div class="stripe_payments_info">
			<p>After setting this here, configure <a href='http://stripe.com' target='_blank'>Stripe</a> to use this web hook.</p>
			<p>Expected stripe.com URL (click to test): <a target="_blank" href="<?php echo $settings->getWebHookUrl(); ?>"><?php echo $settings->getWebHookUrl(); ?></a></p>
		</div>
		<h2>Tax Configuration</h2>
        <div class="stripe_payments_info">
            <p>
                Select a country / region, enter a tax rate (%) and click the 'Add Tax' button.<br />
                <strong>When list is complete, click 'Save Options' to save all the changes.</strong>
            </p>
        </div>
        <div class="diglabs-tax-rates">
            <div class="new">
                <select style='width: 200px !important;' id='country' class='country' name='country'></select>
                <select style='width: 200px !important;' id='state' class='state' name='state'></select>
                <input style='width: 75px !important;' type="number" id="taxRate" value="0.0" />
                <a class='diglabs-btn-orange' href='#' id='addTax'>Add Tax</a>
            </div>
            <div class="current">
                <ul class='taxdata'><!--dynamically created content--></ul>
            </div>
        </div>

		<h2>Other</h2>
		<p>The following provide other options used by this plugin.</p>
        <p>
            <label for="is_live">Use Live Keys?:</label>
            <input type="checkbox" name="is_live_keys" <?php if($settings->isLive){echo 'checked=checked';} ?> />
            <span>Global setting. Leave unchecked for testing. Check when you are ready to <strong>go live</strong>. Individual forms can be set to use the test keys by the <code>test=true</code> attribute.</span>
        </p>
        <p>
            <label for="is_live">Auto Plans?:</label>
            <input type="checkbox" name="is_auto_plan" <?php if($settings->isAutoPlan){echo 'checked=checked';} ?> />
            <span>Automatically create Stripe plans for when the plan is not found.</span>
        </p>
        <p>
            <label for="currency_symbol">Stripe Account Country:</label>
            <select name="stripe_country">
                <?php
                $countries = $country_helper->countries();
                $country_code = $settings->getCountryIso();
                $my_country = $country_helper->country( $country_code );
                foreach($countries as $iso => $country)
                {
                    $name = $country->country_name;
                    if(in_array( $iso, $supported_countries ) )
                    {
                        $name .= ' *';
                    }
                    $selected = '';
                    if( $iso == $country_code )
                    {
                        $selected = 'selected="selected"';
                    }
                    echo "<option value='$iso' $selected>$name</option>";
                }
                ?>
            </select>
            <span>currency: <?php echo $my_country->currency_iso_3char; ?></span>
        </p>
        <div class="stripe_payments_info">
            <p>
                The <code>Stripe Account Country</code> selection set the currency being charged.
                This should be the country that was used to create the Stripe account. This selection
                sets the currency used during charges.
            </p>
        </div>

        <p class="submit">
			<input class="diglabs-btn-blue" type="submit" name="save" value="<?php _e('Save Options'); ?>" />
		</p>
	</form>
    <p>
        <a href="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&reg=true">show registration</a>
    </p>
</div>