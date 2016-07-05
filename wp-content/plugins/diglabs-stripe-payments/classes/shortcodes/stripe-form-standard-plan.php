<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Plan' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Plan extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_standard_plan";

        public function description()
        {
            return 'Create a standard recurring payment form. <em>You must first create a recurring payment plan in your Stripe admin panel.</em>';
        }

        public function options()
        {
            return array(
                'plan'          => array(
                    'type'          => 'string',
                    'description'   => 'The ID of a plan that exists in your Stripe account. If this attribute is not provided, the payment form will be generated as a fixed amount with the attribute not specified.',
                    'is_required'   => true,
                    'example'       => 'plan="monthly_10"'
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
                                         "plan"   => null,
                                         "short"  => false,
                                         "medium" => false,
                                         "state"    => 'AL',
                                         "country"  => 'US',
                                     ), $atts ) );

            if( $plan == null && $_REQUEST[ 'plan' ] )
            {
                $plan = $_REQUEST[ 'plan' ];
            }

            $style = $this->get_billing_style( $short, $medium );

            // If no plan is specified, fallback to an open ended amount form
            //
            if( $plan == null )
            {
                $standard_plan = new DigLabs_Stripe_Shortcodes_Stripe_Form_Standard_Amount();
                return $standard_plan->render( null, $style, $state, $country );
            }
            return $this->render( $plan, $style, $state, $country );
        }

        private function render( $plan = null, $style = "long", $state, $country )
        {
            $stripe_plan_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Plan_Info();
            $html             = $stripe_plan_info->render_plan_info( $plan );

            $billing_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Billing_Info();
            $html .= $billing_info->render_billing_info( $style, $state, $country );

            $payment_info = new DigLabs_Stripe_Shortcodes_Stripe_Form_Payment_Info();
            $html .= $payment_info->render();

            return $html;
        }
    }
}
