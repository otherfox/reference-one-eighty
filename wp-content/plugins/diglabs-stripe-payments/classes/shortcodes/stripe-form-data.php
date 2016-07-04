<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Data' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Data extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_data";

        public function description()
        {
            return 'Provides WordPress shortcode access to the POST data from a redirected receipt (e.g. using <code>&#91;stripe_form_redirect&#93;</code>). During development of the page use the following to emit all the available name/value pair data: <code>&#91;stripe_form_data name="dump_all"&#93;</code>';
        }

        public function options()
        {
            return array(
                'name'          => array(
                    'type'          => 'string',
                    'description'   => 'The name of the data to add to the form.',
                    'is_required'   => false,
                    'example'       => 'name="amount"'
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
                                         "name" => null
                                     ), $atts ) );

            if( $name == null )
            {
                return "Expecting the following format <code>[stripe_form_data id='ABC']</code>.";
            }
            if( $name == 'dump_all' )
            {
                $result = "<h1>Available Variables</h1>";
                $result .= "<table class='stripe-payment-table'>";
                foreach( $_REQUEST as $name => $value )
                {
                    $result .= "<tr><td>" . $name . "</td><td>" . $value . "</td></tr>";
                }
                $result .= "</table>";
                return $result;
            }
            return $_REQUEST[ $name ];
        }
    }
}
