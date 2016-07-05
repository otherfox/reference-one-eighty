<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Receipt' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Receipt extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_receipt";

        public function description()
        {
            return 'Inserts the HTML used to generate the receipt displayed on the web page after a successful payment.';
        }

        public function options()
        {
            return array();
        }

        public function tag()
        {
            return parent::ShortCodeWithPrefix( $this->tag );
        }

        public function output( $atts, $content = null )
        {
            extract( shortcode_atts( array(), $atts ) );
            return $this->render_receipt( $content );
        }

        private function render_receipt( $content = null )
        {
            if( $content == null || strlen( trim( $content ) ) == 0 )
            {
                $content = $this->render_default();
            }
            return <<<HTML
<div class="stripe-payment-receipt">$content</div>
HTML;
        }

        private function render_default()
        {
            return <<<HTML
<p><strong>Thanks for the payment!</strong></p>
<p><ul>
	<li><span>Name:</span><code>{fname} {lname}</code></li>
	<li><span>Amount:</span><code>{amount_str}</code></li>
	<li><span>Initial Fee:</span><code>{init_fee_str}</code></li>
	<li><span>Discount:</span><code>{discount_str}</code></li>
	<li><span>Tax:</span><code>{tax_str}</code></li>
	<li class='total'><span>Total:</span><code>{total_str}</code></li>
</ul></p>
<p><strong>{total_str} is making its way to our bank account.</strong></p>
<p>A receipt has been sent to <strong>{email}</strong>.</p>
<p>Transaction ID: {id}</p>
HTML;
        }
    }
}
