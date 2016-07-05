<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Checkout_Button' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Checkout_Button extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_checkout_button";

        public function description()
        {
            return "Generate a button using <a href='https://stripe.com/docs/checkout'>Stripe Checkout</a> features.";
        }

        public function options()
        {
            return array(
                'test'          => array(
                    'type'          => 'bool',
                    'description'   => 'Puts the form in <em>testing</em> mode. Forms in this mode use the Stripe API test keys and do not make <em>real</em> charges. The test credit card <code>4242-4242-4242-4242</code> can be used to make transactions.',
                    'is_required'   => false,
                    'example'       => 'test=true'
                ),
                'email'          => array(
                    'type'          => 'bool',
                    'description'   => 'Renders an additional input field to collect the user\'s email address.',
                    'is_required'   => false,
                    'example'       => 'email=true'
                ),
                'label'          => array(
                    'type'          => 'string',
                    'description'   => 'Sets the trigger button\'s text.',
                    'is_required'   => true,
                    'example'       => 'label="Pay Me!"'
                ),
                'name'          => array(
                    'type'          => 'string',
                    'description'   => 'Sets the title of the payment form.',
                    'is_required'   => true,
                    'example'       => 'name="Test Charge"'
                ),
                'description'   => array(
                    'type'          => 'string',
                    'description'   => 'Sets the description of the payment form.',
                    'is_required'   => true,
                    'example'       => 'description="This is a test!"'
                ),
                'address'       => array(
                    'type'          => 'bool',
                    'description'   => 'Configures the payment for to collect address information.',
                    'is_required'   => false,
                    'example'       => 'address=true'
                ),
                'amount'       => array(
                    'type'          => 'float',
                    'description'   => 'Sets the amount shown on the payment form. In the case of a single payment it sets the amount to be charged.',
                    'is_required'   => true,
                    'example'       => 'amount="25.00"'
                ),
                'plan'       => array(
                    'type'          => 'string',
                    'description'   => 'The ID of the recurring plan that is being created.',
                    'is_required'   => false,
                    'example'       => 'plan="test_9_99"'
                ),
                'img'       => array(
                    'type'          => 'url',
                    'description'   => 'The URL to the image displayed on the payment form.',
                    'is_required'   => false,
                    'example'       => 'img="http://yoursite.com/img/marketplace.png"'
                ),
                'url'       => array(
                    'type'          => 'url',
                    'description'   => 'The URL the user is redirected to upon successful payment.',
                    'is_required'   => false,
                    'example'       => 'url="http://yoursite.com/receipt"'
                ),
                'name="value"'      => array(
                    'type'          => 'string',
                    'description'   => 'Name / value pairs that are posted back to the server.',
                    'is_required'   => false,
                    'example'       => 'pid="1234"'
                )
            );
        }

        public function tag()
        {
            return parent::ShortCodeWithPrefix( $this->tag );
        }

        public function output( $atts, $content = null )
        {
            $defaults = array(
                "test"        => false,
                "email"       => false,
                "amount"      => 10.00,
                "name"        => "My Marketplace",
                "description" => "1-year subscription",
                "label"       => "Pay by Card",
                "address"     => false,
                "img"         => "/128x128.png"
            );
            $arr      = wp_parse_args( $atts, $defaults );

            $result = $this->render_connect( $arr );
            return $result;
        }

        private function render_connect( $atts )
        {
            extract( $atts, EXTR_SKIP );

            $base = DigLabs_Stripe_Payments::GlobalInstance();
            wp_enqueue_script( 'stripe_payment_plugin', $base->urls->plugin() . '/js/connect.js', array( 'jquery' ), '1.5.19', true );

            $settings = new DigLabs_Stripe_Helpers_Settings();
            $pubkey   = null;
            if( $test )
            {
                Stripe::setApiKey( $settings->testSecretKey );
                $pubkey = $settings->testPublicKey;
            }
            else
            {
                Stripe::setApiKey( $settings->getSecretKey() );
                $pubkey = $settings->getPublicKey();
            }

            // Extract values from array
            extract( $atts );
            $atts[ 'pubkey' ] = $pubkey;

            // Apply conversions
            $amount           = intval( floatval( $amount ) * 100 );
            $atts[ 'amount' ] = $amount;
            $re_map = array(
                'name'          => 'stripe_name',
                'description'   => 'stripe_description',
                'address'       => 'stripe_address',
                'label'         => 'stripe_label',
                'img'           => 'stripe_img'
            );
            foreach( $re_map as $old_name=>$new_name )
            {
                $atts[ $new_name ] = $atts[ $old_name ];
                unset( $atts[ $old_name ] );
            }
            foreach( $atts as $key => $val )
            {
                if( is_bool( $val ) )
                {
                    $atts[ $key ] = $val ? "true" : "false";
                }
            }

            // Render the element
            $html = '<div class="stripe-form-wrap">';
            $html .= '<script type="text/javascript">var stripe_blog_url="' . get_site_url() . '";</script>';
            if( $email )
            {
                $html .= "<input type='text' name='email' placeholder='email...' /><br />";
            }
            $html .= '<script src="https://checkout.stripe.com/v2/checkout.js"></script>';
            $html .= '<button class="stripe-button-el"><span>' . $label . '</span></button>';
            unset( $atts[ 'email' ] );
            foreach( $atts as $key => $val )
            {
                $html .= "<input type='hidden' name='$key' value='$val' />";
            }
            $html .= '</div>';

            return $html;
        }
    }
}
