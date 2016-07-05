<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Amount' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Amount extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_amount";

        public function description()
        {
            return "Creates a section in the form to collect a single payment (non-recurring).";
        }

        public function options()
        {
            return array(
                'amount'          => array(
                    'type'          => 'float',
                    'description'   => 'The amount to be collected. If provided, the amount field is disabled and the amount cannot be changed. If not provides, the amount field becomes free entry and the user can enter any amount.',
                    'is_required'   => false,
                    'example'       => 'amount="49.95"'
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
                                         "amount" => null
                                     ), $atts ) );

            if( $amount == null && isset( $_REQUEST[ 'amount' ] ) )
            {
                $amount = $_REQUEST[ 'amount' ];
            }
            return $this->render_amount_info( $amount );
        }

        public function render_amount_info( $amount = null )
        {
            $disabled       = $amount == null ? '' : 'disabled="disabled"';
            $value          = $amount == null ? '' : number_format( $amount, 2 );
            $value_in_cents = $value * 100;

            $settings    = new DigLabs_Stripe_Helpers_Settings();
            $country_iso = $settings->getCountryIso();

            $country_helper = new DigLabs_Stripe_I18N_Country_Helper();
            $country        = $country_helper->country( $country_iso );
            $currency       = $country->currency_name;

            return <<<HTML
<h3 class="stripe-payment-form-section">Amount</h3>
<div class="stripe-payment-form-row">
<input type="hidden" class="amount" size="20" name="amount" value="$value_in_cents" />
<label>$currency</label>
<input type="text" size="20" $disabled class="disabled amountShown required" value="$value" />
<span class="stripe-payment-form-error"></span>
</div>
HTML;
        }
    }
}
