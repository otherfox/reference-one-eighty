<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Amount' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Amount extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_standard_amount";

        public function description()
        {
            return 'Create a standard fixed amount payment form.';
        }

        public function options()
        {
            return array(
                'amount'          => array(
                    'type'          => 'float',
                    'description'   => 'Sets the amount to be paid. If no amount is specified, the form allows the user to enter any amount.',
                    'is_required'   => false,
                    'example'       => 'amount="24.99"'
                ),
                'short'          => array(
                    'type'          => 'bool',
                    'description'   => 'Collect the minimal data: <code>first name</code>, <code>last name</code>, <code>email</code>. <em>Caution: This level cannot be used when it is desired to collect taxes.</em>',
                    'is_required'   => false,
                    'example'       => 'short=true'
                ),
                'medium'   => array(
                    'type'          => 'string',
                    'description'   => 'Collect a moderate amount of data: <code>first name</code>, <code>last name</code>, <code>email</code>, <code>state/province/region</code>, <code>country</code>',
                    'is_required'   => false,
                    'example'       => 'medium=true'
                ),
                'long'   => array(
                    'type'          => 'string',
                    'description'   => 'Collects the most data: <code>first name</code>, <code>last name</code>, <code>email</code>, <code>address line 1</code>, <code>address line 2</code>, <code>city</code>, <code>state/province/region</code>, <code>country</code>, <code>telephone number</code>',
                    'is_required'   => false,
                    'example'       => 'long=true'
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
                                         "amount" => null,
                                         "short"  => false,
                                         "medium" => false,
                                         "state"    => 'AL',
                                         "country"  => 'US',
                                    ), $atts ) );

            $style = $this->get_billing_style( $short, $medium );

            if( $amount == null && isset( $_REQUEST[ 'amount' ] ) )
            {
                $amount = $_REQUEST[ 'amount' ];
            }
            return $this->render( $amount, $style, $state, $country );
        }

        public function render( $amount = null, $style = "long", $state, $country )
        {
            $stripe_form_amount = new DigLabs_Stripe_Shortcodes_Stripe_Form_Amount();
            $html               = $stripe_form_amount->render_amount_info( $amount );

            $billing_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Billing_Info();
            $html .= $billing_info->render_billing_info( $style, $state, $country );

            $payment_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Payment_Info();
            $html .= $payment_info->render();

            return $html;
        }
    }
}
