<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Redirect' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Redirect extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_redirect";

        public function description()
        {
            return 'Overrides the web receipt by redirecting (HTTP POST) to another page with the all the payment information included as POST variables. This allows custom receipt pages.';
        }

        public function options()
        {
            return array(
                'url'          => array(
                    'type'          => 'url',
                    'description'   => 'The redirect URL upon successful payments.',
                    'is_required'   => true,
                    'example'       => 'url="http://yoursite.com/receipt/"'
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
                                         "url" => null
                                     ), $atts ) );

            if( !is_null( $url ) )
            {
                return "<input type='hidden' name='url' class='url' value='$url' />";
            }
            return "stripe_form_redirect requires a url attribute";
        }
    }
}
