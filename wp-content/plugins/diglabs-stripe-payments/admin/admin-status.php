<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'DigLabs_Status' ) )
{
    class DigLabs_Status
    {
        public function show()
        {
            ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>
                <h2>System Status</h2>
                <?php $this->render_system_status(); ?>
            </div>
        <?php
        }

        private function render_system_status()
        {
            $settings = new DigLabs_Stripe_Helpers_Settings();
            ?>

            <div class="diglabs-message">
                <a style="float: right;" href="#" class="download diglabs-btn-green">Download System Report File</a>
                <h4>Please include this information when requesting support.</h4>
            </div>

            <table class="widefat" cellspacing="0">

            <?php

            // Let other plugins contribute to this page
            //
            do_action( 'diglabs_status_page' );

            ?>

            <thead>
            <tr>
                <th colspan="2">Stripe Payments Plugin Settings</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Is Live</td>
                <td><?php echo $settings->isLive ? 'true' : 'false'; ?></td>
            </tr>
            <tr>
                <td>Live Public Key</td>
                <td><?php echo $settings->livePublicKey; ?></td>
            </tr>
            <tr>
                <td>Live Secret Key</td>
                <td><?php echo substr( $settings->liveSecretKey, 0, 3 ) . '********'; ?></td>
            </tr>
            <tr>
                <td>Test Public Key</td>
                <td><?php echo $settings->testPublicKey; ?></td>
            </tr>
            <tr>
                <td>Test Secret Key</td>
                <td><?php echo substr( $settings->testSecretKey, 0, 3 ) . '********'; ?></td>
            </tr>
            <tr>
                <td>Currency Symbol</td>
                <td><?php echo $settings->currencySymbol; ?></td>
            </tr>
            <tr>
                <td>Webhook URL</td>
                <td><?php echo $settings->getWebHookUrl(); ?></td>
            </tr>
            <tr>
                <td>Tax Data</td>
                <td>
                    <?php
                    if( !is_null( $settings->taxData ) && is_array( $settings->taxData ) )
                    {
                        foreach( $settings->taxData as $country => $states )
                        {
                            foreach( $states as $state => $rate )
                            {
                                echo $state . ', ' . $country . ' @' . number_format( $rate, 2 ) . "%<br />";
                            }
                        }
                    }
                    else
                    {
                        echo '-';
                    }
                    ?>
                </td>
            </tr>
            </tbody>

            <thead>
            <tr>
                <th colspan="2">Environment</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Home URL</td>
                <td><?php echo home_url(); ?></td>
            </tr>
            <tr>
                <td>Site URL</td>
                <td><?php echo site_url(); ?></td>
            </tr>
            <tr>
                <td>WP Version</td>
                <td><?php if( is_multisite() )
                        echo 'WPMU';
                    else
                    {
                        echo 'WP';
                    } ?> <?php echo bloginfo( 'version' ); ?></td>
            </tr>
            <tr>
                <td>Web Server Info</td>
                <td><?php echo esc_html( $_SERVER[ 'SERVER_SOFTWARE' ] ); ?></td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><?php if( function_exists( 'phpversion' ) )
                        echo esc_html( phpversion() ); ?></td>
            </tr>
            <tr>
                <td>MySQL Version</td>
                <td><?php if( function_exists( 'mysql_get_server_info' ) )
                        echo esc_html( mysql_get_server_info() ); ?></td>
            </tr>
            <tr>
                <td>WP Memory Limit</td>
                <td><?php echo size_format( $this->convert_letter( WP_MEMORY_LIMIT ) ); ?></td>
            </tr>
            <tr>
                <td>WP Debug Mode</td>
                <td><?php if( defined( 'WP_DEBUG' ) && WP_DEBUG )
                        echo 'Yes';
                    else echo 'No'; ?></td>
            </tr>
            <tr>
                <td>WP Max Upload Size</td>
                <td><?php echo size_format( wp_max_upload_size() ); ?></td>
            </tr>
            <tr>
                <td>PHP Post Max Size</td>
                <td><?php if( function_exists( 'ini_get' ) )
                        echo size_format( $this->convert_letter( ini_get( 'post_max_size' ) ) ); ?></td>
            </tr>
            <tr>
                <td>PHP Time Limit</td>
                <td><?php if( function_exists( 'ini_get' ) )
                        echo ini_get( 'max_execution_time' ); ?></td>
            </tr>
            <?php

            $posting = array();

            // fsockopen/cURL
            //
            $posting[ 'fsockopen_curl' ][ 'name' ] = 'fsockopen/cURL';
            if( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) )
            {
                if( function_exists( 'fsockopen' ) && function_exists( 'curl_init' ) )
                {
                    $posting[ 'fsockopen_curl' ][ 'note' ] = 'Your server has fsockopen and cURL enabled.';
                }
                elseif( function_exists( 'fsockopen' ) )
                {
                    $posting[ 'fsockopen_curl' ][ 'note' ] = 'Your server has fsockopen enabled, cURL is disabled.';
                }
                else
                {
                    $posting[ 'fsockopen_curl' ][ 'note' ] = 'Your server has cURL enabled, fsockopen is disabled.';
                }
                $posting[ 'fsockopen_curl' ][ 'success' ] = true;
            }
            else
            {
                $posting[ 'fsockopen_curl' ][ 'note' ]    = 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.';
                $posting[ 'fsockopen_curl' ][ 'success' ] = false;
            }

            // SOAP
            //
            $posting[ 'soap_client' ][ 'name' ] = 'SOAP Client';
            if( class_exists( 'SoapClient' ) )
            {
                $posting[ 'soap_client' ][ 'note' ]    = 'Your server has the SOAP Client class enabled.';
                $posting[ 'soap_client' ][ 'success' ] = true;
            }
            else
            {
                $posting[ 'soap_client' ][ 'note' ]    = sprintf( 'Your server does not have the <a href="%s">SOAP Client</a> class enabled - some gateway plugins which use SOAP may not work as expected.', 'http://php.net/manual/en/class.soapclient.php' );
                $posting[ 'soap_client' ][ 'success' ] = false;
            }

            // WP Remote Post Check
            //
            $posting[ 'wp_remote_post' ][ 'name' ] = 'WP Remote Post';
            $request[ 'cmd' ] = '_notify-validate';
            $params = array(
                'sslverify'  => false,
                'timeout'    => 60,
                'user-agent' => 'DigLabs_StripePlugin/',
                'body'       => $request
            );
            $response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );
            if( !is_wp_error( $response ) && $response[ 'response' ][ 'code' ] >= 200 && $response[ 'response' ][ 'code' ] < 300 )
            {
                $posting[ 'wp_remote_post' ][ 'note' ]    = 'wp_remote_post() was successful - PayPal IPN is working.';
                $posting[ 'wp_remote_post' ][ 'success' ] = true;
            }
            elseif( is_wp_error( $response ) )
            {
                $posting[ 'wp_remote_post' ][ 'note' ]    = 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider. Error: ' . $response->get_error_message();
                $posting[ 'wp_remote_post' ][ 'success' ] = false;
            }
            else
            {
                $posting[ 'wp_remote_post' ][ 'note' ]    = 'wp_remote_post() failed. PayPal IPN may not work with your server.';
                $posting[ 'wp_remote_post' ][ 'success' ] = false;
            }

            foreach( $posting as $post )
            {
                ?>
                <tr>
                    <td><?php echo esc_html( $post[ 'name' ] ); ?></td>
                    <td>
                        <?php
                        echo $post[ 'success' ] ? 'true' : 'false';
                        echo ': ' . wp_kses_data( $post[ 'note' ] );
                        ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>


            <thead>
            <tr>
                <th colspan="2">Plugins</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $active_plugins = (array)get_option( 'active_plugins', array() );

            if( is_multisite() )
            {
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
            }

            $wc_plugins = array();
            foreach( $active_plugins as $plugin )
            {
                $plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
                $dirname        = dirname( $plugin );
                $version_string = '';

                if( !empty( $plugin_data[ 'Name' ] ) )
                {
                    echo '<tr>';
                    echo '<td>' . $plugin_data[ 'Name' ] . '</td>';
                    echo '<td>Author: ' . $plugin_data[ 'Author' ] . '<br />Version: ' . $plugin_data[ 'Version' ] . $version_string . '<br />Folder: ' . $dirname . '</td>';
                    echo '</tr>';
                }
            }

            if( sizeof( $wc_plugins ) == 0 )
                echo '-';
            else
                echo implode( ', <br/>', $wc_plugins );

            ?>
            </tbody>

            </table>

            <script type="text/javascript">

                jQuery.wc_strPad = function (i, l, s) {
                    var o = i.toString();
                    if (!s) {
                        s = '0';
                    }
                    while (o.length < l) {
                        o = o + s;
                    }
                    return o;
                };

                jQuery('a.download').click(function () {

                    var report = "";

                    jQuery('thead, tbody').each(function () {

                        $this = jQuery(this);

                        if ($this.is('thead')) {

                            report = report + "\n### " + jQuery.trim($this.text()) + " ###\n\n";

                        } else {

                            jQuery('tr', $this).each(function () {

                                $this = jQuery(this);

                                name = jQuery.wc_strPad(jQuery.trim($this.find('td:eq(0)').text()), 50, ' ');
                                value = jQuery.trim($this.find('td:eq(1)').text());

                                report = report + '' + name + value + "\n\n";
                            });

                        }
                    });

                    var blob = new Blob([report]);

                    jQuery(this).attr('href', window.URL.createObjectURL(blob));

                    return true;
                });

            </script>
        <?php
        }

        private function convert_letter( $size )
        {
            $letter = substr( $size, -1 );
            $return = substr( $size, 0, -1 );
            switch( strtoupper( $letter ) )
            {
                case 'P':
                    $return *= pow( 1024, 5 );
                    break;
                case 'T':
                    $return *= pow( 1024, 4 );
                    break;
                case 'G':
                    $return *= pow( 1024, 3 );
                    break;
                case 'M':
                    $return *= pow( 1024, 2 );
                    break;
                case 'K':
                    $return *= 1024;
                    break;
            }
            return $return;
        }
    }
}

$diglabs_status = new DigLabs_Status();
$diglabs_status->show();