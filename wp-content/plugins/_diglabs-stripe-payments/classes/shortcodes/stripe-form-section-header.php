<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Header' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Header extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_section_header";

        public function description()
        {
            return 'Creates a header for a custom section in the payment form.';
        }

        public function options()
        {
            return array(
                'title'          => array(
                    'type'          => 'string',
                    'description'   => 'The title of the new payment form section.',
                    'is_required'   => true,
                    'example'       => 'title="Favorite Color"'
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
                                         "title" => null
                                     ), $atts ) );

            if( $title )
            {
                return <<<HTML
<h3 class='stripe-payment-form-section'>$title</h3>
HTML;
            }
            else
            {
                return "no title";
            }
        }
    }
}
