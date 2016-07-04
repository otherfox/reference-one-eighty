<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Initial' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Initial extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_plan_initial";

        public function description()
        {
            return 'Add an initial fee (e.g. setup fee) to the first of recurring or to the only payment of non-recurring. The initial fee is added as a line item in the invoice.';
        }

        public function options()
        {
            return array(
                'fee'          => array(
                    'type'          => 'float',
                    'description'   => 'The amount of the initial fee.',
                    'is_required'   => true,
                    'example'       => 'fee="24.95"'
                ),
                'description'   => array(
                    'type'          => 'string',
                    'description'   => 'A description that is used in the invoice.',
                    'is_required'   => true,
                    'example'       => 'description="Registration and setup fee"'
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
                                         "fee"         => null,
                                         "description" => null,
                                         "label"       => "Setup Fee"
                                     ), $atts ) );

            if( is_null( $fee ) )
            {
                return "No <code>fee</code> attribute specified.";
            }
            $fee_cents = intval( floatval( $fee ) * 100 );
            if( $fee_cents < 50 )
            {
                return "Minimum fee is 0.50.";
            }
/*
<div class="stripe-payment-form-row">
<label>$currency</label>
<input type="text" size="20" name="cardAmount" $disabled class="cardAmount amountShown disabled required" />
<span class="stripe-payment-form-error"></span>
</div>

 */

            $html = <<<HTML
<div class="stripe-payment-form-row">
<label>$label</label>
<input type="hidden" name="init_fee" value="$fee_cents" />
<input type="text" size="20" name="feeShown" disabled="disabled" class="disabled" value="$fee" />
</div>
HTML;
            if( !is_null( $description ) )
            {
                $html .= "<input type='hidden' name='init_description' value='$description' />";
            }
            return $html;
        }
    }
}
