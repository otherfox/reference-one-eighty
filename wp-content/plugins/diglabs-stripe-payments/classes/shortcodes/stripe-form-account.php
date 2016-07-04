<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'DigLabs_Stripe_Shortcodes_Stripe_Form_Account' ) )
{
    class DigLabs_Stripe_Shortcodes_Stripe_Form_Account extends DigLabs_Stripe_Shortcodes_Abstract_Base
    {
        private $tag = "stripe_form_account";

        public function description()
        {
            return "Adds standard login form inputs (username, password, confirm password) to the payment form.";
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
            return <<<HTML
<h3 class="stripe-payment-form-section">Login Information</h3>
<div class="stripe-payment-form-row">
<label for="uname">Username</label>
<input type="text" id="uname" name="uname" class="required" />
</div>
<div class="stripe-payment-form-row">
<label for="pword1">Password</label>
<input type="password" id="pword1" name="pword1" class="required" />
</div>
<div class="stripe-payment-form-row">
<label for="pword2">Password Confirmation</label>
<input type="password" id="pword2" name="pword2" class="required" />
<span class="error"></span>
</div>
HTML;
        }
    }
}
