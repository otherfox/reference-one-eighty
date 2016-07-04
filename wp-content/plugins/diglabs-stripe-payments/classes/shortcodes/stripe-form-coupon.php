<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Coupon' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Coupon extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_coupon";

        public function description()
        {
            return "Creates a field to enter a coupon code. Note: Coupons are supported by Stripe only on recurring payment plans. The plugin adds support to single payments, by honoring the percent set on the coupon for single payments also.";
        }

        public function options()
        {
            return array(
                'code'          => array(
                    'type'          => 'string',
                    'description'   => 'Use this to provide a pre-entered code. Otherwise, do not set.',
                    'is_required'   => false,
                    'example'       => 'short=true'
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
                                         "code" => null
                                     ), $atts ) );

            if( !is_null( $code ) )
            {
                return <<<HTML
<input type='hidden' name='coupon' value='$code' />
HTML;
            }
            return <<<HTML
<div class="stripe-payment-form-row">
<label for="coupon">Coupon</label>
<input type="text" id="coupon" name="coupon" />
<span class="error"></span>
</div>
HTML;
        }
    }
}
