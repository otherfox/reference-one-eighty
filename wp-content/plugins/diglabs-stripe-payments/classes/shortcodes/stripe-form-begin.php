<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Begin' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Begin extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_begin";

        public function description()
        {
            return "This shortcode begins a payment form. The <code>stripe_form_end</code> code ends the payment form. Any HTML in between is rendered as part of the form. There are a number of shortcodes that provide standard templates.";
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
                'description'   => array(
                    'type'          => 'string',
                    'description'   => 'Overrides the description sent to Stripe.com. If you want a custom description in your Stripe.com pages, this will allow you to set one. <em>Caution: This may break features (e.g. tax collection) that rely on the data that is normally embedded in the description field.</em>',
                    'is_required'   => false,
                    'example'       => 'description="A custom description."'
                )
            );
        }

        public function tag()
        {
            return parent::ShortCodeWithPrefix( $this->tag );
        }

        public function output( $atts, $content = null )
        {
            extract( shortcode_atts( array(
                                         "test"        => false,
                                         "taxexempt"   => false,
                                         "description" => null
                                     ), $atts ) );


            $this->include_scripts();


            $result = $this->render_form_open_tag( $test );
            if( !is_null( $description ) )
            {
                $result .= $this->render_description( $description );
            }
            if( $taxexempt )
            {
                $result .= "<input type='hidden' name='tax-exempt' value='1' />";
            }
            $ignore = array( "amount", "plan" );
            foreach( $_REQUEST as $name => $value )
            {
                if( !in_array( $name, $ignore ) )
                {
                    $result .= "<input type='hidden' name='$name' value='$value' />";
                }
            }

            return $result;
        }

        private function include_scripts()
        {
            $base = DigLabs_Stripe_Payments::GlobalInstance();
            wp_enqueue_script('stripe_payment_plugin', $base->urls->plugin() . '/js/stripe.js', array('jquery'), '1.5.19', true);
            wp_enqueue_script('stripe', 'https://js.stripe.com/v1/', array('jquery'), '1.5.19', true);
        }

        private function render_form_open_tag( $test = false )
        {
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
            $paymentUrl = $settings->getPaymentUrl();

            $blog_url = get_site_url();

            return <<<HTML
<div class="stripe-form-wrap">
<script type="text/javascript">var stripe_blog_url="$blog_url";</script>
<form action="$paymentUrl" method="post" class="stripe-payment-form">
<input class="pubkey" type="hidden" name="pubkey" value="$pubkey" />
HTML;
        }

        private function render_description( $description )
        {
            return <<<HTML
<input class="description" type="hidden" name="description" value="$description" />
HTML;
        }

    }
}
