<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_No_Amount' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_No_Amount extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_no_amount";

        public function description()
        {
            return 'This short code generates a form that collects payment information but does not charge the card. This allows for future billing to occur.';
        }

        public function options()
        {
            return array(
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
                                         "short"  => false,
                                         "medium" => false,
                                         "state"    => 'AL',
                                         "country"  => 'US',
                                     ), $atts ) );

            $style = $this->get_billing_style( $short, $medium );


            $billing_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Billing_Info();
            $html         = $billing_info->render_billing_info( $style, $state, $country );

            $payment_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Payment_Info();
            $html .= $payment_info->render();

            return $html;
        }
    }
}
