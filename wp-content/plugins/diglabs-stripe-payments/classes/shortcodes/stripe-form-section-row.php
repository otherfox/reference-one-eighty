<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Row' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Section_Row extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_section_row";

        public function description()
        {
            return 'Creates a row for a custom section in the payment form. A section can have multiple rows. This attributes below can be valid HTML. <em>Be aware of quote levels.</em>';
        }

        public function options()
        {
            return array(
                'label'          => array(
                    'type'          => 'html',
                    'description'   => 'The label for the form element.',
                    'is_required'   => true,
                    'example'       => 'label="T-Shirt Size"'
                ),
                'input'          => array(
                    'type'          => 'html',
                    'description'   => 'The input element(s) for the form element.',
                    'is_required'   => true,
                    'example'       => 'input="&lt;select name=\'tshirt\'&gt;&lt;option value=\'S\'&gt;small&lt;/option&gt;&lt;option value=\'M\'&gt;medium&lt;/option&gt;&lt;option value=\'L\'&gt;large&lt;/option&gt;&lt;/select&gt;"'
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
                                         "label" => '',
                                         "input" => ''
                                     ), $atts ) );

            return $this->render( $label, $input );
        }

        private function render( $label, $input )
        {
            return <<<HTML
<div class='stripe-payment-form-row'>
<label>$label</label>
$input
<span class='stripe-payment-form-error'></span>
</div>
HTML;
        }
    }
}
